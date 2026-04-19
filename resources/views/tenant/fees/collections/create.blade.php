@extends('layouts.tenant')

@section('title', 'Collect Fee')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.fees.collections.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Collect Fee / शुल्क संग्रह</h4>
            <p class="text-muted small mb-0">Select student and collect pending fees.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Student Search --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-user-search me-2 text-primary"></i>
                        Select Student
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Search Student</label>
                            <input type="text" id="studentSearchInput"
                                   class="form-control"
                                   placeholder="Type name or admission number..."
                                   value="{{ $student?->full_name }}">
                            <div id="searchDropdown"
                                 class="list-group position-absolute w-50 shadow-sm"
                                 style="z-index:100; display:none;"></div>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ request()->fullUrlWithQuery(['student_id' => '']) }}"
                               class="btn btn-outline-secondary"
                               id="clearStudentBtn"
                               style="{{ $student ? '' : 'display:none' }}">
                                <i class="icon-base ti tabler-x me-1"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($student)
        {{-- Student Info Banner --}}
        <div class="col-12">
            <div class="alert alert-primary mb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-primary text-white">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-primary fw-bold">{{ $student->full_name }}</h6>
                        <p class="mb-0 small">
                            {{ $student->class_section }} &nbsp;·&nbsp;
                            Admission: {{ $student->admission_number }}
                            @if($student->familyDetail?->father_name)
                                &nbsp;·&nbsp; Father: {{ $student->familyDetail->father_name }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Form --}}
        <div class="col-12">
            <form action="{{ route('tenant.fees.collections.store') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="row g-4">
                    {{-- Pending Demands --}}
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-file-invoice me-2 text-warning"></i>
                                    Pending Demands
                                    <span class="badge bg-label-warning ms-1">{{ $demands->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($demands->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="selectAll"
                                                               class="form-check-input"
                                                               onchange="toggleAll(this)">
                                                    </th>
                                                    <th>Fee Head</th>
                                                    <th>Period</th>
                                                    <th class="text-end">Due</th>
                                                    <th class="text-end">Paid</th>
                                                    <th class="text-end">Balance</th>
                                                    <th class="text-end">Pay Now</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($demands as $i => $demand)
                                                    <tr class="demand-row">
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="form-check-input demand-check"
                                                                   name="demand_ids[]"
                                                                   value="{{ $demand->id }}"
                                                                   data-balance="{{ $demand->balance }}"
                                                                   onchange="updateDemand(this, {{ $i }})">
                                                        </td>
                                                        <td>
                                                            <p class="fw-semibold mb-0 small">
                                                                {{ $demand->feeHead->name }}
                                                            </p>
                                                            @if($demand->feeHead->name_hi)
                                                                <p class="text-muted mb-0"
                                                                   style="font-size:11px">
                                                                    {{ $demand->feeHead->name_hi }}
                                                                </p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="small">{{ $demand->period_label }}</span>
                                                            <br>
                                                            @if($demand->due_date->isPast() && !$demand->isPaid())
                                                                <span class="badge bg-label-danger"
                                                                      style="font-size:10px">
                                                                    Overdue
                                                                </span>
                                                            @else
                                                                <span class="text-muted"
                                                                      style="font-size:10px">
                                                                    Due: {{ $demand->due_date->format('d M') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end small">
                                                            ₹{{ number_format($demand->amount_due) }}
                                                        </td>
                                                        <td class="text-end small text-success">
                                                            ₹{{ number_format($demand->amount_paid) }}
                                                        </td>
                                                        <td class="text-end fw-bold text-danger">
                                                            ₹{{ number_format($demand->balance) }}
                                                        </td>
                                                        <td class="text-end">
                                                            <input type="number"
                                                                   name="amounts[{{ $i }}]"
                                                                   id="amount_{{ $i }}"
                                                                   class="form-control form-control-sm text-end amount-input"
                                                                   style="width:100px"
                                                                   value="{{ $demand->balance }}"
                                                                   min="0.01"
                                                                   max="{{ $demand->balance }}"
                                                                   step="0.01"
                                                                   disabled
                                                                   onchange="updateTotal()">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold">
                                                        Selected Total:
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="fw-bold text-success fs-6"
                                                              id="selectedTotal">₹0</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="icon-base ti tabler-check-circle"
                                           style="font-size:3rem; color:#28c76f;"></i>
                                        <p class="text-success mt-2 mb-0 fw-semibold">
                                            No pending demands! All fees are paid.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-credit-card me-2 text-success"></i>
                                    Payment Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Payment Mode <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_mode" class="form-select" required>
                                        @foreach(\App\Models\FeeCollection::paymentModeLabels() as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Collection Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                            name="collection_date"
                                            id="collectionDate"
                                            class="form-control flatpickr-input"
                                            placeholder="Select date"
                                            value="{{ date('Y-m-d') }}"
                                            autocomplete="off" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Payment Reference
                                    </label>
                                    <input type="text" name="payment_reference"
                                           class="form-control"
                                           placeholder="UPI ID, Cheque No, Txn ID...">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control"
                                              rows="2"></textarea>
                                </div>

                                <div class="border rounded p-3 bg-light mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">Total Amount:</span>
                                        <span class="fw-bold text-success fs-5"
                                              id="totalAmountDisplay">₹0.00</span>
                                    </div>
                                </div>

                                <button type="submit"
                                        class="btn btn-success w-100 btn-lg"
                                        id="submitBtn" disabled>
                                    <i class="icon-base ti tabler-check me-2"></i>
                                    Confirm Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Student search
let searchTimer2;
const searchInput2   = document.getElementById('studentSearchInput');
const searchDropdown = document.getElementById('searchDropdown');

if (searchInput2) {
    searchInput2.addEventListener('input', function() {
        clearTimeout(searchTimer2);
        const q = this.value.trim();
        if (q.length < 2) { searchDropdown.style.display = 'none'; return; }

        searchTimer2 = setTimeout(async () => {
            const res  = await fetch(`/fees/students/search?q=${encodeURIComponent(q)}`);
            const data = await res.json();

            searchDropdown.innerHTML = '';
            data.results.forEach(s => {
                const item = document.createElement('a');
                item.href  = `?student_id=${s.id}`;
                item.className = 'list-group-item list-group-item-action small';
                item.textContent = s.text;
                searchDropdown.appendChild(item);
            });
            searchDropdown.style.display = data.results.length ? 'block' : 'none';
        }, 300);
    });
}

// Demand selection
function toggleAll(masterCheckbox) {
    document.querySelectorAll('.demand-check').forEach((cb, i) => {
        cb.checked = masterCheckbox.checked;
        const amountInput = document.getElementById(`amount_${i}`);
        if (amountInput) amountInput.disabled = !masterCheckbox.checked;
    });
    updateTotal();
}

function updateDemand(checkbox, idx) {
    const amountInput = document.getElementById(`amount_${idx}`);
    if (amountInput) amountInput.disabled = !checkbox.checked;
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.demand-check:checked').forEach((cb, i) => {
        const idx    = Array.from(document.querySelectorAll('.demand-check')).indexOf(cb);
        const input  = document.getElementById(`amount_${idx}`);
        if (input) total += parseFloat(input.value) || 0;
    });

    const fmt = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('selectedTotal').textContent      = fmt;
    document.getElementById('totalAmountDisplay').textContent = fmt;

    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) submitBtn.disabled = total <= 0;
}

document.addEventListener('click', function(e) {
    if (searchDropdown && !searchDropdown.contains(e.target) && e.target !== searchInput2) {
        searchDropdown.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    flatpickr('#collectionDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        defaultDate: 'today',
        maxDate:     'today',
        allowInput:  false,
    });
});
</script>
@endpush



@endsection