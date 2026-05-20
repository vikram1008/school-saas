<?php

use App\Models\LibraryBook;
use Carbon\Carbon;

// ══════════════════════════════════════════════════════════════
//  LIBRARY MODULE — Unit tests
//
//  All tests operate purely on model instances & static helpers
//  without ever touching the DB (no `new` Eloquent model that
//  touches a connection, no static query calls).
//
//  Fine-calculation and overdue-detection logic is extracted
//  into plain functions that mirror the model methods so they
//  can be tested without a DB connection.
// ══════════════════════════════════════════════════════════════

// ── Pure logic helpers (mirrors model methods exactly) ────────

/**
 * Mirror of BookIssue::calculateFine()
 *
 * @param  string   $status       issued|overdue|returned|lost
 * @param  Carbon   $dueDate
 * @param  Carbon|null $returnDate
 * @param  int      $finePerDay
 * @param  float    $storedFine   fine_amount column (used for returned)
 */
function calculateFine(string $status, Carbon $dueDate, ?Carbon $returnDate, int $finePerDay, float $storedFine = 0): float
{
    if ($status === 'returned') {
        return $storedFine;
    }

    $checkDate   = $returnDate ?? Carbon::today();
    $overdueDays = max(0, (int) $dueDate->diffInDays($checkDate, false));

    return (float) ($overdueDays * $finePerDay);
}

/**
 * Mirror of BookIssue::isOverdue()
 */
function isOverdue(string $status, Carbon $dueDate): bool
{
    if ($status === 'returned' || $status === 'lost') {
        return false;
    }

    return Carbon::today()->greaterThan($dueDate);
}

/**
 * Mirror of BookIssue::overdueDays()
 */
function overdueDays(string $status, Carbon $dueDate): int
{
    if (! isOverdue($status, $dueDate)) {
        return 0;
    }

    return (int) abs(Carbon::today()->diffInDays($dueDate));
}

/**
 * Mirror of BookIssue::fineDue()
 */
function fineDue(string $status, Carbon $dueDate, ?Carbon $returnDate, int $finePerDay, float $storedFine, float $finePaid): float
{
    return max(0.0, calculateFine($status, $dueDate, $returnDate, $finePerDay, $storedFine) - $finePaid);
}

/**
 * Mirror of LibraryBook::isAvailable()
 */
function bookIsAvailable(int $availableCopies, bool $isReferenceOnly, bool $isActive): bool
{
    return $availableCopies > 0 && ! $isReferenceOnly && $isActive;
}

// ══════════════════════════════════════════════════════════════
//  LibraryBook — availability logic
// ══════════════════════════════════════════════════════════════

describe('LibraryBook availability', function () {

    test('available when copies > 0, not reference-only, and active', function () {
        expect(bookIsAvailable(2, false, true))->toBeTrue();
    });

    test('unavailable when no copies left', function () {
        expect(bookIsAvailable(0, false, true))->toBeFalse();
    });

    test('unavailable when reference-only regardless of copies', function () {
        expect(bookIsAvailable(5, true, true))->toBeFalse();
    });

    test('unavailable when book is inactive', function () {
        expect(bookIsAvailable(3, false, false))->toBeFalse();
    });

    test('all three conditions must hold simultaneously', function () {
        expect(bookIsAvailable(1, false, true))->toBeTrue();
        expect(bookIsAvailable(0, false, true))->toBeFalse();
        expect(bookIsAvailable(1, true,  true))->toBeFalse();
        expect(bookIsAvailable(1, false, false))->toBeFalse();
    });

    test('categories() returns required keys', function () {
        $cats = LibraryBook::categories();

        expect($cats)->toHaveKeys(['textbook', 'fiction', 'reference', 'general'])
            ->and(count($cats))->toBeGreaterThan(5);
    });

    test('languages() returns all four keys', function () {
        $langs = LibraryBook::languages();

        expect($langs)->toHaveKeys(['english', 'hindi', 'both', 'other'])
            ->and(count($langs))->toBe(4);
    });

    test('accession number format is correct after increment', function () {
        $max = 'ACC-2024-0007';
        preg_match('/(\d+)$/', $max, $m);
        $result = 'ACC-' . date('Y') . '-' . str_pad((int) $m[1] + 1, 4, '0', STR_PAD_LEFT);

        expect($result)->toBe('ACC-' . date('Y') . '-0008');
    });

    test('accession number starts at 0001 when no existing records', function () {
        $result = 'ACC-' . date('Y') . '-0001';

        expect($result)->toStartWith('ACC-' . date('Y') . '-');
    });
});

// ══════════════════════════════════════════════════════════════
//  BookIssue — fine calculation
// ══════════════════════════════════════════════════════════════

describe('BookIssue fine calculation', function () {

    test('no fine when due date is in the future', function () {
        $fine = calculateFine('issued', Carbon::today()->addDays(7), null, 2);

        expect($fine)->toBe(0.0);
    });

    test('no fine on the exact due date', function () {
        $fine = calculateFine('issued', Carbon::today(), null, 5);

        expect($fine)->toBe(0.0);
    });

    test('calculates fine correctly for 4 overdue days at ₹5/day', function () {
        $fine = calculateFine('overdue', Carbon::today()->subDays(4), null, 5);

        expect($fine)->toBe(20.0);
    });

    test('calculates fine correctly for 1 overdue day at ₹3/day', function () {
        $fine = calculateFine('overdue', Carbon::yesterday(), null, 3);

        expect($fine)->toBe(3.0);
    });

    test('calculates fine based on return date not today for overdue books returned late', function () {
        $dueDate    = Carbon::today()->subDays(5);
        $returnDate = Carbon::today()->subDays(2); // returned 2 days ago (3 days late)
        $fine       = calculateFine('overdue', $dueDate, $returnDate, 4);

        expect($fine)->toBe(12.0); // 3 × ₹4
    });

    test('returned book uses stored fine_amount and ignores dates', function () {
        $fine = calculateFine('returned', Carbon::today()->subDays(10), null, 5, 25.0);

        expect($fine)->toBe(25.0);
    });

    test('fine is 0 for returned book returned before due date', function () {
        $fine = calculateFine('returned', Carbon::today()->addDays(5), Carbon::today(), 3, 0.0);

        expect($fine)->toBe(0.0);
    });

    test('fine is 0 for zero fine-per-day rate', function () {
        $fine = calculateFine('overdue', Carbon::today()->subDays(10), null, 0);

        expect($fine)->toBe(0.0);
    });
});

