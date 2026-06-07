<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ успешно оформлен</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #2d2d2d;">
<h1>Спасибо за заказ, {{ $order->user->full_name }}!</h1>
<p>Ваш заказ #{{ $order->id }} успешно оформлен.</p>

<table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; border-color: #d4d4d4;">
    <thead style="background: #f7f7f7;">
    <tr>
        <th align="left">Товар</th>
        <th align="right">Кол-во</th>
        <th align="right">Цена за ед.</th>
        <th align="right">Сумма</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product?->name ?? ('Товар #'.$item->product_id) }}</td>
            <td align="right">{{ $item->quantity }}</td>
            <td align="right">{{ number_format((float) $item->price, 2, '.', ' ') }}</td>
            <td align="right">{{ number_format((float) $item->price * $item->quantity, 2, '.', ' ') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p style="margin-top: 16px;">
    <strong>Итоговая сумма:</strong>
    {{ number_format((float) $order->total, 2, '.', ' ') }}
</p>
<p>
    <strong>Способ оплаты:</strong>
    {{ $order->payment_method === \App\Models\Order::PAYMENT_METHOD_CARD ? 'Оплачено онлайн картой' : 'Наличными при получении' }}
</p>
</body>
</html>
