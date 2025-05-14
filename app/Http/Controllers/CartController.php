<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RemoveCartRequest;
use App\Http\Requests\StoreCartRequest;
use App\Services\Cart\CartManager;
use Illuminate\Http\JsonResponse;


class CartController extends Controller
{
    public function __construct(
        private readonly CartManager $cartManager
    ) {}


    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Ваша корзина',
            'data' => $this->cartManager->getFormattedCart()
        ]);
    }

    public function add(StoreCartRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->cartManager->addItem(
            $validated['product_id'],
            $validated['quantity']
        );

        return response()->json([
            'message' => 'Товар успешно добавлен в корзину',
            'data' => $this->cartManager->getFormattedCart()
        ]);
    }

    public function remove(RemoveCartRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->cartManager->decreaseQuantity(
            $validated['product_id'],
            $validated['quantity']);

        return response()->json([
            'message' => 'Количество товара уменьшено',
            'data' => $this->cartManager->getFormattedCart()
        ]);
    }

    public function clear(): JsonResponse
    {
        $this->cartManager->clear();

        return response()->json([
            'message' => 'Корзина успешно очищена',
            'data' => $this->cartManager->getFormattedCart()
        ]);
    }

}