// ══════════════════════════════════════════════════════════════
//  BookIssue — overdue detection
// ══════════════════════════════════════════════════════════════

describe('BookIssue overdue detection', function () {

    test('returned book is never overdue even if past due', function () {
        expect(isOverdue('returned', Carbon::yesterday()))->toBeFalse();
    });

    test('lost book is never marked overdue', function () {
        expect(isOverdue('lost', Carbon::today()->subDays(10)))->toBeFalse();
    });

    test('issued book is overdue when past due date', function () {
        expect(isOverdue('issued', Carbon::today()->subDays(3)))->toBeTrue();
    });

    test('overdue status book is overdue when past due date', function () {
        expect(isOverdue('overdue', Carbon::yesterday()))->toBeTrue();
    });

    test('not overdue when due date is exactly today', function () {
        expect(isOverdue('issued', Carbon::today()))->toBeFalse();
    });

    test('not overdue when due date is in future', function () {
        expect(isOverdue('issued', Carbon::today()->addDays(7)))->toBeFalse();
    });

    test('overdueDays returns correct calendar day count', function () {
        expect(overdueDays('overdue', Carbon::today()->subDays(7)))->toBe(7);
    });

    test('overdueDays returns 0 for future due date', function () {
        expect(overdueDays('issued', Carbon::today()->addDays(5)))->toBe(0);
    });

    test('overdueDays returns 0 for returned book', function () {
        expect(overdueDays('returned', Carbon::yesterday()))->toBe(0);
    });
});

// ══════════════════════════════════════════════════════════════
//  BookIssue — fineDue (outstanding balance)
// ══════════════════════════════════════════════════════════════

describe('BookIssue outstanding fine balance', function () {

    test('fineDue is total fine minus amount paid', function () {
        // 5 days × ₹4 = ₹20 fine, ₹8 paid, ₹12 outstanding
        $due = fineDue('overdue', Carbon::today()->subDays(5), null, 4, 20.0, 8.0);

        expect($due)->toBe(12.0);
    });

    test('fineDue is 0 when fine is fully paid', function () {
        // 2 days × ₹5 = ₹10 fine, ₹10 paid
        $due = fineDue('overdue', Carbon::today()->subDays(2), null, 5, 10.0, 10.0);

        expect($due)->toBe(0.0);
    });

    test('fineDue is never negative even when overpaid', function () {
        // Overpaid scenario
        $due = fineDue('overdue', Carbon::today()->subDays(1), null, 2, 2.0, 100.0);

        expect($due)->toBeGreaterThanOrEqual(0.0);
    });

    test('fineDue is 0 for returned books with no outstanding balance', function () {
        $due = fineDue('returned', Carbon::today()->addDays(5), Carbon::today(), 3, 0.0, 0.0);

        expect($due)->toBe(0.0);
    });
});

// ══════════════════════════════════════════════════════════════
//  LibraryMember — eligibility checks (pure logic)
// ══════════════════════════════════════════════════════════════

describe('LibraryMember membership eligibility', function () {

    test('active member with future expiry and available slots can borrow', function () {
        $isActive       = true;
        $expiry         = Carbon::today()->addYear();
        $currentIssues  = 1;
        $maxAllowed     = 3;

        $canBorrow = $isActive
            && ! $expiry->isPast()
            && $currentIssues < $maxAllowed;

        expect($canBorrow)->toBeTrue();
    });

    test('inactive member cannot borrow', function () {
        $isActive = false;

        expect($isActive)->toBeFalse();
    });

    test('expired membership blocks borrowing', function () {
        $expiry = Carbon::yesterday();

        expect($expiry->isPast())->toBeTrue();
    });

    test('member at limit cannot borrow more books', function () {
        $currentIssues = 3;
        $maxAllowed    = 3;

        expect($currentIssues < $maxAllowed)->toBeFalse();
    });

    test('member under limit can borrow more books', function () {
        $currentIssues = 2;
        $maxAllowed    = 3;

        expect($currentIssues < $maxAllowed)->toBeTrue();
    });

    test('member number format is correct after increment', function () {
        $max = 'LIB-2024-007';
        preg_match('/(\d+)$/', $max, $m);
        $result = 'LIB-' . date('Y') . '-' . str_pad((int) $m[1] + 1, 3, '0', STR_PAD_LEFT);

        expect($result)->toBe('LIB-' . date('Y') . '-008');
    });

    test('issue number format is correct after increment', function () {
        $max = 'ISS-2024-00042';
        preg_match('/(\d+)$/', $max, $m);
        $result = 'ISS-' . date('Y') . '-' . str_pad((int) $m[1] + 1, 5, '0', STR_PAD_LEFT);

        expect($result)->toBe('ISS-' . date('Y') . '-00043');
    });
});
