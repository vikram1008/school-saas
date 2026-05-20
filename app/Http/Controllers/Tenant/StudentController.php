<?php

namespace App\Http\Controllers\Tenant;

use App\Exports\StudentImportTemplateExport;
use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentAcademicHistory;
use App\Models\StudentAddress;
use App\Models\StudentBankDetail;
use App\Models\StudentDocument;
use App\Models\StudentFamilyDetail;
use App\Models\StudentProfile;
use App\Models\StudentSubject;
use App\Models\TenantUser;
use App\Services\ParentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    private function requireAdmin(): void
    {
        if (! $this->tenantUser()->isSchoolAdmin()) {
            abort(403, 'Only school admins can perform this action.');
        }
    }

    public function index(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_view_students');

        $activeYear = AcademicYear::active();
        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $query = StudentProfile::with(['class', 'section', 'academicYear', 'familyDetail'])
            ->orderBy('first_name');

        // Filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('sr_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();

        return view('tenant.students.index', compact(
            'students', 'classes', 'activeYear'
        ));
    }

    public function create()
    {
        $this->requireAdmin();

        $activeYear = AcademicYear::active();
        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        return view('tenant.students.create', compact('activeYear', 'classes'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            // Office Use
            'admission_number' => ['required', 'string', 'unique:student_profiles,admission_number'],
            'sr_number' => ['nullable', 'string', 'unique:student_profiles,sr_number'],
            'admission_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],

            // Personal
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'aadhaar_number' => ['nullable', 'digits:12'],
            'photo' => ['nullable', 'image', 'max:2048'],

            // Family
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],

            // Bank
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ]);

        DB::beginTransaction();

        try {
            // Create login user account
            $user = TenantUser::create([
                'name' => $request->first_name.' '.$request->last_name,
                'email' => $request->email
                              ?? Str::slug($request->first_name.$request->last_name)
                              .'@'.tenant('id').'.student',
                'password' => Hash::make($request->admission_number),
                'role' => 'student',
                'is_active' => true,
            ]);

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store(
                    'students/photos',
                    'public'
                );
            }

            // Create student profile
            $student = StudentProfile::create([
                'user_id' => $user->id,
                'admission_number' => $request->admission_number,
                'sr_number' => $request->sr_number,
                'admission_date' => $request->admission_date,
                'first_name' => $request->first_name,
                'first_name_hi' => $request->first_name_hi,
                'last_name' => $request->last_name,
                'last_name_hi' => $request->last_name_hi,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'dob_in_words' => $request->dob_in_words,
                'dob_in_words_hi' => $request->dob_in_words_hi,
                'aadhaar_number' => $request->aadhaar_number,
                'jan_aadhaar_number' => $request->jan_aadhaar_number,
                'category' => $request->category,
                'minority_status' => $request->boolean('minority_status'),
                'bpl_status' => $request->boolean('bpl_status'),
                'cwsn_type' => $request->cwsn_type,
                'blood_group' => $request->blood_group,
                'identification_mark' => $request->identification_mark,
                'identification_mark_hi' => $request->identification_mark_hi,
                'photo' => $photoPath,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'admission_year' => now()->year,
                'status' => 'active',
            ]);

            // Family Details
            StudentFamilyDetail::create([
                'student_profile_id' => $student->id,
                'father_name' => $request->father_name,
                'father_name_hi' => $request->father_name_hi,
                'father_occupation' => $request->father_occupation,
                'father_occupation_hi' => $request->father_occupation_hi,
                'father_annual_income' => $request->father_annual_income,
                'father_mobile' => $request->father_mobile,
                'father_aadhaar' => $request->father_aadhaar,
                'mother_name' => $request->mother_name,
                'mother_name_hi' => $request->mother_name_hi,
                'mother_occupation' => $request->mother_occupation,
                'mother_occupation_hi' => $request->mother_occupation_hi,
                'mother_annual_income' => $request->mother_annual_income,
                'mother_mobile' => $request->mother_mobile,
                'mother_aadhaar' => $request->mother_aadhaar,
                'guardian_name' => $request->guardian_name,
                'guardian_name_hi' => $request->guardian_name_hi,
                'guardian_relationship' => $request->guardian_relationship,
                'guardian_relationship_hi' => $request->guardian_relationship_hi,
                'guardian_mobile' => $request->guardian_mobile,
                'guardian_occupation' => $request->guardian_occupation,
                'guardian_occupation_hi' => $request->guardian_occupation_hi,
                'guardian_annual_income' => $request->guardian_annual_income,
            ]);

            // Address
            StudentAddress::create([
                'student_profile_id' => $student->id,
                'perm_house_no' => $request->perm_house_no,
                'perm_house_no_hi' => $request->perm_house_no_hi,
                'perm_street' => $request->perm_street,
                'perm_street_hi' => $request->perm_street_hi,
                'perm_village_city' => $request->perm_village_city,
                'perm_village_city_hi' => $request->perm_village_city_hi,
                'perm_tehsil' => $request->perm_tehsil,
                'perm_tehsil_hi' => $request->perm_tehsil_hi,
                'perm_district' => $request->perm_district,
                'perm_district_hi' => $request->perm_district_hi,
                'perm_state' => $request->perm_state,
                'perm_state_hi' => $request->perm_state_hi,
                'perm_pincode' => $request->perm_pincode,
                'same_as_permanent' => $request->boolean('same_as_permanent'),
                'corr_house_no' => $request->corr_house_no,
                'corr_street' => $request->corr_street,
                'corr_village_city' => $request->corr_village_city,
                'corr_tehsil' => $request->corr_tehsil,
                'corr_district' => $request->corr_district,
                'corr_state' => $request->corr_state,
                'corr_pincode' => $request->corr_pincode,
            ]);

            // Academic History
            StudentAcademicHistory::create([
                'student_profile_id' => $student->id,
                'previous_school_name' => $request->previous_school_name,
                'previous_school_type' => $request->previous_school_type,
                'last_class_attended' => $request->last_class_attended,
                'last_result' => $request->last_result,
                'percentage_grade' => $request->percentage_grade,
                'tc_number' => $request->tc_number,
                'tc_issue_date' => $request->tc_issue_date,
                'medium_of_instruction' => $request->medium_of_instruction,
                'medium_other' => $request->medium_other,
            ]);

            // Bank Details
            StudentBankDetail::create([
                'student_profile_id' => $student->id,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'account_holder' => $request->account_holder ?? 'parent',
                'account_holder_name' => $request->account_holder_name,
            ]);

            // Subjects
            StudentSubject::create([
                'student_profile_id' => $student->id,
                'stream' => $request->stream ?? 'na',
                'subject_1' => $request->subject_1,
                'subject_1_hi' => $request->subject_1_hi,
                'subject_2' => $request->subject_2,
                'subject_2_hi' => $request->subject_2_hi,
                'subject_3' => $request->subject_3,
                'subject_3_hi' => $request->subject_3_hi,
                'subject_4' => $request->subject_4,
                'subject_4_hi' => $request->subject_4_hi,
                'subject_5' => $request->subject_5,
                'subject_5_hi' => $request->subject_5_hi,
                'additional_subject' => $request->additional_subject,
                'additional_subject_hi' => $request->additional_subject_hi,
            ]);

            // Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if ($file) {
                        $path = $file->store('students/documents', 'public');
                        StudentDocument::create([
                            'student_profile_id' => $student->id,
                            'document_type' => $type,
                            'file_path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }

            // Auto-create parent account
            try {
                app(ParentService::class)->createFromStudent($student);
            } catch (\Exception $e) {
                // Don't fail student creation if parent creation fails
                \Log::warning('Parent auto-creation failed: '.$e->getMessage());
            }

            DB::commit();

            return redirect()
                ->route('tenant.students.show', $student)
                ->with('success', "Student \"{$student->full_name}\" admitted successfully! Login: {$user->email} / Password: {$request->admission_number}");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'error' => 'Failed to save student: '.$e->getMessage(),
            ]);
        }
    }

    public function show(StudentProfile $student)
    {
        $this->tenantUser()->authorizePermission('can_view_students');

        $student->load([
            'class', 'section', 'academicYear',
            'familyDetail', 'address', 'academicHistory',
            'bankDetail', 'subjects', 'documents',
        ]);

        return view('tenant.students.show', compact('student'));
    }

    public function edit(StudentProfile $student)
    {
        $this->requireAdmin();

        $student->load([
            'familyDetail', 'address', 'academicHistory',
            'bankDetail', 'subjects', 'documents',
        ]);

        $activeYear = AcademicYear::active();
        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $sections = $student->class_id
            ? Section::where('class_id', $student->class_id)->orderBy('order')->get()
            : collect();

        return view('tenant.students.edit', compact(
            'student', 'activeYear', 'classes', 'sections'
        ));
    }

    public function update(Request $request, StudentProfile $student)
    {
        $this->requireAdmin();

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'aadhaar_number' => ['nullable', 'digits:12'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ]);

        DB::beginTransaction();

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }
                $photoPath = $request->file('photo')->store('students/photos', 'public');
                $student->photo = $photoPath;
            }

            $student->update($request->except(['photo', 'documents', '_token', '_method']));

            // Update related records
            $student->familyDetail()->updateOrCreate(
                ['student_profile_id' => $student->id],
                $request->only([
                    'father_name', 'father_name_hi', 'father_occupation',
                    'father_occupation_hi', 'father_annual_income',
                    'father_mobile', 'father_aadhaar',
                    'mother_name', 'mother_name_hi', 'mother_occupation',
                    'mother_occupation_hi', 'mother_annual_income',
                    'mother_mobile', 'mother_aadhaar',
                    'guardian_name', 'guardian_name_hi',
                    'guardian_relationship', 'guardian_relationship_hi',
                    'guardian_mobile', 'guardian_occupation',
                    'guardian_occupation_hi', 'guardian_annual_income',
                ])
            );

            $student->address()->updateOrCreate(
                ['student_profile_id' => $student->id],
                $request->only([
                    'perm_house_no', 'perm_house_no_hi',
                    'perm_street', 'perm_street_hi',
                    'perm_village_city', 'perm_village_city_hi',
                    'perm_tehsil', 'perm_tehsil_hi',
                    'perm_district', 'perm_district_hi',
                    'perm_state', 'perm_state_hi', 'perm_pincode',
                    'same_as_permanent',
                    'corr_house_no', 'corr_street', 'corr_village_city',
                    'corr_tehsil', 'corr_district', 'corr_state', 'corr_pincode',
                ])
            );

            $student->academicHistory()->updateOrCreate(
                ['student_profile_id' => $student->id],
                $request->only([
                    'previous_school_name', 'previous_school_type',
                    'last_class_attended', 'last_result',
                    'percentage_grade', 'tc_number', 'tc_issue_date',
                    'medium_of_instruction', 'medium_other',
                ])
            );

            $student->bankDetail()->updateOrCreate(
                ['student_profile_id' => $student->id],
                $request->only([
                    'bank_name', 'bank_branch', 'account_number',
                    'ifsc_code', 'account_holder', 'account_holder_name',
                ])
            );

            $student->subjects()->updateOrCreate(
                ['student_profile_id' => $student->id],
                $request->only([
                    'stream',
                    'subject_1', 'subject_1_hi',
                    'subject_2', 'subject_2_hi',
                    'subject_3', 'subject_3_hi',
                    'subject_4', 'subject_4_hi',
                    'subject_5', 'subject_5_hi',
                    'additional_subject', 'additional_subject_hi',
                ])
            );

            // Handle new document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if ($file) {
                        $path = $file->store('students/documents', 'public');
                        StudentDocument::updateOrCreate(
                            [
                                'student_profile_id' => $student->id,
                                'document_type' => $type,
                            ],
                            [
                                'file_path' => $path,
                                'original_name' => $file->getClientOriginalName(),
                            ]
                        );
                    }
                }
            }

            // Update user name
            $student->user->update([
                'name' => $request->first_name.' '.$request->last_name,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.students.show', $student)
                ->with('success', 'Student details updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'error' => 'Failed to update: '.$e->getMessage(),
            ]);
        }
    }

    public function destroy(StudentProfile $student)
    {
        $this->requireAdmin();

        $student->user()->update(['is_active' => false]);
        $student->update(['status' => 'inactive']);

        return redirect()
            ->route('tenant.students.index')
            ->with('success', 'Student deactivated successfully.');
    }

    public function resetPassword(Request $request, StudentProfile $student)
    {
        $this->requireAdmin();

        $request->validate([
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (! $student->user) {
            return redirect()
                ->route('tenant.students.show', $student)
                ->with('error', 'No login account found for this student.');
        }

        // Default to admission number when no custom password is supplied
        $newPassword = $request->filled('password')
            ? $request->password
            : $student->admission_number;

        $student->user->update(['password' => Hash::make($newPassword)]);

        return redirect()
            ->route('tenant.students.show', $student)
            ->with('success', 'Password reset to "'.$newPassword.'" successfully.');
    }

    // Ajax — get sections for a class
    public function getSections(SchoolClass $class)
    {
        $sections = Section::where('class_id', $class->id)
            ->orderBy('order')
            ->get(['id', 'name']);

        return response()->json($sections);
    }

    public function updateStatus(Request $request, StudentProfile $student)
    {
        $this->requireAdmin();

        $request->validate([
            'status' => ['required', 'in:active,inactive,graduated,transferred,dropped'],
        ]);

        $student->update(['status' => $request->status]);

        // Sync user is_active
        $student->user->update([
            'is_active' => $request->status === 'active',
        ]);

        return redirect()
            ->route('tenant.students.show', $student)
            ->with('success', 'Student status updated to "'.ucfirst($request->status).'".');
    }

    public function verifyDocument(StudentDocument $document)
    {
        $this->requireAdmin();

        $document->update(['is_verified' => ! $document->is_verified]);

        return redirect()
            ->back()
            ->with('success', $document->is_verified
                ? 'Document verified successfully.'
                : 'Document verification removed.');
    }

    // ─── Export ──────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_view_students');

        $format = $request->input('format', 'xlsx');

        $export = new StudentsExport(
            classId: $request->input('class_id'),
            sectionId: $request->input('section_id'),
            status: $request->input('status'),
            search: $request->input('search'),
        );

        $filename = 'students-'.now()->format('Y-m-d');

        return match ($format) {
            'csv' => Excel::download($export, $filename.'.csv', \Maatwebsite\Excel\Excel::CSV),
            'xlsx' => Excel::download($export, $filename.'.xlsx', \Maatwebsite\Excel\Excel::XLSX),
            default => Excel::download($export, $filename.'.xlsx', \Maatwebsite\Excel\Excel::XLSX),
        };
    }

    // ─── Download Sample Template ─────────────────────────────────────────────

    public function importTemplate()
    {
        $this->requireAdmin();

        return Excel::download(new StudentImportTemplateExport, 'students-import-template.xlsx');
    }

    // ─── Import ───────────────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new StudentsImport;

        Excel::import($import, $request->file('file'));

        $importedCount = count($import->imported);
        $skippedCount = count($import->skipped);
        $errorCount = count($import->errors);

        $message = "Import complete: {$importedCount} imported";
        if ($skippedCount) {
            $message .= ", {$skippedCount} skipped (already exist)";
        }
        if ($errorCount) {
            $message .= ", {$errorCount} errors";
        }

        $sessionData = [
            'success' => $message,
            'import_imported' => $import->imported,
            'import_skipped' => $import->skipped,
            'import_errors' => $import->errors,
        ];

        return redirect()
            ->route('tenant.students.index')
            ->with($sessionData);
    }
}
