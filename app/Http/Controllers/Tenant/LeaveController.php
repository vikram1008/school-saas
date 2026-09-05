<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\ParentProfile;
use App\Models\Section;
use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\TenantUser;
use App\Notifications\LeaveStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    /**
     * Main leave index — role-based view.
     * Admin sees all; staff sees own + pending student leaves they can approve;
     * student/parent sees student's leave history.
     */
    public function index(Request $request): View
    {
        $user = $this->tenantUser();

        if ($user->isSchoolAdmin()) {
            return $this->adminIndex($request);
        }

        if ($user->isStaff()) {
            return $this->staffIndex($request, $user);
        }

        if ($user->isParent()) {
            return $this->parentIndex($request, $user);
        }

        // Student
        return $this->studentIndex($request, $user);
    }

    private function adminIndex(Request $request): View
    {
        $query = LeaveApplication::with(['leaveType', 'user', 'reviewer', 'studentProfile.class', 'studentProfile.section', 'staffProfile']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('leave_type_id', $request->type);
        }

        if ($request->filled('applicant_type')) {
            $query->where('applicant_type', $request->applicant_type);
        }

        if ($request->filled('from')) {
            $query->where('from_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('to_date', '<=', $request->to);
        }

        $leaves = $query->latest()->paginate(20)->withQueryString();
        $leaveTypes = LeaveType::active()->get();

        $stats = [
            'total' => LeaveApplication::count(),
            'pending' => LeaveApplication::where('status', 'pending')->count(),
            'approved' => LeaveApplication::where('status', 'approved')->count(),
            'rejected' => LeaveApplication::where('status', 'rejected')->count(),
        ];

        return view('tenant.leave.index', compact('leaves', 'leaveTypes', 'stats'))->with('role', 'admin');
    }

    private function staffIndex(Request $request, TenantUser $user): View
    {
        $sp = $user->resolvedPermissions();

        // Staff's own leave
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();
        $myLeaves = LeaveApplication::with(['leaveType', 'reviewer'])
            ->where('user_id', $user->id)
            ->where('applicant_type', 'staff')
            ->latest()
            ->get();

        // Pending student leaves this staff member can approve
        $pendingStudentLeaves = collect();

        if ($sp->can_approve_student_leave) {
            // Find sections where this user is class teacher
            $classTeacherSectionIds = Section::where('class_teacher_id', $user->id)->pluck('id');

            if ($classTeacherSectionIds->isNotEmpty()) {
                $studentIds = StudentProfile::whereIn('section_id', $classTeacherSectionIds)->pluck('id');

                $pendingStudentLeaves = LeaveApplication::with(['leaveType', 'user', 'studentProfile.class', 'studentProfile.section'])
                    ->where('applicant_type', 'student')
                    ->where('status', 'pending')
                    ->whereIn('applicant_id', $studentIds)
                    ->latest()
                    ->get();
            }
        }

        $leaveTypes = LeaveType::active()->forStaff()->get();

        $stats = [
            'total' => $myLeaves->count(),
            'pending' => $myLeaves->where('status', 'pending')->count(),
            'approved' => $myLeaves->where('status', 'approved')->count(),
            'rejected' => $myLeaves->where('status', 'rejected')->count(),
        ];

        return view('tenant.leave.index', compact('myLeaves', 'pendingStudentLeaves', 'leaveTypes', 'staffProfile', 'stats', 'sp'))->with('role', 'staff');
    }

    private function parentIndex(Request $request, TenantUser $user): View
    {
        $parentProfile = ParentProfile::where('user_id', $user->id)->with('students')->first();
        $students = $parentProfile?->students ?? collect();

        $leaves = LeaveApplication::with(['leaveType', 'reviewer', 'studentProfile.class'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $leaveTypes = LeaveType::active()->forStudents()->get();

        $stats = [
            'total' => $leaves->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
        ];

        return view('tenant.leave.index', compact('leaves', 'leaveTypes', 'students', 'stats', 'parentProfile'))->with('role', 'parent');
    }

    private function studentIndex(Request $request, TenantUser $user): View
    {
        $studentProfile = StudentProfile::where('user_id', $user->id)->with('class', 'section')->first();

        $leaves = LeaveApplication::with(['leaveType', 'reviewer'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $leaveTypes = LeaveType::active()->forStudents()->get();

        $stats = [
            'total' => $leaves->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
        ];

        return view('tenant.leave.index', compact('leaves', 'leaveTypes', 'studentProfile', 'stats'))->with('role', 'student');
    }

    /**
     * Show the apply-leave form.
     */
    public function create(): View
    {
        $user = $this->tenantUser();

        $leaveTypes = LeaveType::active()->get();
        $students = collect();
        $staffList = collect();

        if ($user->isSchoolAdmin()) {
            // Admin can apply for any student or staff
            $students = StudentProfile::where('status', 'active')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'class_id', 'section_id']);
            $staffList = StaffProfile::where('status', 'active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        } elseif ($user->isParent()) {
            $parentProfile = ParentProfile::where('user_id', $user->id)->with('students')->first();
            $students = $parentProfile?->students ?? collect();
        }

        return view('tenant.leave.create', compact('leaveTypes', 'students', 'staffList'));
    }

    /**
     * Store a new leave application.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->tenantUser();

        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'applicant_type' => ['sometimes', 'in:student,staff'],
            'applicant_id' => ['sometimes', 'integer'],
        ]);

        $totalDays = LeaveApplication::computeDays($data['from_date'], $data['to_date']);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('leave-documents', 'public');
        }

        // Determine who the leave is FOR
        $applicantType = 'student';
        $applicantId = null;
        $appliedByParent = false;

        if ($user->isSchoolAdmin() && $request->filled('applicant_type') && $request->filled('applicant_id')) {
            $applicantType = $data['applicant_type'];
            $applicantId = (int) $data['applicant_id'];
        } elseif ($user->isParent()) {
            // Parent applying for child
            $parentProfile = ParentProfile::where('user_id', $user->id)->with('students')->first();
            $applicantId = (int) $request->applicant_id;

            // Security: verify this student belongs to this parent
            $validStudentIds = $parentProfile?->students->pluck('id')->toArray() ?? [];
            if (! in_array($applicantId, $validStudentIds, true)) {
                abort(403, 'You can only apply leave for your own children.');
            }

            $appliedByParent = true;
        } elseif ($user->isStudent()) {
            $studentProfile = StudentProfile::where('user_id', $user->id)->first();
            $applicantId = $studentProfile?->id;
            $applicantType = 'student';
        } elseif ($user->isStaff()) {
            $staffProfile = StaffProfile::where('user_id', $user->id)->first();
            $applicantId = $staffProfile?->id;
            $applicantType = 'staff';
        }

        if (! $applicantId) {
            return back()->withErrors(['error' => 'Could not determine applicant profile. Please contact admin.']);
        }

        $leave = LeaveApplication::create([
            'applicant_type' => $applicantType,
            'applicant_id' => $applicantId,
            'user_id' => $user->id,
            'applied_by_parent' => $appliedByParent,
            'leave_type_id' => $data['leave_type_id'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'total_days' => $totalDays,
            'reason' => $data['reason'],
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);

        // Notify class teacher (if student leave)
        if ($applicantType === 'student') {
            $this->notifyClassTeacher($leave);
        }

        return redirect()->route('tenant.leave.show', $leave)->with('success', 'Leave application submitted successfully.');
    }

    /**
     * Show a single leave application.
     */
    public function show(LeaveApplication $leave): View
    {
        $user = $this->tenantUser();
        $this->authorizeView($user, $leave);

        $leave->load(['leaveType', 'user', 'reviewer', 'studentProfile.class', 'studentProfile.section', 'staffProfile']);

        return view('tenant.leave.show', compact('leave'));
    }

    /**
     * Approve a leave application.
     */
    public function approve(Request $request, LeaveApplication $leave): RedirectResponse
    {
        $user = $this->tenantUser();
        $this->authorizeApproval($user, $leave);

        if (! $leave->isPending()) {
            return back()->withErrors(['error' => 'This leave has already been processed.']);
        }

        $leave->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Notify applicant
        $this->notifyApplicant($leave, 'approved');

        return back()->with('success', 'Leave approved successfully.');
    }

    /**
     * Reject a leave application.
     */
    public function reject(Request $request, LeaveApplication $leave): RedirectResponse
    {
        $user = $this->tenantUser();
        $this->authorizeApproval($user, $leave);

        if (! $leave->isPending()) {
            return back()->withErrors(['error' => 'This leave has already been processed.']);
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $leave->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        // Notify applicant
        $this->notifyApplicant($leave, 'rejected');

        return back()->with('success', 'Leave rejected.');
    }

    /**
     * Cancel a pending leave (by applicant only).
     */
    public function cancel(LeaveApplication $leave): RedirectResponse
    {
        $user = $this->tenantUser();

        if (! $leave->canBeCancelledBy($user)) {
            abort(403, 'You cannot cancel this leave application.');
        }

        $leave->update(['status' => 'cancelled']);

        return back()->with('success', 'Leave application cancelled.');
    }

    // ── Private Helpers ─────────────────────────────────────────────

    /**
     * Ensure the user can view this leave application.
     */
    private function authorizeView(TenantUser $user, LeaveApplication $leave): void
    {
        if ($user->isSchoolAdmin()) {
            return; // admin sees all
        }

        // Own leave
        if ($leave->user_id === $user->id) {
            return;
        }

        // Staff approver — check if student is in their section
        if ($user->isStaff() && $user->hasPermission('can_approve_student_leave') && $leave->applicant_type === 'student') {
            $sectionIds = Section::where('class_teacher_id', $user->id)->pluck('id');
            if ($sectionIds->isNotEmpty()) {
                $studentIds = StudentProfile::whereIn('section_id', $sectionIds)->pluck('id');
                if ($studentIds->contains($leave->applicant_id)) {
                    return;
                }
            }
        }

        // Parent - check linked children
        if ($user->isParent()) {
            $parentProfile = ParentProfile::where('user_id', $user->id)->with('students')->first();
            $childIds = $parentProfile?->students->pluck('id')->toArray() ?? [];
            if ($leave->applicant_type === 'student' && in_array($leave->applicant_id, $childIds, true)) {
                return;
            }
        }

        abort(403);
    }

    /**
     * Ensure the user can approve/reject this leave.
     */
    private function authorizeApproval(TenantUser $user, LeaveApplication $leave): void
    {
        if ($user->isSchoolAdmin()) {
            return;
        }

        if ($user->isStaff() && $leave->applicant_type === 'student' && $user->hasPermission('can_approve_student_leave')) {
            $sectionIds = Section::where('class_teacher_id', $user->id)->pluck('id');
            $studentIds = StudentProfile::whereIn('section_id', $sectionIds)->pluck('id');
            if ($studentIds->contains($leave->applicant_id)) {
                return;
            }
        }

        abort(403, "You don't have permission to approve this leave.");
    }

    /**
     * Send database notification to the class teacher when a student applies leave.
     */
    private function notifyClassTeacher(LeaveApplication $leave): void
    {
        try {
            $leave->load('studentProfile.section');
            $section = $leave->studentProfile?->section;

            if ($section && $section->class_teacher_id) {
                $teacher = TenantUser::find($section->class_teacher_id);
                $teacher?->notify(new LeaveStatusChanged($leave, 'new_application'));
            }

            // Also notify admin
            $admins = TenantUser::where('role', 'school_admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new LeaveStatusChanged($leave, 'new_application'));
            }
        } catch (\Exception $e) {
            // Notifications should never block the main flow
        }
    }

    /**
     * Send notification to the leave applicant when status changes.
     */
    private function notifyApplicant(LeaveApplication $leave, string $newStatus): void
    {
        try {
            $applicantUser = $leave->user;
            $applicantUser?->notify(new LeaveStatusChanged($leave, $newStatus));
        } catch (\Exception $e) {
            // Notifications should never block the main flow
        }
    }
}
