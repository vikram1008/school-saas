@php
    use App\Models\SchoolSettings;
    // Receipt is a standalone page (no layouts.tenant wrapper)
    // so we load settings directly here. Safe because this view
    // only renders inside tenant middleware — tenancy is initialized.
    $sch = SchoolSettings::current();
    $primaryColor = $sch->primary_color ?: '#696cff';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $feeCollection->receipt_number }}</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .receipt-wrapper { max-width: 700px; margin: 30px auto; }
        .school-header {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}cc 100%);
        }
        .receipt-divider { border-top: 2px dashed #dee2e6; }
        .watermark {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(-30deg);
            font-size: 5rem; color: rgba(40,199,111,0.07);
            font-weight: 900; pointer-events: none; z-index: 0;
            user-select: none;
        }
        .receipt-number { letter-spacing: 1px; font-family: monospace; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .receipt-wrapper { margin: 0; max-width: 100%; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="watermark">PAID</div>

<div class="receipt-wrapper">

    {{-- Action Buttons (hidden on print) --}}
    <div class="d-flex gap-2 mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="ti tabler-printer me-1"></i> Print Receipt
        </button>
        <a href="{{ route('tenant.fees.collections.create', ['student_id' => $feeCollection->student_profile_id]) }}"
           class="btn btn-outline-primary">
            + Collect More
        </a>
        <a href="{{ route('tenant.fees.collections.ledger', ['student_id' => $feeCollection->student_profile_id]) }}"
           class="btn btn-outline-secondary">
            View Ledger
        </a>
        <a href="{{ route('tenant.dashboard') }}"
           class="btn btn-outline-secondary ms-auto">
            ← Dashboard
        </a>
    </div>

    <div class="card shadow-sm">

        {{-- School Header --}}
        <div class="school-header text-white p-4 rounded-top">
            <div class="row align-items-center g-3">

                {{-- Logo --}}
                @if($sch->logo)
                    <div class="col-auto">
                        <img src="{{ $sch->logo_url }}"
                             alt="{{ $sch->school_name }}"
                             style="height:64px; width:auto; object-fit:contain; background:rgba(255,255,255,0.15); border-radius:8px; padding:6px;"
                             onerror="this.style.display='none'">
                    </div>
                @endif

                {{-- School Info --}}
                <div class="col">
                    <h4 class="fw-bold text-white mb-0 lh-sm">
                        {{ $sch->school_name }}
                    </h4>
                    @if($sch->school_name_hi)
                        <p class="mb-1 opacity-75 small">{{ $sch->school_name_hi }}</p>
                    @endif
                    @if($sch->tagline)
                        <p class="mb-1 opacity-75 fst-italic" style="font-size:11px;">
                            {{ $sch->tagline }}
                        </p>
                    @endif
                    <p class="mb-0 opacity-75 small">
                        @if($sch->full_address)
                            {{ $sch->full_address }}
                        @endif
                        @if($sch->phone)
                            &nbsp;·&nbsp; {{ $sch->phone }}
                        @endif
                    </p>
                </div>

                {{-- Receipt Badge --}}
                <div class="col-auto text-end">
                    <div class="bg-white bg-opacity-20 rounded px-3 py-2">
                        <p class="mb-0 text-white fw-bold small">FEE RECEIPT</p>
                        <p class="mb-0 text-white fw-bold receipt-number">
                            {{ $feeCollection->receipt_number }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-body p-4">

            {{-- Receipt Meta + Student Info --}}
            <div class="row g-3 mb-3">

                {{-- Left: Receipt Details --}}
                <div class="col-sm-6">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2 letter-spacing-1">
                        Receipt Details
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted small ps-0" style="width:40%">Date</td>
                            <td class="small fw-semibold">
                                {{ $feeCollection->collection_date->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small ps-0">Payment Mode</td>
                            <td class="small">
                                <span class="badge bg-label-primary">
                                    {{ strtoupper($feeCollection->payment_mode) }}
                                </span>
                            </td>
                        </tr>
                        @if($feeCollection->payment_reference)
                            <tr>
                                <td class="text-muted small ps-0">Reference</td>
                                <td class="small font-monospace">
                                    {{ $feeCollection->payment_reference }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted small ps-0">Collected By</td>
                            <td class="small">{{ $feeCollection->collectedBy?->name ?? 'Staff' }}</td>
                        </tr>
                        @if($sch->board_affiliation)
                            <tr>
                                <td class="text-muted small ps-0">Board</td>
                                <td class="small">{{ $sch->board_affiliation }}</td>
                            </tr>
                        @endif
                        @if($sch->udise_code)
                            <tr>
                                <td class="text-muted small ps-0">UDISE</td>
                                <td class="small font-monospace">{{ $sch->udise_code }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                {{-- Right: Student Info --}}
                <div class="col-sm-6">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Student Details</h6>
                    <div class="border rounded p-3 bg-light h-100">
                        <h6 class="fw-bold mb-0">
                            {{ $feeCollection->student->full_name }}
                        </h6>
                        @if($feeCollection->student->first_name_hi)
                            <p class="text-muted small mb-1">
                                {{ $feeCollection->student->full_name_hi }}
                            </p>
                        @endif
                        <hr class="my-2">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small ps-0" style="width:45%">Class</td>
                                <td class="small fw-semibold">
                                    {{ $feeCollection->student->class_section }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small ps-0">Admission No</td>
                                <td class="small fw-semibold font-monospace">
                                    {{ $feeCollection->student->admission_number }}
                                </td>
                            </tr>
                            @if($feeCollection->student->familyDetail?->father_name)
                                <tr>
                                    <td class="text-muted small ps-0">Father</td>
                                    <td class="small">
                                        {{ $feeCollection->student->familyDetail->father_name }}
                                    </td>
                                </tr>
                            @endif
                            @if($feeCollection->student->familyDetail?->father_mobile)
                                <tr>
                                    <td class="text-muted small ps-0">Mobile</td>
                                    <td class="small">
                                        {{ $feeCollection->student->familyDetail->father_mobile }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="receipt-divider my-3"></div>

            {{-- Fee Details Table --}}
            <h6 class="fw-bold mb-3">Fee Details / शुल्क विवरण</h6>
            <table class="table table-bordered table-sm">
                <thead style="background:{{ $primaryColor }}18;">
                    <tr>
                        <th style="width:36px">#</th>
                        <th>Fee Head / शुल्क मद</th>
                        <th style="width:120px">Period</th>
                        <th class="text-end" style="width:130px">Amount</th>
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
                                    <p class="text-muted mb-0" style="font-size:10px;">
                                        {{ $item->demand->feeHead->name_hi }}
                                    </p>
                                @endif
                            </td>
                            <td class="small">{{ $item->demand->period_label }}</td>
                            <td class="text-end fw-semibold small">
                                ₹{{ number_format($item->amount_paid, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:{{ $primaryColor }}12;">
                        <td colspan="3" class="fw-bold text-end">
                            Total Amount Paid / कुल जमा राशि
                        </td>
                        <td class="fw-bold text-end fs-6" style="color:{{ $primaryColor }};">
                            ₹{{ number_format($feeCollection->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($feeCollection->notes)
                <div class="alert alert-light border small mb-3 py-2">
                    <strong>Note:</strong> {{ $feeCollection->notes }}
                </div>
            @endif

            <div class="receipt-divider my-3"></div>

            {{-- Footer: note + signature --}}
            <div class="row align-items-end">

                {{-- Left: footer note --}}
                <div class="col">
                    @if($sch->receipt_footer_note)
                        <p class="text-muted small mb-1">{{ $sch->receipt_footer_note }}</p>
                        @if($sch->receipt_footer_note_hi)
                            <p class="text-muted small mb-1">{{ $sch->receipt_footer_note_hi }}</p>
                        @endif
                    @else
                        <p class="text-muted small mb-1">
                            This is a computer-generated receipt and does not require a signature.
                        </p>
                    @endif
                    <p class="text-muted small mb-0">
                        Generated: {{ now()->format('d M Y, h:i A') }}
                    </p>
                </div>

                {{-- Right: signature --}}
                <div class="col-auto text-center">
                    @if($sch->principal_signature_url)
                        <img src="{{ $sch->principal_signature_url }}"
                             alt="Signature"
                             style="max-height:50px; max-width:150px; object-fit:contain; display:block; margin:0 auto 4px;">
                    @else
                        <div style="border-top: 1px solid #ccc; width:160px; margin:0 auto 4px;"></div>
                    @endif
                    @if($sch->principal_name)
                        <p class="fw-semibold small mb-0">{{ $sch->principal_name }}</p>
                        @if($sch->principal_name_hi)
                            <p class="text-muted small mb-0" style="font-size:10px;">
                                {{ $sch->principal_name_hi }}
                            </p>
                        @endif
                    @endif
                    <p class="text-muted small mb-0" style="font-size:10px;">
                        Authorised Signatory
                    </p>
                </div>

            </div>

        </div>

        {{-- Card Footer: affiliation info --}}
        @if($sch->affiliation_number || $sch->school_code)
            <div class="card-footer text-center py-2"
                 style="background:{{ $primaryColor }}0a; border-top:1px solid {{ $primaryColor }}22;">
                <p class="text-muted small mb-0">
                    @if($sch->board_affiliation)
                        {{ $sch->board_affiliation }}
                        @if($sch->affiliation_number) · Affiliation No: {{ $sch->affiliation_number }} @endif
                    @endif
                    @if($sch->school_code) · School Code: {{ $sch->school_code }} @endif
                </p>
            </div>
        @endif

    </div>
</div>

</body>
</html>