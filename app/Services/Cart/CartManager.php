<?php

declare(strict_types=1);

namespace App\Services\Cart;

class CartManager
{
    public function __construct(
        private readonly CartServiceInterface $service,
        private readonly CartFormatterInterface $formatter,
    ) {
    }

    public function addItem(int $productId, int $quantity): void
    {
        $this->service->addItem($productId, $quantity);
    }

    public function updateItem(int $productId, int $quantity): void
    {
        $this->service->updateItem($productId, $quantity);
    }

    public function removeItem(int $productId): void
    {
        $this->service->removeItem($productId);
    }

    public function clear(): void
    {
        $this->service->clear();
    }


    /**
     * @return array<string, int>
     */
    public function getRawCart(): array
    {
        return $this->service->getCart();
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormattedCart(): array
    {
        return $this->formatter->getFormattedCart($this->service->getCart());
    }

    public function getItemQuantity(int $productId): int
    {
        $cart = $this->service->getCart();
        return $cart[(string)$productId] ?? 0;
    }

    public function isEmpty(): bool
    {
        return empty($this->service->getCart());
    }

    public function decreaseQuantity(int $productId, int $quantity): void
    {
        $this->service->decreaseQuantity($productId, $quantity);
    }
}
