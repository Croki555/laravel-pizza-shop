<?php

namespace App\Http\Requests;

use App\Models\GuestCart;
use App\Models\Product;
use App\Rules\StrictInteger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required', new StrictInteger, 'exists:products,id',
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
            'guest_token' => ['nullable', 'string', 'min:40', 'max:40', 'exists:guest_carts,guest_token'],
            'quantity' => ['required', new StrictInteger, 'min:1', 'max:5']
        ];
    }

    protected function getCurrentQuantity(int $categoryId): int
    {
        if (!$this->input('guest_token')) {
            return 0;
        }

        return GuestCart::where('guest_token', $this->input('guest_token'))
            ->whereHas('product', fn($q) => $q->where('category_id', $categoryId))
            ->sum('quantity');
    }
}
