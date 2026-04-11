<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();
        $feeHeads   = FeeHead::where('is_active', true)->orderBy('sort_order')->get();

        // Load structures for active year grouped by class
        $structures = $activeYear
            ? FeeStructure::with(['feeHead', 'class'])
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->groupBy('class_id')
            : collect();

        return view('tenant.fees.structures.index', compact(
            'activeYear', 'classes', 'feeHeads', 'structures'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id'         => ['required', 'exists:classes,id'],
            'fee_head_id'      => ['required', 'exists:fee_heads,id'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'due_day'          => ['required', 'integer', 'min:1', 'max:28'],
        ]);

        FeeStructure::updateOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'class_id'         => $request->class_id,
                'fee_head_id'      => $request->fee_head_id,
            ],
            [
                'amount'    => $request->amount,
                'due_day'   => $request->due_day,
                'is_active' => true,
                'notes'     => $request->notes,
            ]
        );

        return redirect()
            ->route('tenant.fees.structures.index')
            ->with('success', 'Fee structure saved.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        return redirect()
            ->route('tenant.fees.structures.index')
            ->with('success', 'Fee structure removed.');
    }
}