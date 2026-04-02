<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::withCount('classes')
            ->latest()
            ->get();

        $activeYear = AcademicYear::active();

        return view('tenant.academic-years.index', compact('years', 'activeYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:20', 'unique:academic_years,name'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $year = AcademicYear::create($validated);

        // Auto-activate if first year
        if (AcademicYear::count() === 1) {
            $year->activate();
        }

        return redirect()
            ->route('tenant.academic-years.index')
            ->with('success', "Academic year \"{$validated['name']}\" created successfully.");
    }

    public function activate(AcademicYear $academicYear)
    {
        $academicYear->activate();

        return redirect()
            ->route('tenant.academic-years.index')
            ->with('success', "\"{$academicYear->name}\" is now the active academic year.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->withErrors(['error' => 'Cannot delete the active academic year.']);
        }

        if ($academicYear->classes()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a year that has classes. Delete classes first.']);
        }

        $academicYear->delete();

        return redirect()
            ->route('tenant.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }
}