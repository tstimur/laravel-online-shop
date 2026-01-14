<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Product;
use Illuminate\Support\Collection;

class SessionCartService
{
    private const string SESSION_KEY = 'cart.items';

    /**
     * @return array<int, array{product: Product, quantity: int, unit_price: string, subtotal: string}>
     */
    public function getItems(): array
    {
        $raw = $this->getRaw();
        $productIds = array_map('intval', array_keys($raw));

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($raw as $productId => $quantity) {
            $product = $products->get((int) $productId);
            if (!$product) {
                $this->removeById((int) $productId);
                continue;
            }

            $quantity = max(1, (int) $quantity);
            $unitPrice = (string) $product->price;
            $subtotal = bcmul($unitPrice, (string) $quantity, 2);

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        return $items;
    }

    public function getTotalQuantity(): int
    {
        return (int) collect($this->getRaw())->sum();
    }

    public function getTotalPrice(): string
    {
        $total = '0.00';
        foreach ($this->getItems() as $item) {
            $total = bcadd($total, $item['subtotal'], 2);
        }

        return $total;
    }

    public function add(Product $product, int $quantity = 1): int
    {
        $quantity = max(1, $quantity);

        $raw = $this->getRaw();
        $current = (int) ($raw[$product->id] ?? 0);
        $next = $current + $quantity;

        if ($product->stock <= 0) {
            $this->removeById($product->id);
            return 0;
        }

        $next = min($next, $product->stock);
        $raw[$product->id] = $next;

        $this->putRaw($raw);

        return $next;
    }

    public function setQuantity(Product $product, int $quantity): int
    {
        $quantity = max(0, $quantity);

        if ($product->stock <= 0 || $quantity === 0) {
            $this->removeById($product->id);
            return 0;
        }

        $quantity = min($quantity, $product->stock);

        $raw = $this->getRaw();
        $raw[$product->id] = $quantity;
        $this->putRaw($raw);

        return $quantity;
    }

    public function remove(Product $product): void
    {
        $this->removeById($product->id);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return array<int, int>
     */
    private function getRaw(): array
    {
        $raw = session()->get(self::SESSION_KEY, []);

        if (!is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $result[$productId] = $quantity;
        }

        return $result;
    }

    /**
     * @param array<int, int> $raw
     */
    private function putRaw(array $raw): void
    {
        session()?->put(self::SESSION_KEY, $raw);
    }

    private function removeById(int $productId): void
    {
        $raw = $this->getRaw();
        unset($raw[$productId]);
        $this->putRaw($raw);
    }
}

