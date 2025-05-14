<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartFormatter implements CartFormatterInterface
{
    /**
     * @param array<string, int> $cart
     * @return array<string, mixed>
     */
    public function getFormattedCart(array $cart): array
    {
        $formattedItems = collect();
        $totalPrice = 0;
        $totalItems = 0;

        if ($cart !== []) {
            $products = Product::with('category')->find(array_keys($cart));

            $formattedItems = $products->map(function ($product) use ($cart) {
                $quantity = $cart[(string)$product->id];
                return [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'name' => $product->name,
                    'price' => (float)$product->price,
                    'category' => $product->category->name ?? null,
                ];
            });

            $totalItems = array_sum($cart);
            $totalPrice = $products->sum(function ($product) use ($cart) {
                return $product->price * $cart[(string)$product->id];
            });
        }

        return [
            'total_items' => $totalItems,
            'total_price' => $totalPrice,
            'cart_items' => $formattedItems
        ];
    }
}
