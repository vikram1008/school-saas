{{--
  School Logo Component
  Usage: @include('tenant.partials.school-logo', ['size' => 'md', 'showName' => true])
  Sizes: xs (24px), sm (36px), md (48px), lg (64px), xl (90px), full (200px)
--}}

@php
    // $schoolSettings is the Tenant model, injected by AppServiceProvider View::composer.
    $s    = $schoolSettings ?? tenant();
    $sizes = ['xs' => 24, 'sm' => 36, 'md' => 48, 'lg' => 64, 'xl' => 90, 'full' => 200];
    $px   = $sizes[$size ?? 'md'] ?? 48;
    $show = $showName ?? false;
    $dark = $darkMode ?? false;
@endphp

<div class="d-flex align-items-center gap-2">
    <img src="{{ $s->logo_url }}"
         alt="{{ $s->school_name }}"
         style="height:{{ $px }}px; width:auto; object-fit:contain; max-width:{{ $px * 4 }}px;"
         onerror="this.style.display='none'">
    @if($show)
        <div>
            <div class="fw-bold lh-sm" style="{{ $dark ? 'color:#fff;' : '' }} font-size:clamp(13px,1.2vw,17px);">
                {{ $s->school_name }}
            </div>
            @if($s->school_name_hi)
                <div class="small opacity-75" style="{{ $dark ? 'color:#fff;' : '' }}">
                    {{ $s->school_name_hi }}
                </div>
            @endif
        </div>
    @endif
</div>