
{{-- FILE: resources/views/admin/barangays/_form.blade.php --}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Barangay Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $barangay->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="slug" class="form-label">URL Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                       id="slug" name="slug" value="{{ old('slug', $barangay->slug) }}"
                       placeholder="Auto-generated if empty">
                <small class="form-text text-muted">Used for public URL: /b/{slug}</small>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="captain_name" class="form-label">Barangay Captain</label>
                <input type="text" class="form-control @error('captain_name') is-invalid @enderror" 
                       id="captain_name" name="captain_name" value="{{ old('captain_name', $barangay->captain_name) }}">
                @error('captain_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                       id="contact_number" name="contact_number" value="{{ old('contact_number', $barangay->contact_number) }}">
                @error('contact_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $barangay->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="logo" class="form-label">Barangay Logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                       id="logo" name="logo" accept="image/*">
                @if($barangay->logo)
                        <div class="mt-2">
                            <img src="{{ asset($barangay->logo) }}" 
                                 alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                            <small class="d-block text-muted">Current logo</small>
                        </div>
                    @endif
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control @error('address') is-invalid @enderror" 
                  id="address" name="address" rows="3">{{ old('address', $barangay->address) }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                   {{ old('is_active', $barangay->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                Active (residents can register)
            </label>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('admin.barangays.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $barangay->exists ? 'Update' : 'Create' }} Barangay
        </button>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
    document.getElementById('slug').value = slug;
});
</script>
@endpush
