<?php

namespace App\Http\Requests;

use App\Models\GuestCart;
use App\Models\Product;
use App\Rules\StrictInteger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreCartRequest",
 *     type="object",
 *     required={"product_id", "quantity"},
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         example=1
 *     )
 * )
 */
class StoreCartRequest extends FormRequest
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
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    $product = Product::find($value);
                    $currentQuantity = $this->getCurrentQuantity($product->category_id);
                    $newQuantity = $this->input('quantity', 1);

                    if ($product->category_id == 1 && ($currentQuantity + $newQuantity) > 10) {
                        $fail('Максимум 10 пицц. Текущее количество: ' . $currentQuantity);
                    }

                    if ($product->category_id == 2 && ($currentQuantity + $newQuantity) > 20) {
                        $fail('Максимум 20 напитков. Текущее количество: ' . $currentQuantity);
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
                }
            ]
        ];
    }

    protected function getCurrentQuantity(int $categoryId): int
    {
        $token = $this->cookie('guest_token');
        if (!$token) {
            return 0;
        }

        return GuestCart::where('guest_token', $token)
            ->whereHas('product', fn($q) => $q->where('category_id', $categoryId))
            ->sum('quantity');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'guest_token' => $this->cookie('guest_token')
        ]);
    }
}
