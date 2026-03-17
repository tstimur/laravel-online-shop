<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
               <input type="text"
                       name="name"
                       id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $product?->name ?? '') }}"
                       required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description"
                          id="description"
                          rows="5"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description ?? '') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="price" class="form-label">Price</label>
                   <input type="number"
                           name="price"
                           id="price"
                           step="0.01"
                           min="0"
                           class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $product?->price ?? 0) }}"
                           required>
                    @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="stock" class="form-label">Stock</label>
                   <input type="number"
                           name="stock"
                           id="stock"
                           min="0"
                           class="form-control @error('stock') is-invalid @enderror"
                           value="{{ old('stock', $product?->stock ?? 0) }}"
                           required>
                    @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                   <input type="text"
                           name="sku"
                           id="sku"
                           class="form-control @error('sku') is-invalid @enderror"
                           value="{{ old('sku', $product?->sku ?? '') }}"
                           required>
                @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id"
                        id="category_id"
                        class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">No category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected(old('category_id', $product?->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status"
                        id="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>
                    @foreach(\App\Models\Product::STATUSES as $status)
                        <option value="{{ $status }}"
                            @selected(old('status', $product?->status ?? \App\Models\Product::STATUS_ACTIVE) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file"
                       name="image"
                       id="image"
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/*">
                @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(!empty($product?->image))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded"
                             style="max-height: 140px;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
