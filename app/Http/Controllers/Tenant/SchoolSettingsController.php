<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingsController extends Controller
{
    public function edit()
    {
        // $settings is the Tenant model (single source of truth).
        // SchoolSettings::current() is a proxy for tenant() — same thing.
        $settings = tenant();

        return view('tenant.settings.school', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => ['required', 'string', 'max:200'],
            'school_name_hi' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:200'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico', 'max:512'],
            'principal_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'receipt_footer_note' => ['nullable', 'string', 'max:2000'],
            'principal_name' => ['nullable', 'string', 'max:200'],
        ]);

        /** @var Tenant $tenant */
        $tenant = tenant();

        // Only allow school-editable fields — billing/subscription fields are
        // intentionally excluded so school admins cannot change them.
        $data = $request->only(Tenant::SCHOOL_EDITABLE_FIELDS);
        unset($data['logo'], $data['favicon'], $data['principal_signature']);

        // ── Logo ──────────────────────────────────────────────────────────
        if ($request->boolean('remove_logo') && $tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $data['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = $request->file('logo')->store('school/logo', 'public');
        }

        // ── Favicon ───────────────────────────────────────────────────────
        if ($request->boolean('remove_favicon') && $tenant->favicon) {
            Storage::disk('public')->delete($tenant->favicon);
            $data['favicon'] = null;
        } elseif ($request->hasFile('favicon')) {
            if ($tenant->favicon) {
                Storage::disk('public')->delete($tenant->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('school/favicon', 'public');
        }

        // ── Principal Signature ───────────────────────────────────────────
        if ($request->boolean('remove_signature') && $tenant->principal_signature) {
            Storage::disk('public')->delete($tenant->principal_signature);
            $data['principal_signature'] = null;
        } elseif ($request->hasFile('principal_signature')) {
            if ($tenant->principal_signature) {
                Storage::disk('public')->delete($tenant->principal_signature);
            }
            $data['principal_signature'] = $request->file('principal_signature')->store('school/signatures', 'public');
        }

        // Save directly to central tenants table — no cross-DB sync needed.
        $tenant->update($data);

        return redirect()
            ->route('tenant.settings.school.edit')
            ->with('success', 'School settings updated successfully.');
    }
}
