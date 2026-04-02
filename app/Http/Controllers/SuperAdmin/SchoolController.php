<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = Tenant::with('domains')->latest()->paginate(15);
        return view('superadmin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('superadmin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'per_student_rate' => ['required', 'integer', 'in:10,20,30,40,50'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8'],
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                'unique:domains,domain',
            ],
        ]);

        $tenantId = Str::slug($validated['school_name']);
        $originalId = $tenantId;
        $counter = 1;
        while (Tenant::find($tenantId)) {
            $tenantId = $originalId . '-' . $counter++;
        }

        // Step 1: Create tenant
        $tenant = Tenant::create([
            'id' => $tenantId,
            'school_name' => $validated['school_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'per_student_rate' => $validated['per_student_rate'],
            'billing_cycle' => $validated['billing_cycle'],
            'subscription_status' => 'active',
            'provisioned_at' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Step 2: Attach subdomain
        $subdomain = $validated['subdomain'] . '.' . config('tenancy.central_domains')[0];
        $tenant->domains()->create(['domain' => $subdomain]);

        // Step 3: Seed school admin in tenant DB
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
                'employee_code' => 'EMP-' . strtoupper(Str::random(6)),
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
        $subscription = app(SubscriptionService::class)
            ->createInitialSubscription($tenant);

        return redirect()
            ->route('superadmin.schools.index')
            ->with('success', "School \"{$validated['school_name']}\" provisioned! First billing cycle: {$subscription->period_start->format('d M Y')} → {$subscription->period_end->format('d M Y')}.");
    }

    public function show(Tenant $school)
    {
        $school->load('domains');
        return view('superadmin.schools.show', compact('school'));
    }

    public function destroy(Tenant $tenant)
    {
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
            'school_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email,' . $school->id . ',id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'per_student_rate' => ['required', 'integer', 'in:10,20,30,40,50'],
            'is_active' => ['required', 'boolean'],
        ]);

        $school->update($validated);

        return redirect()
            ->route('superadmin.schools.show', $school)
            ->with('success', 'School details updated successfully.');
    }
}