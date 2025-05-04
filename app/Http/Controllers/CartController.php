<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemoveCartRequest;
use App\Http\Requests\StoreCartRequest;
use App\Http\Resources\CartResource;
use App\Models\GuestCart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="CartItemResponse",
 *     type="object",
 *     @OA\Property(property="product_id", type="integer", example=7),
 *     @OA\Property(property="quantity", type="integer", example=6),
 *     @OA\Property(property="name", type="string", example="Фанта"),
 *     @OA\Property(property="price", type="number", format="float", example=585.00)
 * )
 *
 * @OA\Schema(
 *     schema="CartResponseData",
 *     type="object",
 *     @OA\Property(property="total_items", type="integer", example=6),
 *     @OA\Property(property="total_price", type="number", format="float", example=6882.00),
 *     @OA\Property(
 *         property="cart_items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CartItemResponse")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CartSuccessResponse",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Товар успешно добавлен в корзину"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/CartResponseData"
 *     )
 * )
 */
class CartController extends Controller
{
    /**
     * Получить содержимое корзины
     *
     * @OA\Get(
     *     path="/api/cart",
     *     tags={"Cart"},
     *     summary="Получить содержимое корзины",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/CartResponseData"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Корзина не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Корзина не найдена")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $guestToken = request()->cookie('guest_token');

        if (!$guestToken) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        $cartItems = GuestCart::with('product')
            ->where('guest_token', $guestToken)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        // Успешный ответ
        return response()->json([
            'data' => [
                'total_items' => $cartItems->sum('quantity'),
                'total_price' => $cartItems->sum(fn($item) => $item->quantity * $item->product->price),
                'cart_items' => CartResource::collection($cartItems)
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cart/add",
     *     tags={"Cart"},
     *     summary="Добавить товар в корзину",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCartRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(ref="#/components/schemas/CartSuccessResponse")
     *     )
     * )
     */
    public function add(StoreCartRequest $request)
    {
        $validated = $request->validated();

        $existingToken = $request->cookie('guest_token');
        $isValidToken = $existingToken && GuestCart::where('guest_token', $existingToken)->exists();
        $guestToken = $isValidToken ? $existingToken : Str::random(40);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $cartItem = GuestCart::firstOrNew([
            'guest_token' => $guestToken,
            'product_id' => $productId
        ]);

        $cartItem->quantity = ($cartItem->quantity ?? 0) + $quantity;
        $cartItem->save();


        $cartItems = GuestCart::with('product')
            ->where('guest_token', $guestToken)
            ->get();

        $responseData = [
            'message' => 'Товар успешно добавлен в корзину',
            'data' => [
                'total_items' => $cartItems->sum('quantity'),
                'total_price' => $cartItems->sum(fn($item) => $item->quantity * $item->product->price),
                'cart_items' => CartResource::collection($cartItems)
            ]
        ];


        if (!$existingToken || !$isValidToken) {
            return response()->json($responseData)->cookie(
                'guest_token',
                $guestToken,
                60 * 24 * 7, // 7 дней
                '/',
                null,
                config('app.env') === 'production',
                false
            );
        }

        return response()->json($responseData);
    }


    /**
     * Удалить товар из корзины
     *
     * @OA\Delete(
     *     path="/api/cart/remove",
     *     tags={"Cart"},
     *     summary="Удалить товар из корзины",
     *     description="Удаляет указанное количество товара",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные для удаления товара",
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=5, description="ID товара"),
     *             @OA\Property(property="quantity", type="integer", example=2, nullable=true, description="Количество для удаления (по умолчанию удаляет весь товар)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Товар успешно удален",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Товар успешно удален из корзины"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/CartResponseData"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизованный доступ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Корзина не найдена")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Товар не найден в корзине",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Товар не найден в вашей корзине")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="array",
     *                     @OA\Items(type="string", example="Максимальное количество для удаления: 3 (в корзине 3)")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function remove(RemoveCartRequest $request)
    {
        $cartItem = $request->getCartItem();
        $quantity = $request->getQuantityToRemove();
        $guestToken = $request->cookie('guest_token');


        if ($quantity >= $cartItem->quantity) {
            $cartItem->delete();
        } else {
            $newQuantity = $cartItem->quantity - $quantity;
            if ($newQuantity <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->decrement('quantity', $quantity);
            }
        }


        $cartItems = GuestCart::with('product')
            ->where('guest_token', $guestToken)
            ->get();

        $responseData = [
            'message' => 'Товар успешно удален из корзины',
            'data' => [
                'total_items' => $cartItems->sum('quantity'),
                'total_price' => $cartItems->sum(fn($item) => $item->quantity * $item->product->price),
                'cart_items' => CartResource::collection($cartItems)
            ]
        ];


        $response = response()->json($responseData);
        if ($cartItems->isEmpty()) {
            $response->withoutCookie('guest_token');
        }

        return $response;
    }

    /**
     * Полная очистка корзины
     *
     * @OA\Delete(
     *     path="/api/cart/clear",
     *     tags={"Cart"},
     *     summary="Очистить корзину полностью",
     *     @OA\Response(
     *         response=200,
     *         description="Корзина очищена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Корзина успешно очищена")
     *         )
     *     )
     * )
     */

    public function clear()
    {
        $guestToken = request()->cookie('guest_token');

        if (!$guestToken) {
            return response()->json(['message' => 'Корзина уже пуста']);
        }

        $deletedCount = GuestCart::where('guest_token', $guestToken)->delete();

        return response()
            ->json(['message' => 'Корзина успешно очищена'])
            ->withoutCookie('guest_token');
    }

}
