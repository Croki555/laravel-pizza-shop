<?php

namespace App\Services\Cart;

interface CartServiceInterface
{
    public function getCart(): array;
    public function addItem(int $productId, int $quantity): void;
    public function updateItem(int $productId, int $quantity): void;
    public function removeItem(int $productId): void;
    public function clear(): void;
    public function decreaseQuantity(int $productId, int $quantity): void;
}
