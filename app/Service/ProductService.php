<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /**
     * Возвращает список товаров с контролируемой пагинацией.
     */
    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query();

        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true)
            ? $dto->per_page
            : 10;

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Возвращает конкретный товар.
     */
    public function getProduct(Product $product): Product
    {

        return $product;
    }
}
