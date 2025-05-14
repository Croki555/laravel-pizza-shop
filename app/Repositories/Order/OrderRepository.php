<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @return Collection<int, Order>
     */
    public function getAllOrdersWithUser(): Collection
    {
        return Order::with(['user', 'status'])->get();
    }


    /**
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getUserOrdersWithProducts(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['status', 'itemsWithProducts.product'])
            ->get();
    }


    /**
     * @param array<string, mixed> $orderData
     * @param array<int, mixed> $items
     * @return Order
     * @throws \Exception
     */
    public function createOrderWithItems(array $orderData, array $items): Order
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'phone' => $orderData['phone'],
                'email' => $orderData['email'],
                'delivery_address' => $orderData['delivery_address'],
                'delivery_time' => $orderData['delivery_time'],
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return Order
     */
    public function createOrderRecord(array $data): Order
    {
        return Order::create([
            'user_id' => Auth::id(),
            'phone' => $data['phone'],
            'email' => $data['email'],
            'delivery_address' => $data['delivery_address'],
            'delivery_time' => $data['delivery_time'],
        ]);
    }

    /**
     * @param int $orderId
     * @param array<int, array{product_id: int|string, quantity: int|string}> $items
     * @return void
     */
    public function createOrderItems(int $orderId, array $items): void
    {
        $preparedItems = array_map(function ($item) use ($orderId) {
            return [
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $items);

        OrderItem::insert($preparedItems);
    }

    /**
     * @param int $orderId
     * @param int $statusId
     * @return Order|null
     */
    public function updateOrderStatus(int $orderId, int $statusId): ?Order
    {
        $order = Order::find($orderId);

        if (!$order) {
            return null;
        }

        $order->update(['status_id' => $statusId]);

        return $order->load(['status', 'user']);
    }
}
