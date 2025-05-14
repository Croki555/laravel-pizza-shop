<?php

declare(strict_types=1);

namespace App\Services\Cart;

use Illuminate\Support\ItemNotFoundException;

class SessionCartService implements CartServiceInterface
{
    /**
     * @return array<string, int>
     */
    public function getCart(): array
    {
        $cart = session()->get('cart', []);

        return array_map('intval', $cart);
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addItem(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Количество должно быть положительным');
        }

        $cart = $this->getCart();
        $cart[(string)$productId] = ($cart[(string)$productId] ?? 0) + $quantity;
        session()->put('cart', $cart);
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws \RuntimeException
     */
    public function updateItem(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (!isset($cart[(string)$productId])) {
            throw new \RuntimeException('Продукт не найден в корзине');
        }

        $newQuantity = $cart[(string)$productId] + $quantity;
        if ($newQuantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart[$productId] = $newQuantity;
        session()->put('cart', $cart);
    }

    /**
     * @param int $productId
     * @return void
     */
    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[(string)$productId]);
        session()->put('cart', $cart);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        session()->put('cart', []);
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws ItemNotFoundException
     */
    public function decreaseQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (!isset($cart[(string)$productId])) {
            throw new ItemNotFoundException("Product $productId not found in cart");
        }

        $newQuantity = $cart[(string)$productId] - $quantity;

        if ($newQuantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart[$productId] = $newQuantity;

        session()->put('cart', $cart);
    }
}
