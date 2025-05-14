<?php

declare(strict_types=1);

namespace App\Services\Cart;

interface CartServiceInterface
{
    /**
     * @return array<string, int>
     */
    public function getCart(): array;

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function addItem(int $productId, int $quantity): void;

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function updateItem(int $productId, int $quantity): void;

    /**
     * @param int $productId
     * @return void
     */
    public function removeItem(int $productId): void;

    /**
     * @return void
     */
    public function clear(): void;

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function decreaseQuantity(int $productId, int $quantity): void;
}
