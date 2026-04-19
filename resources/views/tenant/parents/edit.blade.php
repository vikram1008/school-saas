@extends('layouts.tenant')

@section('title', 'Edit Parent')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.parents.show', $parent) }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Edit Parent / अभिभावक संपादन</h4>
            <p class="text-muted small mb-0">{{ $parent->full_name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.parents.update', $parent) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="first_name" class="form-control"
                                       value="{{ old('first_name', $parent->first_name) }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    प्रथम नाम <span class="badge bg-label-warning">हिं</span>
                                </label>
                                <input type="text" name="first_name_hi" class="form-control"
                                       value="{{ old('first_name_hi', $parent->first_name_hi) }}"
                                       placeholder="हिंदी में">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="last_name" class="form-control"
                                       value="{{ old('last_name', $parent->last_name) }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    उपनाम <span class="badge bg-label-warning">हिं</span>
                                </label>
                                <input type="text" name="last_name_hi" class="form-control"
                                       value="{{ old('last_name_hi', $parent->last_name_hi) }}"
                                       placeholder="हिंदी में">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Relation</label>
                                <select name="relation" class="form-select">
                                    @foreach(['father'=>'Father','mother'=>'Mother','guardian'=>'Guardian','other'=>'Other'] as $v=>$l)
                                        <option value="{{ $v }}"
                                            {{ old('relation',$parent->relation)==$v?'selected':'' }}>
                                            {{ $l }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">
                                    Mobile <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="mobile" class="form-control"
                                       value="{{ old('mobile', $parent->mobile) }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Alternate Phone</label>
                                <input type="text" name="alternate_phone" class="form-control"
                                       value="{{ old('alternate_phone', $parent->alternate_phone) }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Occupation</label>
                                <input type="text" name="occupation" class="form-control"
                                       value="{{ old('occupation', $parent->occupation) }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    व्यवसाय <span class="badge bg-label-warning">हिं</span>
                                </label>
                                <input type="text" name="occupation_hi" class="form-control"
                                       value="{{ old('occupation_hi', $parent->occupation_hi) }}"
                                       placeholder="हिंदी में">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $parent->address) }}</textarea>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" class="form-control"
                                       value="{{ old('city', $parent->city) }}">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" name="state" class="form-control"
                                       value="{{ old('state', $parent->state) }}">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">PIN Code</label>
                                <input type="text" name="pincode" class="form-control"
                                       value="{{ old('pincode', $parent->pincode) }}" maxlength="6">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           name="is_active" value="1"
                                           {{ old('is_active', $parent->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Active Account</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('tenant.parents.show', $parent) }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection