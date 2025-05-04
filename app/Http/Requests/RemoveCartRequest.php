<?php

namespace App\Http\Requests;

use App\Models\GuestCart;
use App\Models\Product;
use App\Rules\StrictInteger;
use Illuminate\Foundation\Http\FormRequest;

class RemoveCartRequest extends FormRequest
{
    protected ?GuestCart $cartItem = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                new StrictInteger,
                function ($attribute, $value, $fail) {
                    if (!$token = $this->getGuestToken()) {
                        $fail('Корзина не найдена (отсутствует guest_token)');
                        return;
                    }

                    $this->cartItem = GuestCart::with('product')
                        ->where('guest_token', $token)
                        ->where('product_id', $value)
                        ->first();

                    if (!$this->cartItem) {
                        $fail('Товар не найден в вашей корзине');
                    }
                }
            ],
            'quantity' => [
                'required',
                new StrictInteger,
                'min:1',
                'max:5',
                function ($attribute, $value, $fail) {

                    if (!is_int($value)) {
                        $fail('Количество должно быть целым числом');
                    }


                    if ($value <= 0) {
                        $fail('Количество должно быть положительным числом');
                    }

                    if ($this->cartItem && $value > $this->cartItem->quantity) {
                        $fail(sprintf(
                            'Максимальное количество для удаления: %d (в корзине %d)',
                            $this->cartItem->quantity,
                            $this->cartItem->quantity
                        ));
                    }
                }
            ]
        ];
    }

    protected function getGuestToken(): ?string
    {
        return $this->cookie('guest_token');
    }

    public function getCartItem(): ?GuestCart
    {
        return $this->cartItem;
    }

    public function getQuantityToRemove(): int
    {
        return $this->input('quantity', $this->cartItem?->quantity ?? 0);
    }
}
