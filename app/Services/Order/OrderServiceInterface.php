<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    public function getUserOrders(int $userId): Collection;
    public function createOrder(array $orderData): Order;
}
