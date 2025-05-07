<?php
namespace App\Services\Order;

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

    public function getUserOrders(int $userId): Collection
    {
        return $this->orderRepository->getUserOrdersWithProducts($userId);
    }

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
}
