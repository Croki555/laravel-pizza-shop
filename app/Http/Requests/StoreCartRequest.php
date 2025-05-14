<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use App\Rules\StrictIntegerValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StoreCartRequest extends FormRequest
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
                new StrictIntegerValidation,
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    $product = Product::find($value);
                    if (!$product) {
                        return;
                    }

                    $currentQuantity = $this->getCurrentQuantity($product->category_id);
                    $newQuantity = $this->input('quantity');

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
                new StrictIntegerValidation,
            ],
        ];
    }


    protected function getCurrentQuantity(int $categoryId): int
    {
        /** @var array<int|string, int> $sessionCart */
        $sessionCart = session()->get('cart', []);

        return collect($sessionCart)
            ->filter(function (int $quantity, int|string $productId) use ($categoryId): bool {
                $product = Product::find($productId);
                return $product && $product->category_id === $categoryId;
            })
            ->sum();
    }
}
