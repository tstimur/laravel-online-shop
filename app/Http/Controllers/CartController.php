<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Service\SessionCartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(SessionCartService $cart): Factory|View
    {
        $defaultAddress = null;
        if (Auth::check()) {
            $defaultAddress = Auth::user()
                ?->addresses()
                ->where('is_default', true)
                ->first();
        }



        return view('cart.index', [
            'items' => $cart->getItems(),
            'totalQuantity' => $cart->getTotalQuantity(),
            'totalPrice' => $cart->getTotalPrice(),
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function store(Product $product, Request $request, SessionCartService $cart): JsonResponse|RedirectResponse
    {

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $cart->add($product, (int) ($data['quantity'] ?? 1));

        return $this->respond($request, $cart);

    }

    /**
     * Обновление корзины
     *
     * @param Product $product
     * @param Request $request
     * @param SessionCartService $cart
     * @return JsonResponse|RedirectResponse
     */
    public function update(Product $product, Request $request, SessionCartService $cart): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cart->setQuantity($product, (int) $data['quantity']);

        return $this->respond($request, $cart);
    }

    public function destroy(Product $product, Request $request, SessionCartService $cart): JsonResponse|RedirectResponse
    {
        $cart->remove($product);

        return $this->respond($request, $cart);
    }

    public function clear(Request $request, SessionCartService $cart): JsonResponse|RedirectResponse
    {
        $cart->clear();

        return $this->respond($request, $cart);
    }

    private function respond(Request $request, SessionCartService $cart): JsonResponse|RedirectResponse
    {
        $payload = [
            'cartCount' => $cart->getTotalQuantity(),
        ];

        if ($request->expectsJson()) {
            $payload['html'] = view('cart._content', [
                'items' => $cart->getItems(),
                'totalQuantity' => $cart->getTotalQuantity(),
                'totalPrice' => $cart->getTotalPrice(),
            ])->render();

            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('cartCount', $payload['cartCount']);
    }
}
