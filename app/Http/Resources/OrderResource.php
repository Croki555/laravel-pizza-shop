<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array{
     *     status: string|null,
     *     phone: string|null,
     *     email: string|null,
     *     delivery_address: string|null,
     *     delivery_time: string|null,
     *     items: array<int, array<string, mixed>>|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->whenLoaded('status', fn () => $this->status->name),
            'phone' => $this->phone,
            'email' => $this->email,
            'delivery_address' => $this->delivery_address,
            'delivery_time' => $this->delivery_time->format('Y-m-d H:i'),
            'items' => $this->whenLoaded('itemsWithProducts',
                fn () => OrderItemResource::collection($this->itemsWithProducts)->toArray($request)
            )
        ];
    }
}
