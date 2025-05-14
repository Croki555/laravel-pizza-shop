<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\Order\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getUserOrders((int) Auth::id());
        return response()->json([
            'message' => 'Ваши заказы',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $this->orderService->createOrder($request->validated());
            return response()->json(['message' => 'Заказ создан'], 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
