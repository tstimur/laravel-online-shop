<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\Data;

class AdminOrderDto extends Data
{
    /**
     * @param array<int, AdminOrderItemDto> $items
     */
    public function __construct(
        public int $userId,
        public ?string $shippingAddress,
        public string $status,
        public string $paymentMethod,
        public array $items,
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        $productIds = $request->validated('product_id', []);
        $quantities = $request->validated('quantity', []);
        $prices = $request->validated('price', []);

        $items = [];
        $count = max(count($productIds), count($quantities), count($prices));

        for ($i = 0; $i < $count; $i++) {
            $productId = $productIds[$i] ?? null;
            $quantity = $quantities[$i] ?? null;
            $price = $prices[$i] ?? null;

            if ($productId === null || $quantity === null || $price === null) {
                continue;
            }

            $items[] = new AdminOrderItemDto(
                (int) $productId,
                (int) $quantity,
                (float) $price
            );
        }

        return new self(
            (int) $request->validated('user_id'),
            $request->validated('shipping_address'),
            $request->validated('status'),
            $request->validated('payment_method'),
            $items
        );
    }
}
