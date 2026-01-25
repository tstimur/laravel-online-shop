@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="{{ route('categories.index') }}">Перейти в каталог</a>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th class="text-end">Цена</th>
                    <th class="text-center" style="width: 220px;">Количество</th>
                    <th class="text-end">Сумма</th>
                    <th class="text-end" style="width: 1px;"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php($product = $item['product'])
                    <tr data-cart-row="{{ $product->id }}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->name }}"
                                         style="width: 64px; height: 64px; object-fit: cover;"
                                         class="rounded border">
                                @else
                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                         style="width: 64px; height: 64px;">
                                        <span class="text-muted small">Нет</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="text-muted small">Артикул: {{ $product->sku }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            {{ number_format((float) $item['unit_price'], 0, ',', ' ') }} ₽
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST"
                                      action="{{ route('cart.items.update', $product) }}"
                                      data-ajax-cart="1"
                                      data-cart-action="update">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] <= 1)>
                                        −
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('cart.items.update', $product) }}"
                                      data-ajax-cart="1"
                                      data-cart-action="set">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item['quantity'] }}"
                                           min="1"
                                           max="{{ $product->stock }}"
                                           step="1"
                                           class="form-control form-control-sm text-center"
                                           style="width: 90px">
                                </form>

                                <form method="POST"
                                      action="{{ route('cart.items.update', $product) }}"
                                      data-ajax-cart="1"
                                      data-cart-action="update">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] >= $product->stock)>
                                        +
                                    </button>
                                </form>
                            </div>
                            <div class="text-muted small mt-1">На складе: {{ $product->stock }}</div>
                        </td>
                        <td class="text-end">
                            {{ number_format((float) $item['subtotal'], 0, ',', ' ') }} ₽
                        </td>
                        <td class="text-end">
                            <form method="POST"
                                  action="{{ route('cart.items.destroy', $product) }}"
                                  data-ajax-cart="1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="fw-semibold">
                Итого: {{ $totalQuantity }} шт.
            </div>
            <div class="fs-5 fw-bold">
                {{ number_format((float) $totalPrice, 0, ',', ' ') }} ₽
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Продолжить покупки</a>
                <form method="POST" action="{{ route('cart.clear') }}" data-ajax-cart="1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Очистить корзину</button>
                </form>
            </div>
        </div>
    </div>
@endif
