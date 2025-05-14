<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    /**
     * @return Collection<int, Order>
     */
    public function getAllOrdersWithUser(): Collection;

    /**
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getUserOrders(int $userId): Collection;

    /**
     * @param array<string, mixed> $orderData
     * @return Order
     */
    public function createOrder(array $orderData): Order;

    /**
     * @param int $orderId
     * @param int $statusId
     * @return Order
     */
    public function updateOrderStatus(int $orderId, int $statusId): Order;
}
