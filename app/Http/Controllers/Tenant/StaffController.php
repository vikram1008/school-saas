<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StaffAddress;
use App\Models\StaffDocument;
use App\Models\StaffProfile;
use App\Models\StaffSubjectAssignment;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffProfile::with(['user'])
            ->orderBy('first_name');

        if ($request->filled('staff_type')) {
            $query->where('staff_type', $request->staff_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $staff = $query->paginate(20)->withQueryString();

        return view('tenant.staff.index', compact('staff'));
    }

    public function create()
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        return view('tenant.staff.create', compact('activeYear', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => ['required', 'string', 'unique:staff_profiles,employee_code'],
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'gender'        => ['required', 'in:male,female,other'],
            'staff_type'    => ['required', 'in:teaching,non_teaching,administrative'],
            'joining_date'  => ['nullable', 'date'],
            'photo'         => ['nullable', 'image', 'max:2048'],
            'aadhaar_number'=> ['nullable', 'digits:12'],
            'pan_number'    => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'salary'        => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            // Create login account
            $email = $request->email
                ?? Str::slug($request->first_name . $request->last_name)
                . '@' . tenant('id') . '.staff';

            $user = TenantUser::create([
                'name'      => $request->first_name . ' ' . $request->last_name,
                'email'     => $email,
                'password'  => Hash::make($request->employee_code),
                'role'      => $this->mapRole($request->staff_type, $request->designation),
                'is_active' => true,
            ]);

            // Handle photo
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')
                    ->store('staff/photos', 'public');
            }

            // Create staff profile
            $staff = StaffProfile::create([
                'user_id'                  => $user->id,
                'staff_type'               => $request->staff_type,
                'employee_code'            => $request->employee_code,
                'first_name'               => $request->first_name,
                'first_name_hi'            => $request->first_name_hi,
                'last_name'                => $request->last_name,
                'last_name_hi'             => $request->last_name_hi,
                'date_of_birth'            => $request->date_of_birth,
                'gender'                   => $request->gender,
                'blood_group'              => $request->blood_group,
                'photo'                    => $photoPath,
                'phone'                    => $request->phone,
                'whatsapp'                 => $request->whatsapp,
                'email'                    => $request->email,
                'aadhaar_number'           => $request->aadhaar_number,
                'pan_number'               => $request->pan_number,
                'id_proof_type'            => $request->id_proof_type,
                'id_proof_number'          => $request->id_proof_number,
                'designation'              => $request->designation,
                'designation_hi'           => $request->designation_hi,
                'department'               => $request->department,
                'department_hi'            => $request->department_hi,
                'qualification'            => $request->qualification,
                'qualification_hi'         => $request->qualification_hi,
                'experience_years'         => $request->experience_years ?? 0,
                'joining_date'             => $request->joining_date,
                'employment_type'          => $request->employment_type ?? 'full_time',
                'status'                   => 'active',
                'salary'                   => $request->salary,
                'emergency_contact_name'   => $request->emergency_contact_name,
                'emergency_contact_phone'  => $request->emergency_contact_phone,
            ]);

            // Address
            StaffAddress::create([
                'staff_profile_id'     => $staff->id,
                'perm_house_no'        => $request->perm_house_no,
                'perm_house_no_hi'     => $request->perm_house_no_hi,
                'perm_street'          => $request->perm_street,
                'perm_street_hi'       => $request->perm_street_hi,
                'perm_village_city'    => $request->perm_village_city,
                'perm_village_city_hi' => $request->perm_village_city_hi,
                'perm_tehsil'          => $request->perm_tehsil,
                'perm_tehsil_hi'       => $request->perm_tehsil_hi,
                'perm_district'        => $request->perm_district,
                'perm_district_hi'     => $request->perm_district_hi,
                'perm_state'           => $request->perm_state,
                'perm_state_hi'        => $request->perm_state_hi,
                'perm_pincode'         => $request->perm_pincode,
                'same_as_permanent'    => $request->boolean('same_as_permanent'),
                'curr_house_no'        => $request->curr_house_no,
                'curr_street'          => $request->curr_street,
                'curr_village_city'    => $request->curr_village_city,
                'curr_tehsil'          => $request->curr_tehsil,
                'curr_district'        => $request->curr_district,
                'curr_state'           => $request->curr_state,
                'curr_pincode'         => $request->curr_pincode,
            ]);

            // Subject assignments (teaching staff only)
            if ($request->staff_type === 'teaching' && $request->has('assignments')) {
                foreach ($request->assignments as $assignment) {
                    if (!empty($assignment['class_id']) && !empty($assignment['subject_name'])) {
                        StaffSubjectAssignment::create([
                            'staff_profile_id' => $staff->id,
                            'class_id'         => $assignment['class_id'],
                            'section_id'       => $assignment['section_id'] ?? null,
                            'subject_name'     => $assignment['subject_name'],
                            'subject_name_hi'  => $assignment['subject_name_hi'] ?? null,
                        ]);
                    }
                }
            }

            // Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if ($file) {
                        $path = $file->store('staff/documents', 'public');
                        StaffDocument::create([
                            'staff_profile_id' => $staff->id,
                            'document_type'    => $type,
                            'file_path'        => $path,
                            'original_name'    => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('tenant.staff.show', $staff)
                ->with('success', "Staff \"{$staff->full_name}\" added! Login: {$user->email} / Password: {$request->employee_code}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Failed to save: ' . $e->getMessage()
            ]);
        }
    }

    public function show(StaffProfile $staff)
    {
        $staff->load([
            'user', 'address', 'documents',
            'subjectAssignments.class',
            'subjectAssignments.section',
        ]);

        return view('tenant.staff.show', compact('staff'));
    }

    public function edit(StaffProfile $staff)
    {
        $staff->load(['address', 'documents', 'subjectAssignments']);

        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        return view('tenant.staff.edit', compact('staff', 'activeYear', 'classes'));
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'gender'        => ['required', 'in:male,female,other'],
            'photo'         => ['nullable', 'image', 'max:2048'],
            'aadhaar_number'=> ['nullable', 'digits:12'],
            'pan_number'    => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'salary'        => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('photo')) {
                if ($staff->photo) {
                    Storage::disk('public')->delete($staff->photo);
                }
                $staff->photo = $request->file('photo')
                    ->store('staff/photos', 'public');
            }

            $staff->update($request->except(['photo', 'documents', 'assignments', '_token', '_method']));

            $staff->address()->updateOrCreate(
                ['staff_profile_id' => $staff->id],
                $request->only([
                    'perm_house_no', 'perm_house_no_hi',
                    'perm_street', 'perm_street_hi',
                    'perm_village_city', 'perm_village_city_hi',
                    'perm_tehsil', 'perm_tehsil_hi',
                    'perm_district', 'perm_district_hi',
                    'perm_state', 'perm_state_hi',
                    'perm_pincode', 'same_as_permanent',
                    'curr_house_no', 'curr_street',
                    'curr_village_city', 'curr_tehsil',
                    'curr_district', 'curr_state', 'curr_pincode',
                ])
            );

            // Update subject assignments
            if ($staff->staff_type === 'teaching') {
                $staff->subjectAssignments()->delete();
                if ($request->has('assignments')) {
                    foreach ($request->assignments as $assignment) {
                        if (!empty($assignment['class_id']) && !empty($assignment['subject_name'])) {
                            StaffSubjectAssignment::create([
                                'staff_profile_id' => $staff->id,
                                'class_id'         => $assignment['class_id'],
                                'section_id'       => $assignment['section_id'] ?? null,
                                'subject_name'     => $assignment['subject_name'],
                                'subject_name_hi'  => $assignment['subject_name_hi'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if ($file) {
                        $path = $file->store('staff/documents', 'public');
                        StaffDocument::updateOrCreate(
                            ['staff_profile_id' => $staff->id, 'document_type' => $type],
                            ['file_path' => $path, 'original_name' => $file->getClientOriginalName()]
                        );
                    }
                }
            }

            // Update user name
            $staff->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.staff.show', $staff)
                ->with('success', 'Staff details updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Failed to update: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(StaffProfile $staff)
    {
        $staff->user->update(['is_active' => false]);
        $staff->update(['status' => 'inactive']);

        return redirect()
            ->route('tenant.staff.index')
            ->with('success', 'Staff member deactivated successfully.');
    }

    public function updateStatus(Request $request, StaffProfile $staff)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive,on_leave,resigned,terminated'],
        ]);

        $staff->update(['status' => $request->status]);
        $staff->user->update([
            'is_active' => $request->status === 'active',
        ]);

        return redirect()
            ->route('tenant.staff.show', $staff)
            ->with('success', 'Status updated to "' . ucfirst($request->status) . '".');
    }

    public function verifyDocument(StaffDocument $document)
    {
        $document->update(['is_verified' => !$document->is_verified]);

        return back()->with('success', $document->is_verified
            ? 'Document verified.' : 'Verification removed.');
    }

    private function mapRole(string $staffType, ?string $designation): string
    {
        if ($staffType === 'administrative') {
            return match($designation) {
                'principal', 'vice_principal' => 'school_admin',
                default                        => 'teacher',
            };
        }
        return match($staffType) {
            'teaching'     => 'teacher',
            'non_teaching' => 'accountant',
            default        => 'teacher',
        };
    }
}