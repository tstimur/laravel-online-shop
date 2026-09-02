<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDto;
use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];
    private const int CATALOG_CACHE_TTL = 600;
    private const string CATALOG_CACHE_VERSION_KEY = 'products:catalog:version';

    public function getMaxProductPrice(): ?string
    {
        /** @var string|null $max */
        $max = Cache::remember(
            $this->catalogCacheKey('max-price', []),
            $this->catalogCacheTtl(),
            static function (): ?string {
                $max = Product::query()->max('price');

                return $max === null ? null : (string) $max;
            },
        );

        return $max;
    }

    public function getMaxProductPriceForCategoryId(int $categoryId): ?string
    {
        /** @var string|null $max */
        $max = Cache::remember(
            $this->catalogCacheKey('category:' . $categoryId . ':max-price', []),
            $this->catalogCacheTtl(),
            static function () use ($categoryId): ?string {
                $max = Product::query()
                    ->where('category_id', $categoryId)
                    ->max('price');

                return $max === null ? null : (string) $max;
            },
        );

        return $max;
    }

    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true) ? $dto->per_page : 10;

        /** @var LengthAwarePaginator $products */
        $products = Cache::remember(
            $this->catalogCacheKey('list', $this->filterCacheParameters($dto, $perPage)),
            $this->catalogCacheTtl(),
            function () use ($dto, $perPage): LengthAwarePaginator {
                $query = Product::query()->with('category');
                $this->applyFilters($query, $dto);

                return $query
                    ->orderByDesc('id')
                    ->paginate($perPage);
            },
        );

        return $this->appendPaginationQuery($products);
    }

    public function getProductsByCategoryId(int $categoryId, ProductFilterDto $dto): LengthAwarePaginator
    {
        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true) ? $dto->per_page : 10;

        /** @var LengthAwarePaginator $products */
        $products = Cache::remember(
            $this->catalogCacheKey('category:' . $categoryId, $this->filterCacheParameters($dto, $perPage)),
            $this->catalogCacheTtl(),
            function () use ($categoryId, $dto, $perPage): LengthAwarePaginator {
                $query = Product::query()
                    ->with('category')
                    ->where('category_id', $categoryId);

                $this->applyFilters($query, $dto);

                return $query
                    ->orderByDesc('id')
                    ->paginate($perPage);
            },
        );

        return $this->appendPaginationQuery($products);
    }

    public function getProduct(Product $product): Product
    {
        return $product->load('category');
    }

    public function create(ProductDto $dto): Product
    {
        $product = new Product();
        $product->fill($dto->toProductData());

        if ($dto->image) {
            $product->image = $this->storeImage($dto->image);
        }

        $product->save();
        $this->invalidateCatalogCache();

        return $product;
    }

    public function update(Product $product, ProductDto $dto): Product
    {
        $product->fill($dto->toProductData());

        if ($dto->image) {
            $this->deleteImageIfExists($product->image);
            $product->image = $this->storeImage($dto->image);
        }

        $product->save();
        $this->invalidateCatalogCache();

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->deleteImageIfExists($product->image);
        $product->delete();
        $this->invalidateCatalogCache();
    }

    private function applyFilters(Builder $query, ProductFilterDto $dto): void
    {
        if ($dto->q !== null) {
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
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }

    private function deleteImageIfExists(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function catalogCacheTtl(): int
    {
        return self::CATALOG_CACHE_TTL;
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function filterCacheParameters(ProductFilterDto $dto, int $perPage): array
    {
        return [
            'page' => Paginator::resolveCurrentPage(),
            'per_page' => $perPage,
            'q' => $dto->q,
            'min_price' => $dto->min_price,
            'max_price' => $dto->max_price,
            'in_stock' => $dto->in_stock,
            'sort' => $dto->sort,
        ];
    }

    private function appendPaginationQuery(LengthAwarePaginator $products): LengthAwarePaginator
    {
        return (clone $products)->appends(request()->only([
            'per_page',
            'q',
            'min_price',
            'max_price',
            'in_stock',
            'sort',
        ]));
    }

    /**
     * @param array<string, bool|int|string|null> $parameters
     */
    private function catalogCacheKey(string $scope, array $parameters): string
    {
        return sprintf(
            'products:catalog:v%d:%s:%s',
            $this->catalogCacheVersion(),
            $scope,
            hash('sha256', json_encode($parameters, JSON_THROW_ON_ERROR)),
        );
    }

    private function catalogCacheVersion(): int
    {
        /** @var int $version */
        $version = Cache::rememberForever(self::CATALOG_CACHE_VERSION_KEY, static fn (): int => 1);

        return $version;
    }

    private function invalidateCatalogCache(): void
    {
        Cache::add(self::CATALOG_CACHE_VERSION_KEY, 1);
        Cache::increment(self::CATALOG_CACHE_VERSION_KEY);
    }
}
