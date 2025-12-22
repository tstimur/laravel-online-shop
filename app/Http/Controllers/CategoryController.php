<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Service\ProductService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): Factory|View
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category, ProductFilterRequest $request, ProductService $service): Factory|View
    {
        $dto = ProductFilterDto::fromRequest($request);

        $products = $service->getProductsByCategoryId($category->id, $dto);
        $maxProductPrice = $service->getMaxProductPriceForCategoryId($category->id);

        return view('products.index', [
            'products' => $products,
            'dto' => $dto,
            'maxProductPrice' => $maxProductPrice,
            'pageTitle' => $category->name,
            'breadcrumbs' => [
                [
                    'label' => 'Каталог',
                    'url' => route('categories.index'),
                ],
                [
                    'label' => $category->name,
                    'url' => route('categories.show', $category),
                ],
            ],
            'backUrl' => route('categories.index'),
        ]);
    }
}
