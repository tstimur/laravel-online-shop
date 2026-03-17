<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\AdminOrderDto;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminOrderService
{
    public function create(AdminOrderDto $dto): Order
    {
        $items = $this->normalizeItems($dto);
        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => 'Order must contain at least one item.',
            ]);
        }

        return DB::transaction(function () use ($dto, $items): Order {
            [$addressId, $shippingAddress] = $this->resolveAddressData($dto);

            $order = Order::create([
                'user_id' => $dto->userId,
                'address_id' => $addressId,
                'shipping_address' => $shippingAddress,
                'status' => $dto->status,
                'payment_method' => $dto->paymentMethod,
                'total' => $this->calculateTotal($items),
            ]);

            $this->syncItems($order, $items);

            return $order;
        });
    }

    public function update(Order $order, AdminOrderDto $dto): Order
    {
        $items = $this->normalizeItems($dto);
        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => 'Order must contain at least one item.',
            ]);
        }

        return DB::transaction(function () use ($order, $dto, $items): Order {
            [$addressId, $shippingAddress] = $this->resolveAddressData($dto);

            $order->user_id = $dto->userId;
            $order->address_id = $addressId;
            $order->shipping_address = $shippingAddress;
            $order->status = $dto->status;
            $order->payment_method = $dto->paymentMethod;
            $order->total = $this->calculateTotal($items);
            $order->save();

            $order->items()->delete();
            $this->syncItems($order, $items);

            return $order;
        });
    }

    public function delete(Order $order): void
    {
        $order->items()->delete();
        $order->delete();
    }

    /**
     * @param array<int, array<string, int|float>> $items
     */
    private function syncItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }
    }

    /**
     * @return array<int, array<string, int|float>>
     */
    private function normalizeItems(AdminOrderDto $dto): array
    {
        $items = [];
        foreach ($dto->items as $item) {
            $items[] = [
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        }

        return $items;
    }

    /**
     * @param array<int, array<string, int|float>> $items
     */
    private function calculateTotal(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        return $total;
    }

    /**
     * @return array{0:int|null,1:string|null}
     */
    private function resolveAddressData(AdminOrderDto $dto): array
    {
        if (!empty($dto->shippingAddress)) {
            return [null, $dto->shippingAddress];
        }

        $address = Address::query()
            ->where('user_id', $dto->userId)
            ->where('is_default', true)
            ->first();

        return [$address?->id, $address?->full_address];
    }
}
