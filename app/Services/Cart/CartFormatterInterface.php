<?php

declare(strict_types=1);

namespace App\Services\Cart;

use Illuminate\Support\Collection;

interface CartFormatterInterface
{
    /**
     * @param array<string, int> $cart
     * @return array<string, mixed>
     */
    public function getFormattedCart(array $cart): array;
}
