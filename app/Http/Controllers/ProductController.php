<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Service\ProductService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request, ProductService $service): Factory|View
    {
        $dto = ProductFilterDto::fromRequest($request);
        $products = $service->getProducts($dto);

        return view('products.index', [
            'products' => $products,
            'dto'      => $dto,
        ]);
    }
}
