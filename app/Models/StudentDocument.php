<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_documents';

    protected $fillable = [
        'student_profile_id',
        'document_type',
        'file_path',
        'original_name',
        'is_verified',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    // Human readable document type labels
    public static function typeLabels(): array
    {
        return [
            'birth_certificate'      => 'Birth Certificate / जन्म प्रमाण पत्र',
            'transfer_certificate'   => 'Transfer Certificate (TC) / स्थानांतरण प्रमाण पत्र',
            'marksheet'              => 'Previous Marksheet / पिछली अंकतालिका',
            'aadhaar_card'           => 'Aadhaar Card / आधार कार्ड',
            'jan_aadhaar_card'       => 'Jan Aadhaar Card / जन आधार कार्ड',
            'caste_certificate'      => 'Caste Certificate / जाति प्रमाण पत्र',
            'income_certificate'     => 'Income Certificate / आय प्रमाण पत्र',
            'bpl_card'               => 'BPL Card / बीपीएल कार्ड',
            'disability_certificate' => 'Disability Certificate / विकलांगता प्रमाण पत्र',
            'other'                  => 'Other / अन्य',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}