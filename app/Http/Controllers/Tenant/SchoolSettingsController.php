<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingsController extends Controller
{
    public function edit()
    {
        // current() is safe here — this controller only runs inside
        // tenant middleware, so tenancy is already initialized.
        $settings = SchoolSettings::current();
        return view('tenant.settings.school', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'         => ['required', 'string', 'max:200'],
            'email'               => ['nullable', 'email', 'max:200'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'website'             => ['nullable', 'url', 'max:200'],
            'logo'                => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'favicon'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico', 'max:512'],
            'principal_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'primary_color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $settings = SchoolSettings::current();
        $data     = $request->except(['_token', '_method', 'logo', 'favicon', 'principal_signature']);

        // Logo
        if ($request->boolean('remove_logo') && $settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $data['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($settings->logo) Storage::disk('public')->delete($settings->logo);
            $data['logo'] = $request->file('logo')->store('school/logo', 'public');
        }

        // Favicon
        if ($request->boolean('remove_favicon') && $settings->favicon) {
            Storage::disk('public')->delete($settings->favicon);
            $data['favicon'] = null;
        } elseif ($request->hasFile('favicon')) {
            if ($settings->favicon) Storage::disk('public')->delete($settings->favicon);
            $data['favicon'] = $request->file('favicon')->store('school/favicon', 'public');
        }

        // Signature
        if ($request->boolean('remove_signature') && $settings->principal_signature) {
            Storage::disk('public')->delete($settings->principal_signature);
            $data['principal_signature'] = null;
        } elseif ($request->hasFile('principal_signature')) {
            if ($settings->principal_signature) Storage::disk('public')->delete($settings->principal_signature);
            $data['principal_signature'] = $request->file('principal_signature')->store('school/signatures', 'public');
        }

        $settings->update($data);

        return redirect()
            ->route('tenant.settings.school.edit')
            ->with('success', 'School settings updated successfully.');
    }
}