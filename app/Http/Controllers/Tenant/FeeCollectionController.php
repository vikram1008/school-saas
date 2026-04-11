<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeDemand;
use App\Models\FeeCollection;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeCollectionController extends Controller
{
    public function __construct(protected FeeService $feeService) {}

    // Fee collection dashboard
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        // Recent collections
        $recentCollections = FeeCollection::with(['student', 'items.demand.feeHead'])
            ->latest()
            ->take(10)
            ->get();

        // Summary stats
        $stats = [
            'total_collected_today'  => FeeCollection::whereDate('collection_date', today())->sum('total_amount'),
            'total_collected_month'  => FeeCollection::whereMonth('collection_date', now()->month)->sum('total_amount'),
            'pending_demands'        => FeeDemand::whereIn('status', ['pending', 'partial', 'overdue'])->count(),
            'overdue_demands'        => FeeDemand::where('status', 'overdue')
                ->orWhere(fn($q) => $q->where('status', 'pending')->where('due_date', '<', today()))
                ->count(),
        ];

        return view('tenant.fees.collections.index', compact(
            'activeYear', 'classes', 'recentCollections', 'stats'
        ));
    }

    // Show student fee ledger
    public function studentLedger(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:student_profiles,id'],
        ]);

        $student    = StudentProfile::with(['class', 'section'])->findOrFail($request->student_id);
        $activeYear = AcademicYear::active();
        $summary    = $this->feeService->getStudentFeeSummary($student, $activeYear?->id);
        $collections = FeeCollection::with(['items.demand.feeHead'])
            ->where('student_profile_id', $student->id)
            ->latest()
            ->get();

        return view('tenant.fees.collections.ledger', compact(
            'student', 'activeYear', 'summary', 'collections'
        ));
    }

    // Collect fee form
    public function create(Request $request)
    {
        $student = null;
        $demands = collect();
        $activeYear = AcademicYear::active();

        if ($request->filled('student_id')) {
            $student = StudentProfile::with(['class', 'section'])
                ->findOrFail($request->student_id);

            $demands = FeeDemand::with('feeHead')
                ->where('student_profile_id', $student->id)
                ->where('academic_year_id', $activeYear?->id)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('due_date')
                ->get();
        }

        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        return view('tenant.fees.collections.create', compact(
            'student', 'demands', 'classes', 'activeYear'
        ));
    }

    // Process payment
    public function store(Request $request)
    {
        $request->validate([
            'student_id'     => ['required', 'exists:student_profiles,id'],
            'payment_mode'   => ['required', 'in:cash,upi,bank_transfer,cheque,dd,online'],
            'collection_date'=> ['required', 'date'],
            'demand_ids'     => ['required', 'array', 'min:1'],
            'demand_ids.*'   => ['exists:fee_demands,id'],
            'amounts'        => ['required', 'array'],
            'amounts.*'      => ['numeric', 'min:0.01'],
        ]);

        $student = StudentProfile::findOrFail($request->student_id);

        $collection = $this->feeService->collectPayment(
            student:        $student,
            demandIds:      $request->demand_ids,
            amounts:        $request->amounts,
            paymentMode:    $request->payment_mode,
            collectionDate: $request->collection_date,
            collectedBy:    Auth::guard('tenant')->id(),
            reference:      $request->payment_reference,
            notes:          $request->notes,
        );

        return redirect()
            ->route('tenant.fees.receipt', $collection)
            ->with('success', "Payment collected. Receipt: {$collection->receipt_number}");
    }

    // Receipt view
    public function receipt(FeeCollection $feeCollection)
    {
        $feeCollection->load([
            'student.class',
            'student.section',
            'student.familyDetail',
            'items.demand.feeHead',
            'collectedBy',
        ]);

        return view('tenant.fees.collections.receipt', compact('feeCollection'));
    }

    // Generate demands manually
    public function generateDemands()
    {
        $result = $this->feeService->generateMonthlyDemands();

        // Handle error case (e.g. no active academic year)
        if (isset($result['error'])) {
            return redirect()
                ->route('tenant.fees.collections.index')
                ->withErrors(['error' => $result['error']]);
        }

        return redirect()
            ->route('tenant.fees.collections.index')
            ->with('success', "Generated: {$result['generated']} demands. Skipped: {$result['skipped']}.");
    }

    // Waive demand
    public function waiveDemand(Request $request, FeeDemand $demand)
    {
        $request->validate([
            'waive_reason' => ['required', 'string', 'max:255'],
        ]);

        $this->feeService->waivedDemand($demand, $request->waive_reason);

        return back()->with('success', 'Fee demand waived.');
    }

    // Ajax — search students
    public function searchStudents(Request $request)
    {
        $search   = $request->q;
        $students = StudentProfile::with(['class', 'section'])
            ->where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            })
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'id'   => $s->id,
                'text' => $s->full_name . ' (' . $s->admission_number . ') — ' . $s->class_section,
            ]);

        return response()->json(['results' => $students]);
    }
}