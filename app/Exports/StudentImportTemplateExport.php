<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentImportTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Students Import Template';
    }

    public function array(): array
    {
        // Two sample rows to guide the user
        return [
            [
                'STU001', 'ADM-2024-001', 'Rahul', 'Sharma', 'राहुल', 'शर्मा',
                'male', '15/08/2010', 'general', 'B+',
                '9876543210', '', 'Class 1', 'A', '01/04/2024',
                'Suresh Sharma', '9812345678', 'Priya Sharma', '9898765432',
                '123456789012', 'Jaipur', 'Rajasthan', '302001',
            ],
            [
                'STU002', 'ADM-2024-002', 'Priya', 'Verma', 'प्रिया', 'वर्मा',
                'female', '20/03/2011', 'obc', 'A+',
                '9765432109', '', 'Class 2', 'B', '01/04/2024',
                'Ramesh Verma', '9823456789', 'Sunita Verma', '9845678901',
                '234567890123', 'Jodhpur', 'Rajasthan', '342001',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'sr_number',
            'admission_number',
            'first_name',
            'last_name',
            'first_name_hindi',
            'last_name_hindi',
            'gender',
            'date_of_birth',
            'category',
            'blood_group',
            'phone',
            'email',
            'class',
            'section',
            'admission_date',
            'father_name',
            'father_mobile',
            'mother_name',
            'mother_mobile',
            'aadhaar_number',
            'city',
            'state',
            'pincode',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7367F0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            2 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EEF0FF'],
                ],
            ],
            3 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F8FF'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, 'B' => 18, 'C' => 16, 'D' => 16,
            'E' => 16, 'F' => 16, 'G' => 10, 'H' => 14,
            'I' => 12, 'J' => 12, 'K' => 14, 'L' => 25,
            'M' => 14, 'N' => 12, 'O' => 14, 'P' => 22,
            'Q' => 14, 'R' => 22, 'S' => 14, 'T' => 16,
            'U' => 16, 'V' => 16, 'W' => 10,
        ];
    }
}
