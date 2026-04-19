<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\ParentStudentLink;
use App\Models\StudentProfile;
use App\Services\ParentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function __construct(protected ParentService $parentService) {}

    public function index(Request $request)
    {
        $query = ParentProfile::with(['user', 'students'])
            ->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $parents = $query->paginate(20)->withQueryString();

        return view('tenant.parents.index', compact('parents'));
    }

    public function show(ParentProfile $parent)
    {
        $parent->load(['user', 'students.class', 'students.section']);
        return view('tenant.parents.show', compact('parent'));
    }

    public function edit(ParentProfile $parent)
    {
        $parent->load(['user', 'students']);
        $allStudents = StudentProfile::where('status', 'active')
            ->orderBy('first_name')->get();
        return view('tenant.parents.edit', compact('parent', 'allStudents'));
    }

    public function update(Request $request, ParentProfile $parent)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'mobile'     => ['required', 'string', 'max:15'],
        ]);

        $parent->update($request->only([
            'first_name', 'first_name_hi',
            'last_name', 'last_name_hi',
            'relation', 'phone', 'mobile',
            'alternate_phone', 'occupation', 'occupation_hi',
            'address', 'city', 'state', 'pincode',
            'is_active',
        ]));

        // Update user name
        $parent->user?->update([
            'name'     => $request->first_name . ' ' . $request->last_name,
            'is_active'=> $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('tenant.parents.show', $parent)
            ->with('success', 'Parent details updated.');
    }

    public function resetPassword(Request $request, ParentProfile $parent)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $this->parentService->resetPassword($parent, $request->password);

        return redirect()
            ->route('tenant.parents.show', $parent)
            ->with('success', 'Password reset successfully.');
    }

    public function linkStudent(Request $request, ParentProfile $parent)
    {
        $request->validate([
            'student_profile_id' => ['required', 'exists:student_profiles,id'],
            'relationship'       => ['required', 'in:father,mother,guardian,other'],
        ]);

        $this->parentService->linkStudentToParent(
            $parent,
            StudentProfile::findOrFail($request->student_profile_id),
            $request->relationship
        );

        return redirect()
            ->route('tenant.parents.show', $parent)
            ->with('success', 'Student linked successfully.');
    }

    public function unlinkStudent(ParentProfile $parent, StudentProfile $student)
    {
        ParentStudentLink::where('parent_profile_id', $parent->id)
            ->where('student_profile_id', $student->id)
            ->delete();

        return redirect()
            ->route('tenant.parents.show', $parent)
            ->with('success', 'Student unlinked.');
    }
}