<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'staff_documents';

    protected $fillable = [
        'staff_profile_id',
        'document_type',
        'file_path',
        'original_name',
        'is_verified',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public static function typeLabels(): array
    {
        return [
            'aadhaar_card'           => 'Aadhaar Card / आधार कार्ड',
            'pan_card'               => 'PAN Card / पैन कार्ड',
            'degree_certificate'     => 'Degree Certificate / डिग्री प्रमाण पत्र',
            'marksheet'              => 'Marksheet / अंकतालिका',
            'experience_certificate' => 'Experience Certificate / अनुभव प्रमाण पत्र',
            'appointment_letter'     => 'Appointment Letter / नियुक्ति पत्र',
            'caste_certificate'      => 'Caste Certificate / जाति प्रमाण पत्र',
            'disability_certificate' => 'Disability Certificate / विकलांगता प्रमाण पत्र',
            'other'                  => 'Other / अन्य',
        ];
    }

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }
}