<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'staff_attendance';

    protected $fillable = [
        'staff_profile_id', 'date', 'status',
        'in_time', 'out_time', 'marked_by', 'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(TenantUser::class, 'marked_by');
    }

    public static function statusLabels(): array
    {
        return [
            'present' => 'Present / उपस्थित',
            'absent'  => 'Absent / अनुपस्थित',
            'late'    => 'Late / देर से',
            'half_day'=> 'Half Day / अर्ध दिवस',
            'leave'   => 'Leave / अवकाश',
            'holiday' => 'Holiday / अवकाश दिवस',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'present'  => 'success',
            'absent'   => 'danger',
            'late'     => 'warning',
            'half_day' => 'info',
            'leave'    => 'secondary',
            'holiday'  => 'primary',
        ];
    }
}