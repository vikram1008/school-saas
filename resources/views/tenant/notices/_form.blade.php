<div class="row g-3">
    <div class="col-sm-8">
        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               data-hindi-target="[name='title_hi']"
               value="{{ old('title', $notice->title ?? '') }}" required>
    </div>
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Visible To</label>
        <select name="visible_to" class="form-select">
            @foreach(\App\Models\Notice::visibleToLabels() as $val => $lbl)
                <option value="{{ $val }}"
                    {{ old('visible_to', $notice->visible_to ?? 'all') === $val ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">
            Title (Hindi) <span class="badge bg-label-warning">हिं</span>
        </label>
        <input type="text" name="title_hi" class="form-control"
               value="{{ old('title_hi', $notice->title_hi ?? '') }}"
               placeholder="शीर्षक हिंदी में">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
        <textarea name="content" class="form-control" rows="4" required
                  data-hindi-target="[name='content_hi']">{{ old('content', $notice->content ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">
            Content (Hindi) <span class="badge bg-label-warning">हिं</span>
        </label>
        <textarea name="content_hi" class="form-control" rows="3"
                  placeholder="सामग्री हिंदी में">{{ old('content_hi', $notice->content_hi ?? '') }}</textarea>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Expires At</label>
        @php
            $expiresValue = old('expires_at',
                isset($notice) && $notice?->expires_at
                    ? $notice->expires_at->format('Y-m-d')
                    : ''
            );
        @endphp
        <input type="text"
               name="expires_at"
               class="form-control notice-expiry"
               placeholder="No expiry (optional)"
               value="{{ $expiresValue }}"
               data-default="{{ $expiresValue }}"
               autocomplete="off" readonly>
        <div class="form-text">Leave empty for no expiry.</div>
    </div>
    <div class="col-sm-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox"
                   name="is_published" value="1"
                   {{ old('is_published', $notice->is_published ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold">Publish Now</label>
        </div>
    </div>
</div>