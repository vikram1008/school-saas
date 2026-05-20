<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentFamilyDetail;
use App\Models\StudentProfile;
use App\Models\TenantUser;
use App\Services\ParentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public array $errors = [];

    public array $imported = [];

    public array $skipped = [];

    protected ?AcademicYear $activeYear;

    /** @var Collection<SchoolClass> */
    protected Collection $classes;

    /** @var Collection<Section> */
    protected Collection $sections;

    public function __construct()
    {
        $this->activeYear = AcademicYear::active();
        $this->classes = SchoolClass::all(['id', 'name']);
        $this->sections = Section::all(['id', 'name', 'class_id']);
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is header

            try {
                $admissionNumber = trim((string) ($row['admission_number'] ?? $row['admission_no'] ?? ''));

                if (blank($admissionNumber)) {
                    $this->skipped[] = "Row {$rowNum}: Admission number is empty, skipped.";

                    continue;
                }

                // Skip if already exists
                if (StudentProfile::where('admission_number', $admissionNumber)->exists()) {
                    $this->skipped[] = "Row {$rowNum}: Admission No. \"{$admissionNumber}\" already exists, skipped.";

                    continue;
                }

                $firstName = trim((string) ($row['first_name'] ?? ''));
                $lastName = trim((string) ($row['last_name'] ?? ''));

                if (blank($firstName) || blank($lastName)) {
                    $this->errors[] = "Row {$rowNum}: First name or last name is missing.";

                    continue;
                }

                $gender = strtolower(trim((string) ($row['gender'] ?? 'male')));
                if (! in_array($gender, ['male', 'female', 'other'])) {
                    $gender = 'male';
                }

                // Resolve class & section
                $classId = $this->resolveClassId($row['class'] ?? null);
                $sectionId = $this->resolveSectionId($row['section'] ?? null, $classId);

                DB::beginTransaction();

                // Create login user
                $email = filled($row['email'] ?? null)
                    ? trim((string) $row['email'])
                    : Str::slug($firstName.$lastName).'@'.tenant('id').'.student';

                // Ensure unique email
                if (TenantUser::where('email', $email)->exists()) {
                    $email = Str::slug($firstName.$lastName).'-'.$admissionNumber.'@'.tenant('id').'.student';
                }

                $user = TenantUser::create([
                    'name' => $firstName.' '.$lastName,
                    'email' => $email,
                    'password' => Hash::make($admissionNumber),
                    'role' => 'student',
                    'is_active' => true,
                ]);

                $dob = $this->parseDate($row['date_of_birth'] ?? $row['dob'] ?? null);
                $admissionDate = $this->parseDate($row['admission_date'] ?? null);

                $student = StudentProfile::create([
                    'user_id' => $user->id,
                    'admission_number' => $admissionNumber,
                    'sr_number' => filled($row['sr_number'] ?? null) ? trim((string) $row['sr_number']) : null,
                    'admission_date' => $admissionDate,
                    'first_name' => $firstName,
                    'first_name_hi' => filled($row['first_name_hindi'] ?? null) ? trim((string) $row['first_name_hindi']) : null,
                    'last_name' => $lastName,
                    'last_name_hi' => filled($row['last_name_hindi'] ?? null) ? trim((string) $row['last_name_hindi']) : null,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'category' => filled($row['category'] ?? null) ? strtolower(trim((string) $row['category'])) : null,
                    'blood_group' => filled($row['blood_group'] ?? null) ? trim((string) $row['blood_group']) : null,
                    'aadhaar_number' => filled($row['aadhaar_number'] ?? null) ? trim((string) $row['aadhaar_number']) : null,
                    'phone' => filled($row['phone'] ?? null) ? trim((string) $row['phone']) : null,
                    'email' => filled($row['email'] ?? null) ? trim((string) $row['email']) : null,
                    'academic_year_id' => $this->activeYear?->id,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'admission_year' => now()->year,
                    'status' => 'active',
                    'city' => filled($row['city'] ?? null) ? trim((string) $row['city']) : null,
                    'state' => filled($row['state'] ?? null) ? trim((string) $row['state']) : null,
                    'pincode' => filled($row['pincode'] ?? null) ? trim((string) $row['pincode']) : null,
                ]);

                // Family details
                $fatherName = filled($row['father_name'] ?? null) ? trim((string) $row['father_name']) : null;
                $motherName = filled($row['mother_name'] ?? null) ? trim((string) $row['mother_name']) : null;
                $fatherMobile = filled($row['father_mobile'] ?? null) ? trim((string) $row['father_mobile']) : null;
                $motherMobile = filled($row['mother_mobile'] ?? null) ? trim((string) $row['mother_mobile']) : null;

                if ($fatherName || $motherName) {
                    StudentFamilyDetail::create([
                        'student_profile_id' => $student->id,
                        'father_name' => $fatherName,
                        'father_mobile' => $fatherMobile,
                        'mother_name' => $motherName,
                        'mother_mobile' => $motherMobile,
                    ]);
                }

                // Auto-create parent account
                try {
                    app(ParentService::class)->createFromStudent($student);
                } catch (\Exception) {
                    // Non-critical — don't fail import
                }

                DB::commit();

                $this->imported[] = "{$firstName} {$lastName} ({$admissionNumber})";

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Row {$rowNum}: ".$e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function resolveClassId(?string $name): ?int
    {
        if (blank($name)) {
            return null;
        }

        return $this->classes->firstWhere('name', trim($name))?->id;
    }

    protected function resolveSectionId(?string $name, ?int $classId): ?int
    {
        if (blank($name) || ! $classId) {
            return null;
        }

        return $this->sections
            ->where('class_id', $classId)
            ->firstWhere('name', trim($name))?->id;
    }

    protected function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Handle Excel serial dates
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Exception) {
                return null;
            }
        }

        $value = trim((string) $value);

        // Try common formats
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'd.m.Y'] as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }
}
