{{--
  Document / Print Header Component
  Usage: @include('tenant.partials.school-header')
  Use at the top of fee receipts, report cards, TC, etc.
--}}

@php $s = $schoolSettings ?? \App\Models\SchoolSettings::current(); @endphp

<div class="school-doc-header d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
    {{-- Logo --}}
    <div style="flex-shrink:0;">
        <img src="{{ $s->logo_url }}"
             alt="{{ $s->school_name }}"
             style="height:70px; width:auto; object-fit:contain;"
             onerror="this.style.display='none'">
    </div>
    {{-- Info --}}
    <div class="text-center flex-grow-1">
        <h4 class="fw-bold mb-0 text-primary" style="font-size:1.3rem;">
            {{ $s->school_name }}
        </h4>
        @if($s->school_name_hi)
            <p class="mb-1 text-muted" style="font-size:1rem;">{{ $s->school_name_hi }}</p>
        @endif
        @if($s->tagline)
            <p class="mb-1 fst-italic small text-muted">{{ $s->tagline }}</p>
        @endif
        <p class="mb-0 small text-muted">
            @if($s->full_address) <i class="ti tabler-map-pin me-1"></i>{{ $s->full_address }} @endif
            @if($s->phone) &nbsp;|&nbsp; <i class="ti tabler-phone me-1"></i>{{ $s->phone }} @endif
            @if($s->email) &nbsp;|&nbsp; <i class="ti tabler-mail me-1"></i>{{ $s->email }} @endif
        </p>
        @if($s->board_affiliation || $s->affiliation_number || $s->udise_code)
            <p class="mb-0 small text-muted">
                @if($s->board_affiliation) {{ $s->board_affiliation }} @endif
                @if($s->affiliation_number) · Affiliation: {{ $s->affiliation_number }} @endif
                @if($s->udise_code) · UDISE: {{ $s->udise_code }} @endif
            </p>
        @endif
    </div>
    {{-- Right spacer (mirrors logo width for perfect center) --}}
    <div style="flex-shrink:0; width:70px;"></div>
</div>