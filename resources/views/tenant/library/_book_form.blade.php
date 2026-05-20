{{-- Shared form fields for Add/Edit Book modals --}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Accession Number <span class="text-danger">*</span></label>
        <input type="text" name="accession_number" class="form-control"
               value="{{ old('accession_number', $book->accession_number ?? $nextAccession ?? '') }}"
               placeholder="e.g. ACC-2024-0001" required>
        <div class="form-text">Unique catalogue identifier for this copy.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">ISBN</label>
        <input type="text" name="isbn" class="form-control"
               value="{{ old('isbn', $book->isbn ?? '') }}"
               placeholder="e.g. 978-0-13-468599-1">
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Title (English) <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="{{ old('title', $book->title ?? '') }}"
               placeholder="Book title in English" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
        <select name="category" class="form-select" required>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}"
                    {{ old('category', $book->category ?? 'general') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Title (Hindi)</label>
        <input type="text" name="title_hi" class="form-control"
               value="{{ old('title_hi', $book->title_hi ?? '') }}"
               placeholder="पुस्तक का नाम">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Language <span class="text-danger">*</span></label>
        <select name="language" class="form-select" required>
            @foreach($languages as $key => $label)
                <option value="{{ $key }}"
                    {{ old('language', $book->language ?? 'english') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Author</label>
        <input type="text" name="author" class="form-control"
               value="{{ old('author', $book->author ?? '') }}"
               placeholder="Author name">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Publisher</label>
        <input type="text" name="publisher" class="form-control"
               value="{{ old('publisher', $book->publisher ?? '') }}"
               placeholder="Publisher name">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Publication Year</label>
        <input type="number" name="publication_year" class="form-control"
               value="{{ old('publication_year', $book->publication_year ?? '') }}"
               placeholder="{{ date('Y') }}" min="1800" max="{{ date('Y') + 1 }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Edition</label>
        <input type="text" name="edition" class="form-control"
               value="{{ old('edition', $book->edition ?? '') }}"
               placeholder="e.g. 3rd">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">No. of Copies <span class="text-danger">*</span></label>
        <input type="number" name="total_copies" class="form-control"
               value="{{ old('total_copies', $book->total_copies ?? 1) }}"
               min="1" required>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Rack / Location</label>
        <input type="text" name="rack_location" class="form-control"
               value="{{ old('rack_location', $book->rack_location ?? '') }}"
               placeholder="e.g. A-3">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Price (₹)</label>
        <input type="number" step="0.01" name="price" class="form-control"
               value="{{ old('price', $book->price ?? '') }}"
               placeholder="0.00" min="0">
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control" rows="2"
                  placeholder="Brief description or notes about this book">{{ old('description', $book->description ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_reference_only"
                   id="is_reference_only_{{ $book->id ?? 'new' }}"
                   value="1" {{ old('is_reference_only', $book->is_reference_only ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_reference_only_{{ $book->id ?? 'new' }}">
                <strong>Reference Only</strong> — This book cannot be issued (in-library use only)
            </label>
        </div>
    </div>
</div>
