<?php

namespace App\Services\Cart;

use Illuminate\Support\ItemNotFoundException;

class SessionCartService implements CartServiceInterface
{
    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    public function addItem(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Количество должно быть положительным');
        }

        $cart = $this->getCart();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        session()->put('cart', $cart);
    }

    public function updateItem(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            throw new \RuntimeException('Продукт не найден в корзине');
        }

        $newQuantity = $cart[$productId] + $quantity;
        if ($newQuantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart[$productId] = $newQuantity;
        session()->put('cart', $cart);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        session()->put('cart', $cart);
    }

    public function clear(): void
    {
        session()->put('cart', []);
    }

    public function decreaseQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            throw new ItemNotFoundException("Product $productId not found in cart");
        }

        $newQuantity = $cart[$productId] - $quantity;

        if ($newQuantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart[$productId] = $newQuantity;

        session()->put('cart', $cart);
    }
}
