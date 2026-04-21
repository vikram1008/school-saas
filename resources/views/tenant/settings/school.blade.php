@extends('layouts.tenant')

@section('title', 'School Settings')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">School Settings / विद्यालय सेटिंग</h4>
            <p class="text-muted small mb-0">
                Manage your school's profile, logo, contact and document settings.
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            {{-- Live logo preview --}}
            <img id="logoLivePreview"
                 src="{{ $settings->logo_url }}"
                 alt="School Logo"
                 class="rounded border"
                 style="height:52px; width:auto; object-fit:contain; background:#f8f8f8; padding:4px;">
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.settings.school.update') }}"
          method="POST"
          enctype="multipart/form-data"
          novalidate>
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- LEFT: Logo & Branding --}}
            <div class="col-lg-4">

                {{-- Logo Card --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="icon-base ti tabler-photo me-1 text-primary"></i>
                            Logo & Favicon
                        </h5>
                    </div>
                    <div class="card-body">

                        {{-- Logo --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">School Logo</label>
                            <div class="border rounded p-3 text-center mb-2"
                                 style="background:#fafafa; min-height:100px; display:flex; align-items:center; justify-content:center;">
                                <img id="logoPreview"
                                     src="{{ $settings->logo_url }}"
                                     alt="Logo"
                                     style="max-height:90px; max-width:100%; object-fit:contain;">
                            </div>
                            <input type="file"
                                   name="logo"
                                   id="logoInput"
                                   class="form-control form-control-sm @error('logo') is-invalid @enderror"
                                   accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                   onchange="previewImage(this,'logoPreview','logoLivePreview')">
                            <div class="form-text">PNG, JPG, SVG, WebP. Max 2MB. Transparent PNG recommended.</div>
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($settings->logo)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                                    <label class="form-check-label small text-danger" for="removeLogo">
                                        Remove current logo
                                    </label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        {{-- Favicon --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Favicon</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img id="faviconPreview"
                                     src="{{ $settings->favicon_url }}"
                                     style="width:32px; height:32px; object-fit:contain; border:1px solid #eee; border-radius:4px; background:#fafafa;">
                                <span class="text-muted small">Shown in browser tab</span>
                            </div>
                            <input type="file"
                                   name="favicon"
                                   class="form-control form-control-sm"
                                   accept="image/png,image/jpeg,image/x-icon"
                                   onchange="previewImage(this,'faviconPreview',null)">
                            <div class="form-text">32×32 or 64×64 PNG. Max 512KB.</div>
                            @if($settings->favicon)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_favicon" value="1">
                                    <label class="form-check-label small text-danger">Remove current favicon</label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        {{-- Primary Color --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Color</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="color"
                                       name="primary_color"
                                       class="form-control form-control-color"
                                       value="{{ old('primary_color', $settings->primary_color) }}"
                                       style="width:48px; height:38px; padding:2px;">
                                <input type="text"
                                       id="colorHex"
                                       class="form-control form-control-sm font-monospace"
                                       value="{{ old('primary_color', $settings->primary_color) }}"
                                       pattern="#[0-9A-Fa-f]{6}"
                                       placeholder="#696cff"
                                       style="max-width:100px;"
                                       readonly>
                                <span class="text-muted small">Used in receipts & reports</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Principal Signature --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="icon-base ti tabler-signature me-1 text-success"></i>
                            Principal Signature
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($settings->principal_signature_url)
                            <div class="border rounded p-2 text-center mb-2"
                                 style="background:#fafafa; min-height:60px;">
                                <img src="{{ $settings->principal_signature_url }}"
                                     alt="Signature"
                                     style="max-height:60px; max-width:100%; object-fit:contain;">
                            </div>
                        @endif
                        <input type="file" name="principal_signature"
                               class="form-control form-control-sm"
                               accept="image/png,image/jpeg">
                        <div class="form-text">PNG with transparent background recommended. Max 1MB.</div>
                        @if($settings->principal_signature)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_signature" value="1">
                                <label class="form-check-label small text-danger">Remove signature</label>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIGHT: Info tabs --}}
            <div class="col-lg-8">

                {{-- Nav tabs --}}
                <div class="card">
                    <div class="card-header border-bottom p-0">
                        <ul class="nav nav-tabs border-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active px-4 py-3" data-bs-toggle="tab" href="#tab-identity">
                                    <i class="icon-base ti tabler-school me-1"></i> Identity
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#tab-contact">
                                    <i class="icon-base ti tabler-phone me-1"></i> Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#tab-address">
                                    <i class="icon-base ti tabler-map-pin me-1"></i> Address
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#tab-academic">
                                    <i class="icon-base ti tabler-certificate me-1"></i> Academic
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#tab-documents">
                                    <i class="icon-base ti tabler-file-text me-1"></i> Documents
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content pt-4">

                        {{-- TAB: Identity --}}
                        <div class="tab-pane fade show active" id="tab-identity">
                            <div class="row g-3">
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold">
                                        School Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="school_name"
                                           class="form-control @error('school_name') is-invalid @enderror"
                                           value="{{ old('school_name', $settings->school_name) }}"
                                           placeholder="Full school name"
                                           data-hindi-target="[name='school_name_hi']"
                                           required>
                                    <div class="invalid-feedback">School name is required.</div>
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold">
                                        विद्यालय का नाम <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="school_name_hi" class="form-control"
                                           value="{{ old('school_name_hi', $settings->school_name_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold">Tagline / Motto</label>
                                    <input type="text" name="tagline" class="form-control"
                                           value="{{ old('tagline', $settings->tagline) }}"
                                           placeholder="e.g. Knowledge is Power"
                                           data-hindi-target="[name='tagline_hi']">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold">
                                        आदर्श वाक्य <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="tagline_hi" class="form-control"
                                           value="{{ old('tagline_hi', $settings->tagline_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Principal Name</label>
                                    <input type="text" name="principal_name" class="form-control"
                                           value="{{ old('principal_name', $settings->principal_name) }}"
                                           placeholder="Full name"
                                           data-hindi-target="[name='principal_name_hi']">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">
                                        प्राचार्य का नाम <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="principal_name_hi" class="form-control"
                                           value="{{ old('principal_name_hi', $settings->principal_name_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>

                        {{-- TAB: Contact --}}
                        <div class="tab-pane fade" id="tab-contact">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="icon-base ti tabler-mail"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control"
                                               value="{{ old('email', $settings->email) }}"
                                               placeholder="school@example.com">
                                    </div>
                                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Website</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="icon-base ti tabler-world"></i>
                                        </span>
                                        <input type="url" name="website" class="form-control"
                                               value="{{ old('website', $settings->website) }}"
                                               placeholder="https://myschool.edu">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Primary Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="icon-base ti tabler-phone"></i>
                                        </span>
                                        <input type="text" name="phone" class="form-control"
                                               value="{{ old('phone', $settings->phone) }}"
                                               placeholder="Primary number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Alternate Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="icon-base ti tabler-phone-plus"></i>
                                        </span>
                                        <input type="text" name="phone_alt" class="form-control"
                                               value="{{ old('phone_alt', $settings->phone_alt) }}"
                                               placeholder="Secondary number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: Address --}}
                        <div class="tab-pane fade" id="tab-address">
                            <div class="row g-3">
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold">Address Line 1</label>
                                    <input type="text" name="address_line1" class="form-control"
                                           value="{{ old('address_line1', $settings->address_line1) }}"
                                           placeholder="Building / Street"
                                           data-hindi-target="[name='address_line1_hi']">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold">
                                        पता पंक्ति 1 <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="address_line1_hi" class="form-control"
                                           value="{{ old('address_line1_hi', $settings->address_line1_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold">Address Line 2</label>
                                    <input type="text" name="address_line2" class="form-control"
                                           value="{{ old('address_line2', $settings->address_line2) }}"
                                           placeholder="Area / Landmark"
                                           data-hindi-target="[name='address_line2_hi']">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold">
                                        पता पंक्ति 2 <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="address_line2_hi" class="form-control"
                                           value="{{ old('address_line2_hi', $settings->address_line2_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text" name="city" class="form-control"
                                           value="{{ old('city', $settings->city) }}"
                                           data-hindi-target="[name='city_hi']">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">
                                        शहर <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="city_hi" class="form-control"
                                           value="{{ old('city_hi', $settings->city_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">PIN Code</label>
                                    <input type="text" name="pincode" class="form-control"
                                           value="{{ old('pincode', $settings->pincode) }}"
                                           placeholder="6-digit" maxlength="6">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">State</label>
                                    <input type="text" name="state" class="form-control"
                                           value="{{ old('state', $settings->state) }}"
                                           data-hindi-target="[name='state_hi']">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">
                                        राज्य <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" name="state_hi" class="form-control"
                                           value="{{ old('state_hi', $settings->state_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold">Country</label>
                                    <input type="text" name="country" class="form-control"
                                           value="{{ old('country', $settings->country ?? 'India') }}">
                                </div>
                            </div>
                        </div>

                        {{-- TAB: Academic --}}
                        <div class="tab-pane fade" id="tab-academic">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Board / Affiliation</label>
                                    <input type="text" name="board_affiliation" class="form-control"
                                           value="{{ old('board_affiliation', $settings->board_affiliation) }}"
                                           placeholder="e.g. CBSE, RBSE, ICSE">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">School Code</label>
                                    <input type="text" name="school_code" class="form-control"
                                           value="{{ old('school_code', $settings->school_code) }}"
                                           placeholder="Board-assigned school code">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Affiliation Number</label>
                                    <input type="text" name="affiliation_number" class="form-control"
                                           value="{{ old('affiliation_number', $settings->affiliation_number) }}"
                                           placeholder="Affiliation certificate number">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">UDISE Code</label>
                                    <input type="text" name="udise_code" class="form-control"
                                           value="{{ old('udise_code', $settings->udise_code) }}"
                                           placeholder="11-digit UDISE+ code">
                                </div>
                            </div>
                        </div>

                        {{-- TAB: Documents --}}
                        <div class="tab-pane fade" id="tab-documents">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Fee Receipt Footer Note
                                    </label>
                                    <textarea name="receipt_footer_note"
                                              class="form-control" rows="3"
                                              placeholder="e.g. This is a computer generated receipt. No signature required."
                                              data-hindi-target="[name='receipt_footer_note_hi']">{{ old('receipt_footer_note', $settings->receipt_footer_note) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        रसीद पाद टिप्पणी <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <textarea name="receipt_footer_note_hi"
                                              class="form-control" rows="3"
                                              placeholder="हिंदी में">{{ old('receipt_footer_note_hi', $settings->receipt_footer_note_hi) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info small mb-0">
                                        <i class="icon-base ti tabler-info-circle me-1"></i>
                                        This note appears at the bottom of every fee receipt, result card, and official printout.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Last updated: {{ $settings->updated_at?->diffForHumans() ?? 'Never' }}
                        </span>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-device-floppy me-1"></i>
                            Save Settings
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Live image preview for logo / favicon
window.previewImage = function(input, previewId, livePreviewId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById(previewId);
        if (preview) preview.src = e.target.result;
        if (livePreviewId) {
            const live = document.getElementById(livePreviewId);
            if (live) live.src = e.target.result;
        }
    };
    reader.readAsDataURL(input.files[0]);
};

// Sync color picker with hex input
document.querySelector('[name="primary_color"]')?.addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});
</script>
@endpush

@endsection