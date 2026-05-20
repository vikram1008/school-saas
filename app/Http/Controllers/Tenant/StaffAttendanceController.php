<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\StaffProfile;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffAttendanceController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    public function index(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_mark_staff_attendance');

        $date = $request->date ?? today()->toDateString();
        $type = $request->staff_type ?? '';

        $staff = StaffProfile::where('status', 'active')
            ->when($type, fn ($q) => $q->where('staff_type', $type))
            ->orderBy('first_name')
            ->get();

        $existingAttendance = StaffAttendance::whereDate('date', $date)
            ->get()
            ->keyBy('staff_profile_id');

        $summary = [
            'present' => $existingAttendance->where('status', 'present')->count(),
            'absent' => $existingAttendance->where('status', 'absent')->count(),
            'late' => $existingAttendance->where('status', 'late')->count(),
            'half_day' => $existingAttendance->where('status', 'half_day')->count(),
            'leave' => $existingAttendance->where('status', 'leave')->count(),
            'total' => $staff->count(),
        ];

        return view('tenant.attendance.staff.index', compact(
            'staff', 'existingAttendance', 'summary', 'date', 'type'
        ));
    }

    public function store(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_mark_staff_attendance');

        $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
        ]);

        $markedBy = Auth::guard('tenant')->id();

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $staffId => $data) {
                StaffAttendance::updateOrCreate(
                    [
                        'staff_profile_id' => $staffId,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $data['status'] ?? 'present',
                        'in_time' => $data['in_time'] ?? null,
                        'out_time' => $data['out_time'] ?? null,
                        'remarks' => $data['remarks'] ?? null,
                        'marked_by' => $markedBy,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed: '.$e->getMessage()]);
        }

        return redirect()
            ->back()
            ->with('success', 'Staff attendance saved for '.count($request->attendance).' members.');
    }
}
