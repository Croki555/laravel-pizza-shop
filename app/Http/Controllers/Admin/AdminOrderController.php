<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\AdminOrderResource;
use App\Services\Order\OrderServiceInterface;
use Illuminate\Http\JsonResponse;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getAllOrdersWithUser();

        return response()->json([
            'message' => "Заказы пользователей",
            'total_orders' => count($orders),
            'data' => AdminOrderResource::collection($orders),
        ]);
    }

    public function updateStatus(int $id, UpdateOrderStatusRequest $request): JsonResponse
    {
        $order = $this->orderService->updateOrderStatus($id, $request->status_id);

        return response()->json([
            'message' => 'Статус заказа изменен',
            'data' => new AdminOrderResource($order),
        ]);
    }
}
