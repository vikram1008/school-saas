<?php

namespace App\Services;

use App\Models\ParentProfile;
use App\Models\ParentStudentLink;
use App\Models\StudentProfile;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentService
{
    /**
     * Auto-create parent account when student is admitted.
     * Called from StudentController@store
     */
    public function createFromStudent(StudentProfile $student): ?ParentProfile
    {
        $family = $student->familyDetail;
        if (!$family) return null;

        // Use father's mobile as primary login
        $mobile = $family->father_mobile
            ?? $family->mother_mobile
            ?? $family->guardian_mobile;

        if (!$mobile) return null;

        // Check if parent user already exists (same mobile = same family)
        $existingUser = TenantUser::where('email', $mobile . '@parent.local')->first();

        if ($existingUser) {
            // Link existing parent to this student
            $parent = ParentProfile::where('user_id', $existingUser->id)->first();
            if ($parent) {
                $this->linkStudentToParent($parent, $student, 'father');
                return $parent;
            }
        }

        // Create new parent user account
        // Default password = student's admission number
        $user = TenantUser::create([
            'name'      => $family->father_name ?? $family->guardian_name ?? 'Parent',
            'email'     => $mobile . '@parent.local',
            'password'  => Hash::make($student->admission_number),
            'role'      => 'parent',
            'is_active' => true,
        ]);

        // Create parent profile
        $parent = ParentProfile::create([
            'user_id'    => $user->id,
            'first_name' => $family->father_name
                ? explode(' ', $family->father_name)[0]
                : ($family->guardian_name ? explode(' ', $family->guardian_name)[0] : 'Parent'),
            'first_name_hi' => $family->father_name_hi
                ? explode(' ', $family->father_name_hi)[0]
                : null,
            'last_name'  => $family->father_name
                ? (explode(' ', $family->father_name, 2)[1] ?? '')
                : '',
            'last_name_hi' => $family->father_name_hi
                ? (explode(' ', $family->father_name_hi, 2)[1] ?? null)
                : null,
            'relation'   => 'father',
            'phone'      => $family->father_mobile,
            'mobile'     => $mobile,
            'occupation' => $family->father_occupation,
            'occupation_hi' => $family->father_occupation_hi,
            'is_active'  => true,
        ]);

        // Link to student
        $this->linkStudentToParent($parent, $student, 'father');

        return $parent;
    }

    public function linkStudentToParent(
        ParentProfile $parent,
        StudentProfile $student,
        string $relationship = 'father'
    ): void {
        ParentStudentLink::updateOrCreate(
            [
                'parent_profile_id'  => $parent->id,
                'student_profile_id' => $student->id,
            ],
            [
                'relationship' => $relationship,
                'is_primary'   => true,
            ]
        );

        // Update student's parent_profile_id
        $student->update(['parent_profile_id' => $parent->id]);
    }

    /**
     * Reset parent password to student's admission number
     */
    public function resetPassword(ParentProfile $parent, string $newPassword): void
    {
        $parent->user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}