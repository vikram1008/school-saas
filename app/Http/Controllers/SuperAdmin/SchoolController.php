<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = Tenant::with('domains')->latest()->paginate(15);
        $activeCount = Tenant::where('is_active', true)->count();

        // Pull student counts for the current page of schools
        $schoolStudents = [];
        foreach ($schools as $school) {
            try {
                tenancy()->initialize($school);
                $schoolStudents[$school->id] = DB::connection('tenant')
                    ->table('users')
                    ->where('role', 'student')
                    ->where('is_active', true)
                    ->count();
                tenancy()->end();
            } catch (\Exception) {
                tenancy()->end();
                $schoolStudents[$school->id] = 0;
            }
        }

        return view('superadmin.schools.index', compact('schools', 'activeCount', 'schoolStudents'));
    }

    public function create()
    {
        return view('superadmin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Identity
            'school_name' => ['required', 'string', 'max:255'],
            'school_name_hi' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'tagline' => ['nullable', 'string', 'max:255'],
            // Contact
            'email' => ['required', 'email', 'unique:tenants,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:200'],
            // Address
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            // Academic
            'board_affiliation' => ['nullable', 'string', 'max:100'],
            'school_code' => ['nullable', 'string', 'max:50'],
            'udise_code' => ['nullable', 'string', 'max:20'],
            // Billing
            'per_student_rate' => ['required', 'integer', 'in:10,20,30,40,50'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            // Admin credentials
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8'],
            // Subdomain
            'subdomain' => [
                'required', 'string', 'min:3', 'max:50',
                'regex:/^[a-z0-9\-]+$/',
                'unique:domains,domain',
            ],
        ]);

        // Generate a unique tenant ID from the school name slug
        $tenantId = Str::slug($validated['school_name']);
        $originalId = $tenantId;
        $counter = 1;
        while (Tenant::find($tenantId)) {
            $tenantId = $originalId.'-'.$counter++;
        }

        // Handle logo upload before creating the tenant
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('school/logo', 'public');
        }

        // Step 1: Create tenant in central DB — single source of truth
        $tenant = Tenant::create([
            'id' => $tenantId,
            'school_name' => $validated['school_name'],
            'school_name_hi' => $validated['school_name_hi'] ?? null,
            'logo' => $logoPath,
            'tagline' => $validated['tagline'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'phone_alt' => $validated['phone_alt'] ?? null,
            'website' => $validated['website'] ?? null,
            'address_line1' => $validated['address_line1'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'board_affiliation' => $validated['board_affiliation'] ?? null,
            'school_code' => $validated['school_code'] ?? null,
            'udise_code' => $validated['udise_code'] ?? null,
            'per_student_rate' => $validated['per_student_rate'],
            'billing_cycle' => $validated['billing_cycle'],
            'subscription_status' => 'active',
            'provisioned_at' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Step 2: Attach subdomain
        $subdomain = $validated['subdomain'].'.'.config('tenancy.central_domains')[0];
        $tenant->domains()->create(['domain' => $subdomain]);

        // Step 3: Seed the school admin user inside the tenant DB
        try {
            tenancy()->initialize($tenant);

            $userId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'school_admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('tenant')->table('staff_profiles')->insert([
                'user_id' => $userId,
                'employee_code' => 'EMP-'.strtoupper(Str::random(6)),
                'first_name' => Str::before($validated['admin_name'], ' '),
                'last_name' => Str::after($validated['admin_name'], ' ') ?: 'Admin',
                'designation' => 'School Administrator',
                'department' => 'Administration',
                'employment_type' => 'full_time',
                'status' => 'active',
                'joining_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } finally {
            tenancy()->end();
        }

        // Step 4: Create initial subscription in central DB
        $subscription = app(SubscriptionService::class)->createInitialSubscription($tenant);

        return redirect()
            ->route('superadmin.schools.show', $tenant)
            ->with('success', "School \"{$validated['school_name']}\" provisioned! First billing cycle: {$subscription->period_start->format('d M Y')} → {$subscription->period_end->format('d M Y')}.");
    }

    public function show(Tenant $school)
    {
        $school->load('domains', 'latestSubscription');

        // Pull live counts from the tenant DB
        $stats = ['students' => 0, 'staff' => 0, 'parents' => 0, 'classes' => 0];
        try {
            tenancy()->initialize($school);
            $stats['students'] = DB::connection('tenant')->table('users')->where('role', 'student')->count();
            $stats['staff'] = DB::connection('tenant')->table('users')->whereIn('role', ['teacher', 'accountant', 'librarian', 'school_admin'])->count();
            $stats['parents'] = DB::connection('tenant')->table('users')->where('role', 'parent')->count();
            $stats['classes'] = DB::connection('tenant')->table('classes')->whereNull('deleted_at')->count();
        } catch (\Throwable) {
            // Tenant DB may not be ready yet
        } finally {
            tenancy()->end();
        }

        return view('superadmin.schools.show', compact('school', 'stats'));
    }

    public function destroy(Tenant $tenant)
    {
        // Remove logo from storage before deleting the tenant
        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $tenant->delete();

        return redirect()
            ->route('superadmin.schools.index')
            ->with('success', 'School and its database deleted successfully.');
    }

    public function edit(Tenant $school)
    {
        $school->load('domains');

        return view('superadmin.schools.edit', compact('school'));
    }

    public function update(Request $request, Tenant $school)
    {
        $validated = $request->validate([
            // Identity
            'school_name' => ['required', 'string', 'max:255'],
            'school_name_hi' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'tagline' => ['nullable', 'string', 'max:255'],
            // Contact
            'email' => ['required', 'email', 'unique:tenants,email,'.$school->id.',id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:200'],
            // Address
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            // Academic
            'board_affiliation' => ['nullable', 'string', 'max:100'],
            'school_code' => ['nullable', 'string', 'max:50'],
            'udise_code' => ['nullable', 'string', 'max:20'],
            // Billing (super admin only)
            'per_student_rate' => ['required', 'integer', 'in:10,20,30,40,50'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            'is_active' => ['required', 'boolean'],
        ]);

        // Handle logo upload / removal
        if ($request->boolean('remove_logo') && $school->logo) {
            Storage::disk('public')->delete($school->logo);
            $validated['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('school/logo', 'public');
        } else {
            // No new logo submitted — keep existing value
            unset($validated['logo']);
        }

        // Single update to the central tenants table
        $school->update($validated);

        return redirect()
            ->route('superadmin.schools.show', $school)
            ->with('success', 'School details updated successfully.');
    }
}
