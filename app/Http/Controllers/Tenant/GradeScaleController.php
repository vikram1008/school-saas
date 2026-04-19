<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GradeScale;
use Illuminate\Http\Request;

class GradeScaleController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::active();
        $scales     = GradeScale::where('academic_year_id', $activeYear?->id)
            ->orderByDesc('min_percentage')
            ->get();

        return view('tenant.results.grade-scales.index', compact('scales', 'activeYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade'          => ['required', 'string', 'max:10'],
            'min_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_point'    => ['nullable', 'numeric', 'min:0'],
        ]);

        GradeScale::create([
            'academic_year_id' => $request->academic_year_id,
            'grade'            => strtoupper($request->grade),
            'min_percentage'   => $request->min_percentage,
            'max_percentage'   => $request->max_percentage,
            'grade_point'      => $request->grade_point ?? 0,
            'description'      => $request->description,
            'description_hi'   => $request->description_hi,
            'color'            => $request->color ?? 'secondary',
        ]);

        return redirect()
            ->route('tenant.results.grade-scales.index')
            ->with('success', 'Grade added.');
    }

    public function destroy(GradeScale $gradeScale)
    {
        $gradeScale->delete();
        return redirect()
            ->route('tenant.results.grade-scales.index')
            ->with('success', 'Grade deleted.');
    }

    public function applyDefault(Request $request)
    {
        $activeYear = AcademicYear::active();
        if (!$activeYear) return back()->withErrors(['No active academic year.']);

        // Clear existing
        GradeScale::where('academic_year_id', $activeYear->id)->delete();

        foreach (GradeScale::defaultCbseScales() as $scale) {
            GradeScale::create([
                'academic_year_id' => $activeYear->id,
                'grade'            => $scale['grade'],
                'min_percentage'   => $scale['min'],
                'max_percentage'   => $scale['max'],
                'grade_point'      => $scale['point'],
                'description'      => $scale['desc'],
                'description_hi'   => $scale['desc_hi'],
                'color'            => $scale['color'],
            ]);
        }

        return redirect()
            ->route('tenant.results.grade-scales.index')
            ->with('success', 'CBSE grade scale applied.');
    }
}