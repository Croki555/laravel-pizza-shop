<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Rules\StrictInteger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                    if (!$product) {
                        return;
                    }

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
                'regex:/^[1-5]$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
          'quantity.regex' => 'Только цифра от 1 до 5'
        ];
    }

    protected function getCurrentQuantity(int $categoryId): int
    {
        return collect(session()->get('cart', []))
            ->filter(function ($quantity, $productId) use ($categoryId) {
                $product = Product::find($productId);
                return $product && $product->category_id == $categoryId;
            })
            ->sum();
    }
}
