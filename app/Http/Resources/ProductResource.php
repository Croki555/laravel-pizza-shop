<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array{
     *     name: string|null,
     *     description: string|null,
     *     price: float|null,
     *     category: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->whenLoaded('category', fn () => $this->category->name),
        ];
    }
}
