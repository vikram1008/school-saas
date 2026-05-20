<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\BookIssue;
use App\Models\LibraryBook;
use App\Models\LibraryMember;
use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\TenantUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LibraryController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    // ── DASHBOARD ──────────────────────────────────────────────────

    public function dashboard()
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403, "You don't have permission to access the library.");
        }

        // Sync overdue statuses before loading stats
        $this->syncOverdueStatuses();

        $stats = [
            'total_books' => LibraryBook::where('is_active', true)->count(),
            'total_copies' => LibraryBook::where('is_active', true)->sum('total_copies'),
            'available_copies' => LibraryBook::where('is_active', true)->sum('available_copies'),
            'total_members' => LibraryMember::where('is_active', true)->count(),
            'active_issues' => BookIssue::whereIn('status', ['issued', 'overdue'])->count(),
            'overdue_issues' => BookIssue::where('status', 'overdue')->count(),
            'returns_today' => BookIssue::whereDate('return_date', today())->where('status', 'returned')->count(),
            'issued_today' => BookIssue::whereDate('issue_date', today())->count(),
            'total_fine_due' => BookIssue::whereIn('status', ['issued', 'overdue'])
                ->selectRaw('SUM(fine_amount - fine_paid) as total')->value('total') ?? 0,
        ];

        $recentIssues = BookIssue::with(['book', 'member.studentProfile', 'member.staffProfile'])
            ->whereIn('status', ['issued', 'overdue'])
            ->orderByDesc('issue_date')
            ->take(8)
            ->get();

        $overdueIssues = BookIssue::with(['book', 'member.studentProfile', 'member.staffProfile'])
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->take(8)
            ->get();

        $popularBooks = LibraryBook::withCount('issues')
            ->where('is_active', true)
            ->orderByDesc('issues_count')
            ->take(5)
            ->get();

        return view('tenant.library.dashboard', compact(
            'stats', 'recentIssues', 'overdueIssues', 'popularBooks'
        ));
    }

    // ── BOOKS CRUD ─────────────────────────────────────────────────

    public function books(Request $request)
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $query = LibraryBook::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('accession_number', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('available_copies', '>', 0)->where('is_reference_only', false);
            } elseif ($request->availability === 'unavailable') {
                $query->where(function ($q) {
                    $q->where('available_copies', 0)->orWhere('is_reference_only', true);
                });
            } elseif ($request->availability === 'reference') {
                $query->where('is_reference_only', true);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $books = $query->orderBy('title')->paginate(20)->withQueryString();
        $categories = LibraryBook::categories();
        $languages = LibraryBook::languages();
        $nextAccession = LibraryBook::nextAccessionNumber();

        return view('tenant.library.books', compact('books', 'categories', 'languages', 'nextAccession'));
    }

    public function storeBook(Request $request)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $data = $request->validate([
            'accession_number' => ['required', 'string', 'max:50', 'unique:library_books,accession_number'],
            'title' => ['required', 'string', 'max:255'],
            'title_hi' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'category' => ['required', Rule::in(array_keys(LibraryBook::categories()))],
            'publication_year' => ['nullable', 'integer', 'min:1800', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:50'],
            'language' => ['required', Rule::in(array_keys(LibraryBook::languages()))],
            'total_copies' => ['required', 'integer', 'min:1'],
            'rack_location' => ['nullable', 'string', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_reference_only' => ['nullable', 'boolean'],
        ]);

        $data['available_copies'] = $data['total_copies'];
        $data['is_reference_only'] = $request->boolean('is_reference_only');

        LibraryBook::create($data);

        return back()->with('success', 'Book added to catalogue successfully.');
    }

    public function updateBook(Request $request, LibraryBook $book)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $data = $request->validate([
            'accession_number' => ['required', 'string', 'max:50', Rule::unique('library_books', 'accession_number')->ignore($book->id)],
            'title' => ['required', 'string', 'max:255'],
            'title_hi' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'category' => ['required', Rule::in(array_keys(LibraryBook::categories()))],
            'publication_year' => ['nullable', 'integer', 'min:1800', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:50'],
            'language' => ['required', Rule::in(array_keys(LibraryBook::languages()))],
            'total_copies' => ['required', 'integer', 'min:1'],
            'rack_location' => ['nullable', 'string', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_reference_only' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Adjust available copies proportionally if total_copies changes
        $diff = (int) $data['total_copies'] - $book->total_copies;
        $newAvailable = max(0, $book->available_copies + $diff);

        // Clamp available copies — can't exceed new total
        $issuedCopies = $book->total_copies - $book->available_copies;
        $newAvailable = min($newAvailable, (int) $data['total_copies'] - $issuedCopies);
        $newAvailable = max(0, $newAvailable);

        $data['available_copies'] = $newAvailable;
        $data['is_reference_only'] = $request->boolean('is_reference_only');
        $data['is_active'] = $request->boolean('is_active', true);

        $book->update($data);

        return back()->with('success', 'Book updated successfully.');
    }

    public function destroyBook(LibraryBook $book)
    {
        $this->tenantUser()->isSchoolAdmin() || abort(403);

        if ($book->activeIssues()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete a book that has active issues. Return all copies first.']);
        }

        $book->delete();

        return back()->with('success', 'Book removed from catalogue.');
    }

    // ── MEMBERS ────────────────────────────────────────────────────

    public function members(Request $request)
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $query = LibraryMember::with(['studentProfile', 'staffProfile'])
            ->withCount(['activeIssues']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('member_number', 'like', "%{$q}%")
                    ->orWhereHas('studentProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('admission_number', 'like', "%{$q}%"))
                    ->orWhereHas('staffProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('type')) {
            $query->where('member_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $members = $query->orderBy('member_number', 'desc')->paginate(20)->withQueryString();
        $nextMemberNumber = LibraryMember::nextMemberNumber();

        // Students and staff who aren't already members
        $existingStudentIds = LibraryMember::where('member_type', 'student')->pluck('profile_id');
        $existingStaffIds = LibraryMember::where('member_type', 'staff')->pluck('profile_id');

        $students = StudentProfile::whereNotIn('id', $existingStudentIds)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        $staffList = StaffProfile::whereNotIn('id', $existingStaffIds)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('tenant.library.members', compact(
            'members', 'nextMemberNumber', 'students', 'staffList'
        ));
    }

    public function storeMember(Request $request)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $data = $request->validate([
            'member_type' => ['required', 'in:student,staff'],
            'profile_id' => ['required', 'integer'],
            'member_number' => ['required', 'string', 'max:30', 'unique:library_members,member_number'],
            'membership_start' => ['required', 'date'],
            'membership_expiry' => ['nullable', 'date', 'after:membership_start'],
            'max_books_allowed' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Verify the profile exists and isn't already a member
        if ($data['member_type'] === 'student') {
            $profile = StudentProfile::findOrFail($data['profile_id']);
            $data['user_id'] = $profile->user_id ?? null;
        } else {
            $profile = StaffProfile::findOrFail($data['profile_id']);
            $data['user_id'] = $profile->user_id ?? null;
        }

        // Guard: no duplicate membership for same profile
        $exists = LibraryMember::where('member_type', $data['member_type'])
            ->where('profile_id', $data['profile_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This person is already registered as a library member.']);
        }

        LibraryMember::create($data);

        return back()->with('success', 'Library member registered successfully.');
    }

    public function updateMember(Request $request, LibraryMember $member)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $data = $request->validate([
            'membership_expiry' => ['nullable', 'date'],
            'max_books_allowed' => ['required', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $member->update($data);

        return back()->with('success', 'Member updated.');
    }

    // ── BOOK ISSUE ─────────────────────────────────────────────────

    public function issues(Request $request)
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $this->syncOverdueStatuses();

        $query = BookIssue::with(['book', 'member.studentProfile', 'member.staffProfile', 'issuedBy']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('issue_number', 'like', "%{$q}%")
                    ->orWhereHas('book', fn ($b) => $b->where('title', 'like', "%{$q}%")->orWhere('accession_number', 'like', "%{$q}%"))
                    ->orWhereHas('member', fn ($m) => $m->where('member_number', 'like', "%{$q}%"))
                    ->orWhereHas('member.studentProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%"))
                    ->orWhereHas('member.staffProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show active issues
            $query->whereIn('status', ['issued', 'overdue']);
        }

        $issues = $query->orderByDesc('issue_date')->paginate(20)->withQueryString();

        // Data for the issue modal
        $books = LibraryBook::available()->orderBy('title')->get(['id', 'accession_number', 'title', 'available_copies']);
        $members = LibraryMember::with(['studentProfile', 'staffProfile'])
            ->where('is_active', true)
            ->get();

        $nextIssueNumber = BookIssue::nextIssueNumber();

        return view('tenant.library.issues', compact(
            'issues', 'books', 'members', 'nextIssueNumber'
        ));
    }

    public function storeIssue(Request $request)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $data = $request->validate([
            'book_id' => ['required', 'exists:library_books,id'],
            'member_id' => ['required', 'exists:library_members,id'],
            'issue_number' => ['required', 'string', 'unique:book_issues,issue_number'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after:issue_date'],
            'fine_per_day' => ['required', 'integer', 'min:0', 'max:100'],
            'condition_on_issue' => ['nullable', 'string', 'in:good,fair,poor'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $book = LibraryBook::findOrFail($data['book_id']);
        $member = LibraryMember::findOrFail($data['member_id']);

        // ── Edge case checks ──────────────────────────────────────
        if (! $book->isAvailable()) {
            return back()->withErrors(['error' => "Book '{$book->title}' is not available for issue (no copies or reference only)."]);
        }

        if (! $member->canBorrow()) {
            if (! $member->is_active) {
                return back()->withErrors(['error' => "Member '{$member->display_name}' is inactive."]);
            }
            if ($member->membership_expiry && $member->membership_expiry->isPast()) {
                return back()->withErrors(['error' => "Member '{$member->display_name}' membership has expired."]);
            }

            return back()->withErrors(['error' => "Member '{$member->display_name}' has reached their book limit ({$member->max_books_allowed} books)."]);
        }

        // Guard: same member can't have same book issued twice simultaneously
        $alreadyIssued = BookIssue::where('book_id', $book->id)
            ->where('member_id', $member->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->exists();

        if ($alreadyIssued) {
            return back()->withErrors(['error' => "This member already has a copy of '{$book->title}' issued."]);
        }

        DB::transaction(function () use ($data, $book) {
            BookIssue::create(array_merge($data, [
                'issued_by' => Auth::guard('tenant')->id(),
                'status' => 'issued',
            ]));

            // Decrement available copies
            $book->decrement('available_copies');
        });

        return back()->with('success', "Book issued successfully. Issue #: {$data['issue_number']}");
    }

    public function returnBook(Request $request, BookIssue $issue)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        if ($issue->status === 'returned') {
            return back()->withErrors(['error' => 'This book has already been returned.']);
        }

        $data = $request->validate([
            'return_date' => ['required', 'date', 'after_or_equal:'.$issue->issue_date->toDateString()],
            'condition_on_return' => ['nullable', 'string', 'in:good,fair,poor'],
            'fine_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $returnDate = Carbon::parse($data['return_date']);
        $overdueDays = max(0, $issue->due_date->diffInDays($returnDate, false));
        $fineAmount = $overdueDays * $issue->fine_per_day;
        $finePaid = min((float) ($data['fine_paid'] ?? 0), $fineAmount);

        DB::transaction(function () use ($issue, $data, $returnDate, $fineAmount, $finePaid) {
            $issue->update([
                'return_date' => $returnDate,
                'returned_to' => Auth::guard('tenant')->id(),
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_paid' => $finePaid,
                'condition_on_return' => $data['condition_on_return'] ?? null,
                'notes' => $data['notes'] ?? $issue->notes,
            ]);

            // Restore available copies
            $issue->book->increment('available_copies');
        });

        $msg = 'Book returned successfully.';
        if ($fineAmount > 0) {
            $msg .= " Fine: ₹{$fineAmount}. Collected: ₹{$finePaid}.";
        }

        return back()->with('success', $msg);
    }

    public function markLost(BookIssue $issue)
    {
        $user = $this->tenantUser();
        if (! $user->isSchoolAdmin() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        if ($issue->status !== 'issued' && $issue->status !== 'overdue') {
            return back()->withErrors(['error' => 'Only active issues can be marked as lost.']);
        }

        DB::transaction(function () use ($issue) {
            $issue->update([
                'status' => 'lost',
                'return_date' => null,
                'fine_amount' => $issue->calculateFine(),
            ]);

            // Don't restore available copies — the book is physically lost
            // But we reduce total_copies to keep inventory accurate
            $issue->book->decrement('total_copies');
            // Clamp available copies if total goes below
            if ($issue->book->available_copies > $issue->book->total_copies - 1) {
                $issue->book->update(['available_copies' => max(0, $issue->book->total_copies)]);
            }
        });

        return back()->with('success', 'Book marked as lost. Inventory updated.');
    }

    // ── AJAX ENDPOINTS ─────────────────────────────────────────────

    /**
     * Ajax: search books for the issue modal.
     */
    public function searchBooks(Request $request)
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $q = $request->q;
        $books = LibraryBook::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('accession_number', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%");
            })
            ->take(15)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'text' => "[{$b->accession_number}] {$b->title}".($b->author ? " — {$b->author}" : ''),
                'available' => $b->available_copies,
                'is_ref' => $b->is_reference_only,
                'can_issue' => $b->isAvailable(),
            ]);

        return response()->json(['results' => $books]);
    }

    /**
     * Ajax: search members for the issue modal.
     */
    public function searchMembers(Request $request)
    {
        $user = $this->tenantUser();
        if ($user->isStaff() && ! $user->hasPermission('can_manage_library')) {
            abort(403);
        }

        $q = $request->q;
        $members = LibraryMember::with(['studentProfile', 'staffProfile'])
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('member_number', 'like', "%{$q}%")
                    ->orWhereHas('studentProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('admission_number', 'like', "%{$q}%"))
                    ->orWhereHas('staffProfile', fn ($s) => $s->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%"));
            })
            ->take(15)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'text' => "[{$m->member_number}] {$m->display_name} ({$m->member_type})",
                'can_borrow' => $m->canBorrow(),
                'issued' => $m->currentIssueCount(),
                'max' => $m->max_books_allowed,
            ]);

        return response()->json(['results' => $members]);
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────────

    /**
     * Mark all issued books past their due date as overdue and update their fines.
     */
    private function syncOverdueStatuses(): void
    {
        BookIssue::where('status', 'issued')
            ->where('due_date', '<', today())
            ->each(function (BookIssue $issue) {
                $issue->update([
                    'status' => 'overdue',
                    'fine_amount' => $issue->calculateFine(),
                ]);
            });
    }
}
