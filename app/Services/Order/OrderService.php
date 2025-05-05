<?php
namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Cart\CartManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly CartManager $cartManager
    ) {}

    public function getUserOrders(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['status', 'itemsWithProducts.product'])
            ->get();
    }

    public function createOrder(array $orderData): Order
    {
        if ($this->cartManager->isEmpty()) {
            throw new \DomainException('Добавьте товары в корзину');
        }

        return DB::transaction(function () use ($orderData) {
            $order = $this->createOrderRecord($orderData);
            $this->createOrderItems($order, $this->cartManager->getRawCart());
            $this->cartManager->clear();
            return $order;
        });
    }

    private function createOrderRecord(array $data): Order
    {
        return Order::create([
            'user_id' => Auth::id(),
            'phone' => $data['phone'],
            'email' => $data['email'],
            'delivery_address' => $data['delivery_address'],
            'delivery_time' => $data['delivery_time'],
        ]);
    }

    private function createOrderItems(Order $order, array $cartItems): void
    {
        $items = array_map(function ($productId, $quantity) use ($order) {
            return [
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, array_keys($cartItems), $cartItems);

        OrderItem::insert($items);
    }
}
