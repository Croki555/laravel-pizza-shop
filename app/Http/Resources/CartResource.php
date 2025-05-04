<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="CartResource",
 *     type="object",
 *     @OA\Property(property="product_id", type="integer", example=7),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Фанта", nullable=true),
 *     @OA\Property(property="price", type="number", format="float", example=585.00, nullable=true),
 *     @OA\Property(property="category", type="string", example="Напитки", nullable=true)
 * )
 */
class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'name' => $this->whenLoaded('product', function() {
                return $this->product->name;
            }),
            'price' => $this->whenLoaded('product', function() {
                return (float)$this->product->price; // Явное преобразование в float
            }),
            'category' => $this->whenLoaded('product.category', function() {
                return $this->product->category->name;
            }),
        ];
    }
}
