<?php

namespace App\Http\Requests;

use App\Services\Cart\CartManager;
use App\Rules\StrictInteger;
use Illuminate\Foundation\Http\FormRequest;

class RemoveCartRequest extends FormRequest
{
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
                    $manager = app(CartManager::class);

                    if (!$manager->getItemQuantity($value)) {
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
                }
            ]
        ];
    }
}
