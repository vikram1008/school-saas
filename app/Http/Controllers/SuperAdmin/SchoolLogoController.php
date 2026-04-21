<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSettings;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Facades\Tenancy;

class SchoolLogoController extends Controller
{
    /**
     * Show all schools with logo management.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->orderBy('id')->get();
        return view('superadmin.schools.logos', compact('tenants'));
    }

    /**
     * Upload / remove logo for a specific tenant from super admin panel.
     */
    public function updateLogo(Request $request, Tenant $tenant)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        // Run inside tenant context
        tenancy()->initialize($tenant);

        $settings = SchoolSettings::current();

        if ($settings->logo) {
            Storage::disk('public')->delete($settings->logo);
        }

        $path = $request->file('logo')->store('school/logo', 'public');
        $settings->update(['logo' => $path]);
        // SchoolSettings::clearCache();

        tenancy()->end();

        return redirect()
            ->back()
            ->with('success', "Logo updated for {$tenant->id}.");
    }

    public function removeLogo(Tenant $tenant)
    {
        tenancy()->initialize($tenant);

        $settings = SchoolSettings::current();
        if ($settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $settings->update(['logo' => null]);
            SchoolSettings::clearCache();
        }

        tenancy()->end();

        return redirect()->back()->with('success', 'Logo removed.');
    }
}