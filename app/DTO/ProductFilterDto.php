<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;
use Spatie\LaravelData\Data;

class ProductFilterDto extends Data
{
    public function __construct(
        public int $per_page = 10,
    ) {
    }

    public static function fromRequest(ProductFilterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            isset($data['per_page']) ? (int) $data['per_page'] : 10,
        );
    }
}
