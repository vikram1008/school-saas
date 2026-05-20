<?php

namespace App\Exports;

use App\Models\StudentProfile;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected ?string $classId = null,
        protected ?string $sectionId = null,
        protected ?string $status = null,
        protected ?string $search = null,
    ) {}

    public function title(): string
    {
        return 'Students';
    }

    public function query()
    {
        $query = StudentProfile::with(['class', 'section', 'familyDetail', 'academicYear'])
            ->orderBy('class_id')
            ->orderBy('first_name');

        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }

        if ($this->sectionId) {
            $query->where('section_id', $this->sectionId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('sr_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'SR No.',
            'Admission No.',
            'First Name',
            'Last Name',
            'First Name (Hindi)',
            'Last Name (Hindi)',
            'Gender',
            'Date of Birth',
            'Category',
            'Blood Group',
            'Phone',
            'Email',
            'Class',
            'Section',
            'Academic Year',
            'Status',
            'Admission Date',
            'Father Name',
            'Father Mobile',
            'Mother Name',
            'Mother Mobile',
            'Aadhaar Number',
            'City',
            'State',
            'Pincode',
        ];
    }

    /** @param StudentProfile $row */
    public function map($row): array
    {
        return [
            $row->sr_number,
            $row->admission_number,
            $row->first_name,
            $row->last_name,
            $row->first_name_hi,
            $row->last_name_hi,
            $row->gender,
            $row->date_of_birth?->format('d/m/Y'),
            $row->category,
            $row->blood_group,
            $row->phone,
            $row->email,
            $row->class?->name,
            $row->section?->name,
            $row->academicYear?->name,
            $row->status,
            $row->admission_date?->format('d/m/Y'),
            $row->familyDetail?->father_name,
            $row->familyDetail?->father_mobile,
            $row->familyDetail?->mother_name,
            $row->familyDetail?->mother_mobile,
            $row->aadhaar_number,
            $row->city,
            $row->state,
            $row->pincode,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7367F0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // SR No.
            'B' => 18, // Admission No.
            'C' => 18, // First Name
            'D' => 18, // Last Name
            'E' => 18, // First Name (Hindi)
            'F' => 18, // Last Name (Hindi)
            'G' => 10, // Gender
            'H' => 14, // DOB
            'I' => 12, // Category
            'J' => 12, // Blood Group
            'K' => 14, // Phone
            'L' => 25, // Email
            'M' => 14, // Class
            'N' => 12, // Section
            'O' => 14, // Academic Year
            'P' => 12, // Status
            'Q' => 14, // Admission Date
            'R' => 22, // Father Name
            'S' => 14, // Father Mobile
            'T' => 22, // Mother Name
            'U' => 14, // Mother Mobile
            'V' => 16, // Aadhaar
            'W' => 16, // City
            'X' => 16, // State
            'Y' => 10, // Pincode
        ];
    }
}
