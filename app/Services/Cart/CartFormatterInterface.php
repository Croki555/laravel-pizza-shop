<?php

declare(strict_types=1);

namespace App\Services\Cart;

interface CartFormatterInterface
{
    /**
     * @param array<string, int> $cart
     * @return array<string, mixed>
     */
    public function getFormattedCart(array $cart): array;
}
