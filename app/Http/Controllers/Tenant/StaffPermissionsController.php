<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\StaffPermission;
use App\Models\TenantUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPermissionsController extends Controller
{
    public function index(): View
    {
        $staffMembers = TenantUser::whereIn('role', ['teacher', 'accountant', 'librarian'])
            ->where('is_active', true)
            ->with('staffPermission')
            ->orderBy('name')
            ->get();

        return view('tenant.staff.permissions.index', compact('staffMembers'));
    }

    public function edit(int $userId): View
    {
        $staffUser = TenantUser::whereIn('role', ['teacher', 'accountant', 'librarian'])
            ->findOrFail($userId);

        $permissions = $staffUser->resolvedPermissions();
        $permissionMeta = StaffPermission::permissionMeta();

        // Group by 'group' label while preserving the permission key as the array key
        $grouped = collect($permissionMeta)
            ->map(fn ($meta, $key) => array_merge($meta, ['key' => $key]))
            ->groupBy('group');

        return view('tenant.staff.permissions.edit', compact('staffUser', 'permissions', 'grouped', 'permissionMeta'));
    }

    public function update(Request $request, int $userId): RedirectResponse
    {
        $staffUser = TenantUser::whereIn('role', ['teacher', 'accountant', 'librarian'])
            ->findOrFail($userId);

        $permissionKeys = array_keys(StaffPermission::permissionMeta());

        // Build data: checkbox present → true, absent → false
        $data = [];
        foreach ($permissionKeys as $key) {
            $data[$key] = $request->boolean($key);
        }

        StaffPermission::updateOrCreate(
            ['user_id' => $staffUser->id],
            $data
        );

        return redirect()
            ->route('tenant.staff.permissions.index')
            ->with('success', "Permissions updated for {$staffUser->name}.");
    }

    public function applyDefaults(int $userId): RedirectResponse
    {
        $staffUser = TenantUser::whereIn('role', ['teacher', 'accountant', 'librarian'])
            ->findOrFail($userId);

        StaffPermission::updateOrCreate(
            ['user_id' => $staffUser->id],
            StaffPermission::defaultsForRole($staffUser->role)
        );

        return redirect()
            ->route('tenant.staff.permissions.edit', $userId)
            ->with('success', "Default permissions applied for {$staffUser->name}.");
    }
}
