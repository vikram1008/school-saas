<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $feeCollection->receipt_number }}</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .receipt-wrapper { max-width: 680px; margin: 30px auto; }
        .school-header { background: linear-gradient(135deg, #696cff 0%, #9155fd 100%); }
        .receipt-divider { border-top: 2px dashed #dee2e6; }
        .watermark {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(-30deg);
            font-size: 5rem; color: rgba(40,199,111,0.08);
            font-weight: 900; pointer-events: none; z-index: 0;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .receipt-wrapper { margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="watermark">PAID</div>

<div class="receipt-wrapper">

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Receipt
        </button>
        <a href="{{ route('tenant.fees.collections.create', ['student_id' => $feeCollection->student_profile_id]) }}"
           class="btn btn-outline-primary">
            + Collect More
        </a>
        <a href="{{ route('tenant.fees.collections.ledger', ['student_id' => $feeCollection->student_profile_id]) }}"
           class="btn btn-outline-secondary">
            View Ledger
        </a>
    </div>

    <div class="card shadow">

        {{-- School Header --}}
        <div class="school-header text-white p-4 rounded-top">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="fw-bold text-white mb-1">{{ tenant('school_name') }}</h4>
                    @if(tenant('school_name_hi'))
                        <p class="mb-1 opacity-75">{{ tenant('school_name_hi') }}</p>
                    @endif
                    <p class="mb-0 opacity-75 small">Fee Payment Receipt</p>
                </div>
                <div class="col-auto text-end">
                    <h5 class="text-white fw-bold mb-1">RECEIPT</h5>
                    <p class="mb-0 opacity-75 small font-monospace">
                        {{ $feeCollection->receipt_number }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- Receipt Meta --}}
            <div class="row mb-3">
                <div class="col-sm-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted small fw-semibold ps-0">Date:</td>
                            <td class="small">{{ $feeCollection->collection_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold ps-0">Payment Mode:</td>
                            <td class="small">
                                <span class="badge bg-label-primary">
                                    {{ strtoupper($feeCollection->payment_mode) }}
                                </span>
                            </td>
                        </tr>
                        @if($feeCollection->payment_reference)
                        <tr>
                            <td class="text-muted small fw-semibold ps-0">Reference:</td>
                            <td class="small font-monospace">{{ $feeCollection->payment_reference }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted small fw-semibold ps-0">Collected By:</td>
                            <td class="small">{{ $feeCollection->collectedBy?->name ?? 'Staff' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-3 bg-light">
                        <p class="text-muted small mb-1">Student Name</p>
                        <h6 class="fw-bold mb-1">{{ $feeCollection->student->full_name }}</h6>
                        @if($feeCollection->student->first_name_hi)
                            <p class="text-muted small mb-1">
                                {{ $feeCollection->student->full_name_hi }}
                            </p>
                        @endif
                        <p class="text-muted small mb-1">
                            Class: <strong>{{ $feeCollection->student->class_section }}</strong>
                        </p>
                        <p class="text-muted small mb-0">
                            Admission No: <strong>{{ $feeCollection->student->admission_number }}</strong>
                        </p>
                        @if($feeCollection->student->familyDetail?->father_name)
                            <p class="text-muted small mb-0">
                                Father: {{ $feeCollection->student->familyDetail->father_name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="receipt-divider my-3"></div>

            {{-- Fee Details --}}
            <h6 class="fw-bold mb-3">Fee Details</h6>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Fee Head / शुल्क मद</th>
                        <th>Period</th>
                        <th class="text-end">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeCollection->items as $i => $item)
                        <tr>
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <p class="fw-semibold mb-0 small">
                                    {{ $item->demand->feeHead->name }}
                                </p>
                                @if($item->demand->feeHead->name_hi)
                                    <p class="text-muted mb-0" style="font-size:11px">
                                        {{ $item->demand->feeHead->name_hi }}
                                    </p>
                                @endif
                            </td>
                            <td class="small">{{ $item->demand->period_label }}</td>
                            <td class="text-end fw-semibold">
                                ₹{{ number_format($item->amount_paid, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <td colspan="3" class="fw-bold text-end fs-6">Total Amount Paid</td>
                        <td class="fw-bold text-end fs-5 text-success">
                            ₹{{ number_format($feeCollection->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($feeCollection->notes)
                <div class="alert alert-light border small mb-3">
                    <strong>Note:</strong> {{ $feeCollection->notes }}
                </div>
            @endif

            <div class="receipt-divider my-3"></div>

            {{-- Footer --}}
            <div class="row align-items-end">
                <div class="col">
                    <p class="text-muted small mb-0">
                        This is a computer-generated receipt and does not require a signature.
                    </p>
                    <p class="text-muted small mb-0">
                        Generated: {{ now()->format('d M Y, h:i A') }}
                    </p>
                </div>
                <div class="col-auto text-end">
                    <p class="text-muted small mb-1">Authorised Signatory</p>
                    <div style="border-top: 1px solid #ccc; width: 150px; margin-left:auto;"></div>
                    <p class="text-muted small mt-1">{{ tenant('school_name') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>