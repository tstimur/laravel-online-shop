<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;
use Spatie\LaravelData\Data;

class ProductFilterDto extends Data
{
    public function __construct(
        public int $per_page = 10,
        public ?string $q = null,
        public ?string $min_price = null,
        public ?string $max_price = null,
        public bool $in_stock = false,
        public string $sort = 'new',
    ) {
    }

    public static function fromRequest(ProductFilterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : 10,
            q: isset($data['q']) ? (string) $data['q'] : null,
            min_price: isset($data['min_price']) ? (string) $data['min_price'] : null,
            max_price: isset($data['max_price']) ? (string) $data['max_price'] : null,
            in_stock: $request->boolean('in_stock'),
            sort: isset($data['sort']) ? (string) $data['sort'] : 'new',
        );
    }
}

