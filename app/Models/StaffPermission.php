<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPermission extends Model
{
    protected $connection = 'tenant';

    protected $table = 'staff_permissions';

    protected $fillable = [
        'user_id',
        'can_mark_student_attendance',
        'can_mark_staff_attendance',
        'can_view_attendance_reports',
        'can_enter_marks',
        'can_view_exams',
        'can_view_report_cards',
        'can_manage_timetable',
        'can_view_timetable',
        'can_post_notices',
        'can_view_notices',
        'can_collect_fees',
        'can_view_fee_reports',
        'can_view_students',
        'can_view_staff',
        'can_view_parents',
        'can_manage_library',
        'can_approve_student_leave',
    ];

    protected $casts = [
        'can_mark_student_attendance' => 'boolean',
        'can_mark_staff_attendance' => 'boolean',
        'can_view_attendance_reports' => 'boolean',
        'can_enter_marks' => 'boolean',
        'can_view_exams' => 'boolean',
        'can_view_report_cards' => 'boolean',
        'can_manage_timetable' => 'boolean',
        'can_view_timetable' => 'boolean',
        'can_post_notices' => 'boolean',
        'can_view_notices' => 'boolean',
        'can_collect_fees' => 'boolean',
        'can_view_fee_reports' => 'boolean',
        'can_view_students' => 'boolean',
        'can_view_staff' => 'boolean',
        'can_view_parents' => 'boolean',
        'can_manage_library' => 'boolean',
        'can_approve_student_leave' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    /**
     * Default permission set for a given role.
     *
     * @return array<string, bool>
     */
    public static function defaultsForRole(string $role): array
    {
        return match ($role) {
            'teacher' => [
                'can_mark_student_attendance' => true,
                'can_mark_staff_attendance' => false,
                'can_view_attendance_reports' => false,
                'can_enter_marks' => true,
                'can_view_exams' => true,
                'can_view_report_cards' => true,
                'can_manage_timetable' => false,
                'can_view_timetable' => true,
                'can_post_notices' => false,
                'can_view_notices' => true,
                'can_collect_fees' => false,
                'can_view_fee_reports' => false,
                'can_view_students' => true,
                'can_view_staff' => false,
                'can_view_parents' => false,
                'can_manage_library' => false,
                'can_approve_student_leave' => true,
            ],
            'accountant' => [
                'can_mark_student_attendance' => false,
                'can_mark_staff_attendance' => false,
                'can_view_attendance_reports' => true,
                'can_enter_marks' => false,
                'can_view_exams' => false,
                'can_view_report_cards' => false,
                'can_manage_timetable' => false,
                'can_view_timetable' => false,
                'can_post_notices' => false,
                'can_view_notices' => true,
                'can_collect_fees' => true,
                'can_view_fee_reports' => true,
                'can_view_students' => true,
                'can_view_staff' => false,
                'can_view_parents' => false,
                'can_manage_library' => false,
                'can_approve_student_leave' => false,
            ],
            'librarian' => [
                'can_mark_student_attendance' => false,
                'can_mark_staff_attendance' => false,
                'can_view_attendance_reports' => false,
                'can_enter_marks' => false,
                'can_view_exams' => false,
                'can_view_report_cards' => false,
                'can_manage_timetable' => false,
                'can_view_timetable' => true,
                'can_post_notices' => false,
                'can_view_notices' => true,
                'can_collect_fees' => false,
                'can_view_fee_reports' => false,
                'can_view_students' => true,
                'can_view_staff' => false,
                'can_view_parents' => false,
                'can_manage_library' => true,
                'can_approve_student_leave' => false,
            ],
            default => [
                'can_mark_student_attendance' => false,
                'can_mark_staff_attendance' => false,
                'can_view_attendance_reports' => false,
                'can_enter_marks' => false,
                'can_view_exams' => false,
                'can_view_report_cards' => false,
                'can_manage_timetable' => false,
                'can_view_timetable' => false,
                'can_post_notices' => false,
                'can_view_notices' => true,
                'can_collect_fees' => false,
                'can_view_fee_reports' => false,
                'can_view_students' => false,
                'can_view_staff' => false,
                'can_view_parents' => false,
                'can_manage_library' => false,
                'can_approve_student_leave' => false,
            ],
        };
    }

    /**
     * Human-readable labels for permission keys.
     *
     * @return array<string, array{label: string, description: string, icon: string, group: string}>
     */
    public static function permissionMeta(): array
    {
        return [
            'can_mark_student_attendance' => ['label' => 'Mark Student Attendance', 'description' => 'Take daily student attendance', 'icon' => 'tabler-user-check', 'group' => 'Attendance'],
            'can_mark_staff_attendance' => ['label' => 'Mark Staff Attendance', 'description' => 'Take daily staff attendance', 'icon' => 'tabler-id-badge', 'group' => 'Attendance'],
            'can_view_attendance_reports' => ['label' => 'View Attendance Reports', 'description' => 'Access attendance analytics', 'icon' => 'tabler-chart-bar', 'group' => 'Attendance'],
            'can_enter_marks' => ['label' => 'Enter Marks', 'description' => 'Enter exam marks for students', 'icon' => 'tabler-pencil', 'group' => 'Academics'],
            'can_view_exams' => ['label' => 'View Exams', 'description' => 'View exam schedule', 'icon' => 'tabler-clipboard-list', 'group' => 'Academics'],
            'can_view_report_cards' => ['label' => 'View Report Cards', 'description' => 'Access student result cards', 'icon' => 'tabler-certificate', 'group' => 'Academics'],
            'can_manage_timetable' => ['label' => 'Manage Timetable', 'description' => 'Create/edit class timetables', 'icon' => 'tabler-calendar-time', 'group' => 'Timetable'],
            'can_view_timetable' => ['label' => 'View Timetable', 'description' => 'View teacher/class timetable', 'icon' => 'tabler-calendar', 'group' => 'Timetable'],
            'can_post_notices' => ['label' => 'Post Notices', 'description' => 'Publish school notices', 'icon' => 'tabler-speakerphone', 'group' => 'Communication'],
            'can_view_notices' => ['label' => 'View Notices', 'description' => 'Read published notices', 'icon' => 'tabler-bell', 'group' => 'Communication'],
            'can_collect_fees' => ['label' => 'Collect Fees', 'description' => 'Record fee payments', 'icon' => 'tabler-cash', 'group' => 'Finance'],
            'can_view_fee_reports' => ['label' => 'View Fee Reports', 'description' => 'Access fee collection reports', 'icon' => 'tabler-report-money', 'group' => 'Finance'],
            'can_view_students' => ['label' => 'View Students', 'description' => 'Access student directory', 'icon' => 'tabler-users', 'group' => 'People'],
            'can_view_staff' => ['label' => 'View Staff', 'description' => 'Access staff directory', 'icon' => 'tabler-chalkboard', 'group' => 'People'],
            'can_view_parents' => ['label' => 'View Parents', 'description' => 'Access parent directory', 'icon' => 'tabler-users-group', 'group' => 'People'],
            'can_manage_library' => ['label' => 'Manage Library', 'description' => 'Issue, return, and manage library books', 'icon' => 'tabler-books', 'group' => 'Library'],
            'can_approve_student_leave' => ['label' => 'Approve Student Leave', 'description' => 'Review and approve/reject student leave applications', 'icon' => 'tabler-calendar-check', 'group' => 'Leave'],
        ];
    }
}
