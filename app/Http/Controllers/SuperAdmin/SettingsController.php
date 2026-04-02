<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SaasSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SaasSettings::all()->keyBy('key');
        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'grace_warning_days'     => ['required', 'integer', 'min:1', 'max:30'],
            'grace_readonly_days'    => ['required', 'integer', 'min:1', 'max:60'],
            'suspension_days'        => ['required', 'integer', 'min:1', 'max:90'],
            'default_billing_cycle'  => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            'saas_name'              => ['required', 'string', 'max:100'],
            'support_email'          => ['required', 'email'],
        ]);

        // Logical validation — thresholds must be in order
        if ($request->grace_warning_days >= $request->grace_readonly_days) {
            return back()
                ->withInput()
                ->withErrors([
                    'grace_warning_days' => 'Warning period must be less than Read-Only period.'
                ]);
        }

        if ($request->grace_readonly_days >= $request->suspension_days) {
            return back()
                ->withInput()
                ->withErrors([
                    'grace_readonly_days' => 'Read-Only period must be less than Suspension threshold.'
                ]);
        }

        // Save each setting
        $keys = [
            'grace_warning_days',
            'grace_readonly_days',
            'suspension_days',
            'default_billing_cycle',
            'saas_name',
            'support_email',
        ];

        foreach ($keys as $key) {
            SaasSettings::set($key, $request->input($key));
        }

        return redirect()
            ->route('superadmin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}