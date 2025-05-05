<?php

namespace App\Services\Cart;

use App\Models\Product;

class CartFormatter implements CartFormatterInterface
{
    public function getFormattedCart(array $cart): array
    {
        $formattedItems = collect();
        $totalPrice = 0;
        $totalItems = 0;

        if (!empty($cart)) {
            $products = Product::with('category')->find(array_keys($cart));

            $formattedItems = $products->map(function ($product) use ($cart) {
                $quantity = $cart[$product->id];
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
                return $product->price * $cart[$product->id];
            });
        }

        return [
            'total_items' => $totalItems,
            'total_price' => $totalPrice,
            'cart_items' => $formattedItems
        ];
    }
}
