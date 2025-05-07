<?php
namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderRepository implements OrderRepositoryInterface
{
    public function getUserOrdersWithProducts(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['status', 'itemsWithProducts.product'])
            ->get();
    }

    public function createOrderWithItems(array $orderData, array $items): Order
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'phone' => $orderData['phone'],
                'email' => $orderData['email'],
                'delivery_address' => $orderData['delivery_address'],
                'delivery_time' => $orderData['delivery_time']
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

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
}
