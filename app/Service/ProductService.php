<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function getMaxProductPrice(): ?string
    {
        /** @var string|null $max */
        $max = Product::query()->max('price');

        return $max;
    }

    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query();

        if ($dto->q) {
            $like = '%' . $dto->q . '%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)
                    ->orWhere('sku', 'like', $like);
            });
        }

        if ($dto->min_price !== null) {
            $query->where('price', '>=', $dto->min_price);
        }

        if ($dto->max_price !== null) {
            $query->where('price', '<=', $dto->max_price);
        }

        if ($dto->in_stock) {
            $query->where('stock', '>', 0);
        }

        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'stock_asc':
                $query->orderBy('stock');
                break;
            case 'stock_desc':
                $query->orderByDesc('stock');
                break;
            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true) ? $dto->per_page : 10;

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getProduct(Product $product): Product
    {
        return $product;
    }
}
