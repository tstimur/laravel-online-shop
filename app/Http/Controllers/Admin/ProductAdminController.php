<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\ProductDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Service\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request, ProductService $service): RedirectResponse
    {
        $product = $service->create(ProductDto::fromRequest($request));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product created.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(
        ProductUpdateRequest $request,
        Product $product,
        ProductService $service
    ): RedirectResponse {
        $service->update($product, ProductDto::fromRequest($request));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product, ProductService $service): RedirectResponse
    {
        $service->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}
