<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::active();

        $classes = $activeYear
            ? SchoolClass::with(['sections.classTeacher', 'classTeacher'])
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('order')
                ->get()
            : collect();

        $years   = AcademicYear::orderBy('is_active', 'desc')->get();
        $teachers = TenantUser::whereIn('role', ['teacher', 'school_admin'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.classes.index', compact(
            'classes', 'activeYear', 'years', 'teachers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name'             => ['required', 'string', 'max:100'],
            'has_sections'     => ['required', 'boolean'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity'         => ['nullable', 'integer', 'min:1'],
            'description'      => ['nullable', 'string', 'max:255'],
        ]);

        // Check duplicate name in same year
        $exists = SchoolClass::where('academic_year_id', $validated['academic_year_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'A class with this name already exists in the selected academic year.'
            ]);
        }

        $validated['order'] = SchoolClass::nextOrder($validated['academic_year_id']);

        // Clear class_teacher_id if has_sections — teacher assigned per section
        if ($validated['has_sections']) {
            $validated['class_teacher_id'] = null;
        }

        SchoolClass::create($validated);

        return redirect()
            ->route('tenant.classes.index')
            ->with('success', "Class \"{$validated['name']}\" created successfully.");
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity'         => ['nullable', 'integer', 'min:1'],
            'description'      => ['nullable', 'string', 'max:255'],
        ]);

        if ($class->has_sections) {
            $validated['class_teacher_id'] = null;
        }

        $class->update($validated);

        return redirect()
            ->route('tenant.classes.index')
            ->with('success', "Class \"{$class->name}\" updated.");
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete a class that has students assigned.'
            ]);
        }

        $class->delete();

        return redirect()
            ->route('tenant.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    // Section CRUD
    public function storeSection(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:10'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity'         => ['nullable', 'integer', 'min:1'],
        ]);

        $exists = Section::where('class_id', $class->id)
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => "Section \"{$validated['name']}\" already exists in {$class->name}."
            ]);
        }

        $validated['class_id'] = $class->id;
        $validated['order']    = (Section::where('class_id', $class->id)->max('order') ?? 0) + 1;

        Section::create($validated);

        return redirect()
            ->route('tenant.classes.index')
            ->with('success', "Section \"{$validated['name']}\" added to {$class->name}.");
    }

    public function destroySection(SchoolClass $class, Section $section)
    {
        if ($section->students()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete a section that has students assigned.'
            ]);
        }

        $section->delete();

        return redirect()
            ->route('tenant.classes.index')
            ->with('success', 'Section deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders'   => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($request->orders as $classId => $order) {
            SchoolClass::where('id', $classId)->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}