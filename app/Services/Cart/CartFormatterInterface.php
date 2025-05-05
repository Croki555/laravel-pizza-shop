<?php

namespace App\Services\Cart;

interface CartFormatterInterface
{
    public function getFormattedCart(array $cart): array;
}
