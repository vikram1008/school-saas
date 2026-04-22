<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolLogoController extends Controller
{
    /**
     * Show all schools with logo management.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->orderBy('school_name')->get();

        return view('superadmin.schools.logos', compact('tenants'));
    }

    /**
     * Upload / replace logo for a school.
     * Since the logo field now lives on the central Tenant model,
     * no tenant DB context is needed — just a direct Tenant update.
     */
    public function updateLogo(Request $request, Tenant $tenant)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $path = $request->file('logo')->store('school/logo', 'public');
        $tenant->update(['logo' => $path]);

        return redirect()
            ->back()
            ->with('success', "Logo updated for {$tenant->school_name}.");
    }

    /**
     * Remove the logo for a school.
     */
    public function removeLogo(Tenant $tenant)
    {
        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $tenant->update(['logo' => null]);
        }

        return redirect()->back()->with('success', 'Logo removed.');
    }
}
