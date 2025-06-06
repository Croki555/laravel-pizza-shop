<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\StrictIntegerValidation;
use App\Services\Cart\CartManager;
use Illuminate\Foundation\Http\FormRequest;

class RemoveCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                new StrictIntegerValidation(),
                function ($attribute, $value, $fail) {
                    $manager = app(CartManager::class);

                    if (!$manager->getItemQuantity($value)) {
                        $fail('Товар не найден в вашей корзине');
                    }
                },
            ],
            'quantity' => [
                'required',
                new StrictIntegerValidation(),
                'min:1',
                'max:5',
                function ($attribute, $value, $fail) {
                    if (!is_int($value)) {
                        $fail('Количество должно быть целым числом');
                    }
                },
            ],
        ];
    }
}
