<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Exceptions\JsonNotFoundException;
use App\Models\Order;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\Cart\CartManager;
use Illuminate\Support\Collection;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly CartManager $cartManager,
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getUserOrders(int $userId): Collection
    {
        return $this->orderRepository->getUserOrdersWithProducts($userId);
    }

    /**
     * @return Collection<int, Order>
     */
    public function getAllOrdersWithUser(): Collection
    {
        return $this->orderRepository->getAllOrdersWithUser();
    }

    /**
     * @param array<string, mixed> $orderData
     * @return Order
     * @throws \DomainException
     */
    public function createOrder(array $orderData): Order
    {
        if ($this->cartManager->isEmpty()) {
            throw new \DomainException('Добавьте товары в корзину');
        }

        $order = $this->orderRepository->createOrderWithItems(
            $orderData,
            $this->prepareCartItems($this->cartManager->getRawCart())
        );
        $this->cartManager->clear();

        return $order;
    }

    /**
     * @param array<string, int> $cartItems
     * @return array<int, mixed>
     */
    private function prepareCartItems(array $cartItems): array
    {
        $items = [];

        foreach ($cartItems as $productId => $quantity) {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }

        return $items;
    }

    /**
     * @param int $orderId
     * @param int $statusId
     * @return Order
     * @throws JsonNotFoundException
     */
    public function updateOrderStatus(int $orderId, int $statusId): Order
    {
        $order = $this->orderRepository->updateOrderStatus($orderId, $statusId);

        if(!$order) {
            throw new JsonNotFoundException('Заказ не найден');
        }

        return $order;
    }
}
