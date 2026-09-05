<?php

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\TenantUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Helpers ─────────────────────────────────────────────────────────

function makeLeaveType(array $attrs = []): LeaveType
{
    return LeaveType::create(array_merge([
        'name' => 'Test Leave',
        'max_days_per_year' => 10,
        'requires_document' => false,
        'applicable_to_students' => true,
        'applicable_to_staff' => true,
        'is_active' => true,
        'sort_order' => 1,
    ], $attrs));
}

// ── Leave Type ───────────────────────────────────────────────────────

test('leave type can be created and has expected columns', function () {
    $lt = makeLeaveType(['name' => 'Sick Leave', 'name_hi' => 'बीमारी']);

    expect($lt->name)->toBe('Sick Leave')
        ->and($lt->name_hi)->toBe('बीमारी')
        ->and($lt->is_active)->toBeTrue();
});

test('leave type scope active only returns active types', function () {
    makeLeaveType(['is_active' => true]);
    makeLeaveType(['is_active' => false]);

    expect(LeaveType::active()->count())->toBe(1);
});

// ── LeaveApplication computeDays ─────────────────────────────────────

test('total days is computed correctly', function () {
    expect(LeaveApplication::computeDays('2026-06-01', '2026-06-01'))->toBe(1)
        ->and(LeaveApplication::computeDays('2026-06-01', '2026-06-05'))->toBe(5)
        ->and(LeaveApplication::computeDays('2026-06-01', '2026-06-10'))->toBe(10);
});

// ── Status helpers ────────────────────────────────────────────────────

test('leave application status helpers return correct values', function () {
    $lt = makeLeaveType();
    $admin = TenantUser::factory()->create(['role' => 'school_admin']);

    $leave = LeaveApplication::create([
        'applicant_type' => 'staff',
        'applicant_id' => 1,
        'user_id' => $admin->id,
        'leave_type_id' => $lt->id,
        'from_date' => '2026-06-10',
        'to_date' => '2026-06-12',
        'total_days' => 3,
        'reason' => 'Test reason',
        'status' => 'pending',
    ]);

    expect($leave->isPending())->toBeTrue()
        ->and($leave->isApproved())->toBeFalse()
        ->and($leave->statusColor())->toBe('warning')
        ->and($leave->statusLabel())->toBe('Pending');

    $leave->update(['status' => 'approved']);
    expect($leave->fresh()->isApproved())->toBeTrue()
        ->and($leave->fresh()->statusColor())->toBe('success');
});

// ── canBeCancelledBy ──────────────────────────────────────────────────

test('applicant can cancel their own pending leave', function () {
    $lt = makeLeaveType();
    $user = TenantUser::factory()->create(['role' => 'teacher']);

    $leave = LeaveApplication::create([
        'applicant_type' => 'staff',
        'applicant_id' => 1,
        'user_id' => $user->id,
        'leave_type_id' => $lt->id,
        'from_date' => '2026-07-01',
        'to_date' => '2026-07-03',
        'total_days' => 3,
        'reason' => 'Test',
        'status' => 'pending',
    ]);

    expect($leave->canBeCancelledBy($user))->toBeTrue();

    // A different user cannot cancel it
    $other = TenantUser::factory()->create(['role' => 'teacher']);
    expect($leave->canBeCancelledBy($other))->toBeFalse();
});

test('approved leave cannot be cancelled', function () {
    $lt = makeLeaveType();
    $user = TenantUser::factory()->create(['role' => 'teacher']);

    $leave = LeaveApplication::create([
        'applicant_type' => 'staff',
        'applicant_id' => 1,
        'user_id' => $user->id,
        'leave_type_id' => $lt->id,
        'from_date' => '2026-07-01',
        'to_date' => '2026-07-02',
        'total_days' => 2,
        'reason' => 'Test',
        'status' => 'approved',
    ]);

    expect($leave->canBeCancelledBy($user))->toBeFalse();
});

// ── Permission: teacher default has can_approve_student_leave ─────────

test('teacher has can_approve_student_leave by default', function () {
    $teacher = TenantUser::factory()->create(['role' => 'teacher']);
    $perms = $teacher->resolvedPermissions();

    expect($perms->can_approve_student_leave)->toBeTrue();
});

test('accountant does not have can_approve_student_leave by default', function () {
    $accountant = TenantUser::factory()->create(['role' => 'accountant']);
    $perms = $accountant->resolvedPermissions();

    expect($perms->can_approve_student_leave)->toBeFalse();
});

test('school admin hasPermission always returns true', function () {
    $admin = TenantUser::factory()->create(['role' => 'school_admin']);

    expect($admin->hasPermission('can_approve_student_leave'))->toBeTrue();
});
