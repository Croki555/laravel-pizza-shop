<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\GuestCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $guestToken = request()->cookie('guest_token');
        $cartItems = GuestCart::when($guestToken, fn($query) => $query->where('guest_token', $guestToken))
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Ваша корзина пуста. Добавьте товары перед оформлением заказа.'], 422);
        }

        return CartResource::collection($cartItems);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
