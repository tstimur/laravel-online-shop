@php($items = $items ?? [['product_id' => '', 'quantity' => 1, 'price' => '']])

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id"
                    id="user_id"
                    class="form-select @error('user_id') is-invalid @enderror"
                    required>
                <option value="">Select user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        @selected(old('user_id', $order?->user_id) == $user->id)>
                        {{ $user->full_name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-lg-6">
            <label for="payment_method" class="form-label">Payment method</label>
            <select name="payment_method"
                    id="payment_method"
                    class="form-select @error('payment_method') is-invalid @enderror"
                    required>
                @foreach(\App\Models\Order::PAYMENT_METHODS as $method)
                    <option value="{{ $method }}"
                        @selected(old('payment_method', $order?->payment_method ?? \App\Models\Order::PAYMENT_METHOD_CASH) === $method)>
                        {{ $method }}
                    </option>
                @endforeach
            </select>
            @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="shipping_address" class="form-label">Shipping address</label>
            <textarea name="shipping_address"
                      id="shipping_address"
                      rows="2"
                      class="form-control @error('shipping_address') is-invalid @enderror">{{ old('shipping_address', $order?->shipping_address ?? '') }}</textarea>
            @error('shipping_address')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">If empty, the default address of the user will be used.</div>
        </div>

        <div class="col-12 col-lg-4">
            <label for="status" class="form-label">Status</label>
            <select name="status"
                    id="status"
                    class="form-select @error('status') is-invalid @enderror"
                    required>
                @foreach(\App\Models\Order::STATUSES as $status)
                    <option value="{{ $status }}"
                        @selected(old('status', $order?->status ?? \App\Models\Order::STATUS_PENDING) === $status)>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <hr class="my-4">

    <h2 class="h5 mb-3">Order items</h2>

    <div class="table-responsive">
        <table class="table align-middle" id="order-items">
            <thead>
            <tr>
                <th>Product</th>
                <th style="width: 140px;">Quantity</th>
                <th style="width: 160px;">Price</th>
                <th style="width: 40px;"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>
                        <select name="product_id[]"
                                class="form-select @error('product_id.' . $index) is-invalid @enderror"
                                required>
                            <option value="">Select product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    @selected(old('product_id.' . $index, $item['product_id']) == $product->id)>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number"
                               name="quantity[]"
                               class="form-control @error('quantity.' . $index) is-invalid @enderror"
                               min="1"
                               value="{{ old('quantity.' . $index, $item['quantity']) }}"
                               required>
                    </td>
                    <td>
                        <input type="number"
                               name="price[]"
                               class="form-control @error('price.' . $index) is-invalid @enderror"
                               min="0"
                               step="0.01"
                               value="{{ old('price.' . $index, $item['price']) }}"
                               required>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>&times;</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-outline-secondary btn-sm" id="add-item">Add item</button>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<template id="order-item-template">
    <tr>
        <td>
            <select name="product_id[]" class="form-select" required>
                <option value="">Select product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="price[]" class="form-control" min="0" step="0.01" value="0" required>
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>&times;</button>
        </td>
    </tr>
</template>

<script>
    (function () {
        const table = document.getElementById('order-items');
        const addButton = document.getElementById('add-item');
        const template = document.getElementById('order-item-template');

        if (!table || !addButton || !template) return;

        function removeRow(button) {
            const row = button.closest('tr');
            if (!row) return;
            const rows = table.querySelectorAll('tbody tr');
            if (rows.length <= 1) return;
            row.remove();
        }

        table.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.hasAttribute('data-remove-row')) return;
            removeRow(target);
        });

        addButton.addEventListener('click', function () {
            const clone = template.content.cloneNode(true);
            table.querySelector('tbody')?.appendChild(clone);
        });
    })();
</script>
