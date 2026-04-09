<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class SchoolHomeController extends Controller
{
    /**
     * Display the public school homepage.
     */
    public function index()
    {
        /** @var Tenant $tenant */
        $tenant = tenant();

        // Pull school meta from tenants table custom columns
        $schoolName        = $tenant->school_name        ?? $tenant->school_name_hi ?? config('app.name');
        $established       = $tenant->established_year   ?? null;
        $board             = $tenant->board_affiliation   ?? 'CBSE';
        $address           = $tenant->address            ?? null;
        $phone             = $tenant->phone              ?? null;
        $email             = $tenant->email              ?? null;
        $principalPhone    = $tenant->principal_phone    ?? null;

        // Live counts from tenant DB
        try {
            $totalStudents = \App\Models\TenantUser::where('role', 'student')->count();
            $totalStaff    = \App\Models\TenantUser::where('role', '!=', 'student')->count();
        } catch (\Throwable $e) {
            Log::error('SchoolHome: count query failed — ' . $e->getMessage());
            $totalStudents = null;
            $totalStaff    = null;
        }

        // Years of excellence
        $yearsOfExcellence = $established
            ? (now()->year - (int) $established)
            : null;

        return view('tenant.home.index', compact(
            'schoolName',
            'established',
            'board',
            'address',
            'phone',
            'email',
            'principalPhone',
            'totalStudents',
            'totalStaff',
            'yearsOfExcellence',
        ));
    }

    /**
     * Handle the public contact form submission.
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // TODO: store in DB or send email notification to school admin
        // \App\Models\ContactEnquiry::create($validated);

        return redirect()
            ->route('tenant.home')
            ->with('success', 'Thank you! Your message has been sent. We will get back to you shortly.');
    }
}