<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public LeaveApplication $leave,
        public string $event   // 'new_application' | 'approved' | 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $applicantName = $this->resolveApplicantName();
        $leaveType = $this->leave->leaveType?->name ?? 'Leave';

        return match ($this->event) {
            'new_application' => [
                'type' => 'leave_new',
                'title' => 'New Leave Application',
                'message' => "{$applicantName} has applied for {$leaveType} from {$this->leave->from_date->format('d M')} to {$this->leave->to_date->format('d M Y')} ({$this->leave->total_days} day(s)).",
                'url' => route('tenant.leave.show', $this->leave->id),
                'icon' => 'tabler-calendar-plus',
                'color' => 'warning',
            ],
            'approved' => [
                'type' => 'leave_approved',
                'title' => 'Leave Approved',
                'message' => "Your {$leaveType} application ({$this->leave->from_date->format('d M')} – {$this->leave->to_date->format('d M Y')}) has been approved.",
                'url' => route('tenant.leave.show', $this->leave->id),
                'icon' => 'tabler-calendar-check',
                'color' => 'success',
            ],
            'rejected' => [
                'type' => 'leave_rejected',
                'title' => 'Leave Rejected',
                'message' => "Your {$leaveType} application ({$this->leave->from_date->format('d M')} – {$this->leave->to_date->format('d M Y')}) was rejected.".($this->leave->rejection_reason ? " Reason: {$this->leave->rejection_reason}" : ''),
                'url' => route('tenant.leave.show', $this->leave->id),
                'icon' => 'tabler-calendar-x',
                'color' => 'danger',
            ],
            default => [
                'type' => 'leave_update',
                'title' => 'Leave Update',
                'message' => 'Your leave application status has been updated.',
                'url' => route('tenant.leave.show', $this->leave->id),
                'icon' => 'tabler-bell',
                'color' => 'info',
            ],
        };
    }

    private function resolveApplicantName(): string
    {
        if ($this->leave->applicant_type === 'student') {
            $profile = $this->leave->studentProfile;

            return $profile ? "{$profile->first_name} {$profile->last_name}" : 'A student';
        }

        $profile = $this->leave->staffProfile;

        return $profile ? "{$profile->first_name} {$profile->last_name}" : 'A staff member';
    }
}
