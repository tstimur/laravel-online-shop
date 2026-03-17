<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text"
               name="name"
               id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $role?->name ?? '') }}"
               required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text"
               name="slug"
               id="slug"
               class="form-control @error('slug') is-invalid @enderror"
               value="{{ old('slug', $role?->slug ?? '') }}"
               required>
        @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Lowercase, letters, numbers, and dashes only.</div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
