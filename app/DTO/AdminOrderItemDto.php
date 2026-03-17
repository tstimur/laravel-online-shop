<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\LaravelData\Data;

class AdminOrderItemDto extends Data
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public float $price,
    ) {
    }
}
