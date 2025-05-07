<?php

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function getAllOrdersWithUser(): Collection;
    public function getUserOrdersWithProducts(int $userId): Collection;
    public function createOrderWithItems(array $orderData, array $items): Order;
    public function createOrderRecord(array $data): Order;
    public function createOrderItems(int $orderId, array $items): void;
    public function updateOrderStatus(int $orderId, int $statusId): ?Order;
}
