<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestCartRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductResource;
use App\Models\GuestCart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestCartController extends Controller
{
    public function add(StoreGuestCartRequest $request)
    {
        $validated = $request->validated();

        $guestToken = $validated['guest_token'] ?? Str::random(40);
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $cartItem = GuestCart::where('guest_token', $guestToken)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            GuestCart::create([
                'guest_token' => $guestToken,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }


        $cartItems = GuestCart::with('product')
            ->where('guest_token', $guestToken)
            ->get();

        $totalItems = $cartItems->sum('quantity');
        $totalPrices = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'guest_token' => $guestToken,
            'total_items' => $totalItems,
            'total_price' => $totalPrices,
            'message' => 'Товар успешно добавлен в корзину',
            'cart_items' => CartResource::collection($cartItems),
        ]);
    }

}
