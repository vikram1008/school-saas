<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeDemand;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\StudentFeeOverride;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeService
{
    /**
     * Generate fee demands for all active students
     * Called by scheduler monthly.
     */
    public function generateMonthlyDemands(): array
    {
        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            return ['error' => 'No active academic year found.'];
        }

        $students   = StudentProfile::where('status', 'active')
            ->whereNotNull('class_id')
            ->get();

        $now        = Carbon::now();
        $generated  = 0;
        $skipped    = 0;

        foreach ($students as $student) {
            $structures = FeeStructure::with('feeHead')
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $student->class_id)
                ->where('is_active', true)
                ->get();

            foreach ($structures as $structure) {
                $feeHead = $structure->feeHead;

                // Only generate for frequencies applicable this month
                if (!$this->isDueThisMonth($feeHead->frequency, $now)) {
                    $skipped++;
                    continue;
                }

                $periodLabel = $this->getPeriodLabel($feeHead->frequency, $now);
                $periodDates = $this->getPeriodDates($feeHead->frequency, $now);

                // Check if demand already exists
                $exists = FeeDemand::where('student_profile_id', $student->id)
                    ->where('fee_head_id', $feeHead->id)
                    ->where('period_label', $periodLabel)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Check student override
                $override = StudentFeeOverride::where('student_profile_id', $student->id)
                    ->where('fee_head_id', $feeHead->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

                $amount   = $override ? $override->amount : $structure->amount;
                $dueDate  = Carbon::createFromDay($structure->due_day);

                FeeDemand::create([
                    'student_profile_id' => $student->id,
                    'fee_head_id'        => $feeHead->id,
                    'academic_year_id'   => $activeYear->id,
                    'period_label'       => $periodLabel,
                    'period_start'       => $periodDates['start'],
                    'period_end'         => $periodDates['end'],
                    'due_date'           => $dueDate,
                    'amount_due'         => $amount,
                    'amount_paid'        => 0,
                    'balance'            => $amount,
                    'fine_amount'        => 0,
                    'status'             => 'pending',
                ]);

                $generated++;
            }
        }

        return compact('generated', 'skipped');
    }

    /**
     * Collect fee payment against selected demands.
     */
    public function collectPayment(
        StudentProfile $student,
        array $demandIds,
        array $amounts,
        string $paymentMode,
        string $collectionDate,
        int $collectedBy,
        ?string $reference = null,
        ?string $notes = null
    ): \App\Models\FeeCollection {

        DB::beginTransaction();

        try {
            $totalAmount = array_sum($amounts);

            $collection = \App\Models\FeeCollection::create([
                'receipt_number'     => \App\Models\FeeCollection::generateReceiptNumber(),
                'student_profile_id' => $student->id,
                'collected_by'       => $collectedBy,
                'payment_mode'       => $paymentMode,
                'payment_reference'  => $reference,
                'total_amount'       => $totalAmount,
                'collection_date'    => $collectionDate,
                'notes'              => $notes,
            ]);

            foreach ($demandIds as $index => $demandId) {
                $demand      = FeeDemand::findOrFail($demandId);
                $amountPaid  = $amounts[$index] ?? 0;

                if ($amountPaid <= 0) continue;

                \App\Models\FeeCollectionItem::create([
                    'fee_collection_id' => $collection->id,
                    'fee_demand_id'     => $demandId,
                    'amount_paid'       => $amountPaid,
                ]);

                // Update demand
                $newPaid    = $demand->amount_paid + $amountPaid;
                $newBalance = max(0, $demand->amount_due - $newPaid);
                $newStatus  = $newBalance <= 0 ? 'paid' : 'partial';

                $demand->update([
                    'amount_paid' => $newPaid,
                    'balance'     => $newBalance,
                    'status'      => $newStatus,
                ]);
            }

            DB::commit();
            return $collection;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Waive a fee demand.
     */
    public function waivedDemand(FeeDemand $demand, string $reason): void
    {
        $demand->update([
            'status'       => 'waived',
            'balance'      => 0,
            'waive_reason' => $reason,
        ]);
    }

    /**
     * Get student fee summary for a given academic year.
     */
    public function getStudentFeeSummary(StudentProfile $student, int $academicYearId): array
    {
        $demands = FeeDemand::with('feeHead')
            ->where('student_profile_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->get();

        return [
            'total_due'     => $demands->sum('amount_due'),
            'total_paid'    => $demands->sum('amount_paid'),
            'total_balance' => $demands->sum('balance'),
            'total_fine'    => $demands->sum('fine_amount'),
            'paid_count'    => $demands->where('status', 'paid')->count(),
            'pending_count' => $demands->whereIn('status', ['pending', 'partial', 'overdue'])->count(),
            'demands'       => $demands,
        ];
    }

    /**
     * Check if fee head is due this month.
     */
    private function isDueThisMonth(string $frequency, Carbon $now): bool
    {
        return match($frequency) {
            'monthly'     => true,
            'quarterly'   => in_array($now->month, [4, 7, 10, 1]),
            'half_yearly' => in_array($now->month, [4, 10]),
            'yearly'      => $now->month === 4, // April = start of Indian academic year
            'one_time'    => false, // Generated manually
            default       => false,
        };
    }

    /**
     * Get human-readable period label.
     */
    public function getPeriodLabel(string $frequency, Carbon $now): string
    {
        return match($frequency) {
            'monthly'     => $now->format('F Y'),           // "April 2025"
            'quarterly'   => 'Q' . ceil($now->month / 3) . ' ' . $now->year,
            'half_yearly' => ($now->month <= 6 ? 'H1 ' : 'H2 ') . $now->year,
            'yearly'      => $now->year . '-' . ($now->year + 1),
            'one_time'    => 'One Time ' . $now->year,
            default       => $now->format('F Y'),
        };
    }

    /**
     * Get period start and end dates.
     */
    private function getPeriodDates(string $frequency, Carbon $now): array
    {
        return match($frequency) {
            'monthly' => [
                'start' => $now->copy()->startOfMonth(),
                'end'   => $now->copy()->endOfMonth(),
            ],
            'quarterly' => [
                'start' => $now->copy()->firstOfQuarter(),
                'end'   => $now->copy()->lastOfQuarter(),
            ],
            'half_yearly' => [
                'start' => $now->month <= 6
                    ? $now->copy()->month(1)->startOfMonth()
                    : $now->copy()->month(7)->startOfMonth(),
                'end'   => $now->month <= 6
                    ? $now->copy()->month(6)->endOfMonth()
                    : $now->copy()->month(12)->endOfMonth(),
            ],
            'yearly' => [
                'start' => $now->copy()->month(4)->startOfMonth(),
                'end'   => $now->copy()->addYear()->month(3)->endOfMonth(),
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end'   => $now->copy()->endOfMonth(),
            ],
        };
    }
}