<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    /**
     * @return Collection<int, Order>
     */
    public function getAllOrdersWithUser(): Collection;

    /**
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getUserOrdersWithProducts(int $userId): Collection;

    /**
     * @param array<string, mixed> $orderData
     * @param array<int, mixed> $items
     * @return Order
     * @throws \Exception
     */
    public function createOrderWithItems(array $orderData, array $items): Order;

    /**
     * @param array<string, mixed> $data
     * @return Order
     */
    public function createOrderRecord(array $data): Order;

    /**
     * @param int $orderId
     * @param array<int, array{product_id: int|string, quantity: int|string}> $items
     * @return void
     */
    public function createOrderItems(int $orderId, array $items): void;

    /**
     * @param int $orderId
     * @param int $statusId
     * @return Order|null
     */
    public function updateOrderStatus(int $orderId, int $statusId): ?Order;
}
