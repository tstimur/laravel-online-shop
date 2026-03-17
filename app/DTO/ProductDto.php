<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class ProductDto extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock,
        public string $sku,
        public string $status,
        public ?int $categoryId,
        public ?UploadedFile $image,
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            $request->validated('name'),
            $request->validated('description'),
            (float) $request->validated('price'),
            (int) $request->validated('stock'),
            $request->validated('sku'),
            $request->validated('status'),
            $request->validated('category_id'),
            $request->file('image')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toProductData(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->categoryId,
        ];
    }
}
