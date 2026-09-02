<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\ProductDto;
use App\DTO\ProductFilterDto;
use App\Models\Category;
use App\Models\Product;
use App\Service\ProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductCatalogCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('sku')->unique();
            $table->string('image')->nullable();
            $table->string('status')->default(Product::STATUS_ACTIVE);
            $table->foreignId('category_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        parent::tearDown();
    }

    public function test_same_catalog_request_uses_cached_products(): void
    {
        Product::factory()->create(['name' => 'Кэшируемый товар', 'price' => 1000]);

        DB::enableQueryLog();
        $this->get('/products?sort=price_asc')->assertOk()->assertSee('Кэшируемый товар');
        $firstRequestProductQueries = $this->productSelectQueries();

        DB::flushQueryLog();
        $this->get('/products?sort=price_asc')->assertOk()->assertSee('Кэшируемый товар');

        self::assertNotEmpty($firstRequestProductQueries);
        self::assertSame([], $this->productSelectQueries());
    }

    public function test_filter_page_and_category_have_independent_cached_results(): void
    {
        $firstCategory = Category::query()->create(['name' => 'Первая', 'slug' => 'pervaya']);
        $secondCategory = Category::query()->create(['name' => 'Вторая', 'slug' => 'vtoraya']);

        Product::factory()->create(['name' => 'Недорогой', 'price' => 100, 'category_id' => $firstCategory->id]);
        Product::factory()->create(['name' => 'Дорогой', 'price' => 900, 'category_id' => $secondCategory->id]);

        $this->get('/products?max_price=200')->assertOk()->assertSee('Недорогой')->assertDontSee('Дорогой');
        $this->get('/products?min_price=800')->assertOk()->assertSee('Дорогой')->assertDontSee('Недорогой');
        $this->get('/categories/pervaya')->assertOk()->assertSee('Недорогой')->assertDontSee('Дорогой');
        $this->get('/categories/vtoraya')->assertOk()->assertSee('Дорогой')->assertDontSee('Недорогой');
    }

    public function test_catalog_and_max_price_are_invalidated_after_product_changes(): void
    {
        $service = app(ProductService::class);
        $product = Product::factory()->create(['name' => 'Исходный товар', 'price' => 100]);
        $filter = new ProductFilterDto();

        self::assertSame('100', $service->getMaxProductPrice());
        self::assertSame('Исходный товар', $service->getProducts($filter)->first()->name);

        $service->update($product, $this->productDto('Обновлённый товар', 200, $product->sku));

        self::assertSame('200', $service->getMaxProductPrice());
        self::assertSame('Обновлённый товар', $service->getProducts($filter)->first()->name);

        $created = $service->create($this->productDto('Новый товар', 300, 'NEW-CACHE-SKU'));

        self::assertSame('300', $service->getMaxProductPrice());
        self::assertCount(2, $service->getProducts($filter));

        $service->delete($created);

        self::assertSame('200', $service->getMaxProductPrice());
        self::assertCount(1, $service->getProducts($filter));
    }

    public function test_pagination_sorting_and_query_parameters_are_not_mixed_between_cache_hits(): void
    {
        for ($number = 1; $number <= 11; $number++) {
            Product::factory()->create([
                'name' => sprintf('Товар %02d', $number),
                'price' => $number * 100,
            ]);
        }

        $this->get('/products?sort=name_asc&per_page=10')
            ->assertOk()
            ->assertViewHas('products', static function (LengthAwarePaginator $products): bool {
                return $products->total() === 11
                    && $products->count() === 10
                    && $products->getCollection()->pluck('name')->all() === [
                        'Товар 01',
                        'Товар 02',
                        'Товар 03',
                        'Товар 04',
                        'Товар 05',
                        'Товар 06',
                        'Товар 07',
                        'Товар 08',
                        'Товар 09',
                        'Товар 10',
                    ];
            });

        $this->get('/products?sort=name_asc')
            ->assertOk()
            ->assertViewHas(
                'products',
                static function (LengthAwarePaginator $products): bool {
                    $secondPageUrl = $products->url(2);

                    return str_contains($secondPageUrl, 'sort=name_asc')
                        && !str_contains($secondPageUrl, 'per_page=10');
                },
            );

        $this->get('/products?sort=name_asc&per_page=10&page=2')
            ->assertOk()
            ->assertSee('Товар 11')
            ->assertDontSee('Товар 01');
    }

    public function test_category_max_price_is_invalidated_after_product_update(): void
    {
        $service = app(ProductService::class);
        $category = Category::query()->create(['name' => 'Категория', 'slug' => 'kategoriya']);
        $product = Product::factory()->create([
            'price' => 100,
            'category_id' => $category->id,
        ]);

        self::assertSame('100', $service->getMaxProductPriceForCategoryId($category->id));

        $service->update(
            $product,
            $this->productDto('Обновлённый товар', 200, $product->sku, $category->id),
        );

        self::assertSame('200', $service->getMaxProductPriceForCategoryId($category->id));
    }

    /**
     * @return list<string>
     */
    private function productSelectQueries(): array
    {
        return array_values(array_filter(
            array_map(
                static fn (array $query): string => $query['query'],
                DB::getQueryLog(),
            ),
            static fn (string $query): bool => str_contains(strtolower($query), 'select')
                && str_contains(strtolower($query), '"products"'),
        ));
    }

    private function productDto(string $name, float $price, string $sku, ?int $categoryId = null): ProductDto
    {
        return new ProductDto(
            name: $name,
            description: null,
            price: $price,
            stock: 10,
            sku: $sku,
            status: Product::STATUS_ACTIVE,
            categoryId: $categoryId,
            image: null,
        );
    }
}
