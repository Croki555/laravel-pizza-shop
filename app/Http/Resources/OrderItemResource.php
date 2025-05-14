<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array{
     *     name: string|null,
     *     price: float|null,
     *     quantity: float|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->whenLoaded('product', fn () => $this->product->name),
            'price' => $this->whenLoaded('product', fn () => $this->product->price),
            'quantity' => $this->quantity,
        ];
    }
}
