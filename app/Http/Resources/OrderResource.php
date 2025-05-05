<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->whenLoaded('status', fn () => $this->status->name),
            'phone' => $this->phone,
            'email' => $this->email,
            'delivery_address' => $this->delivery_address,
            'delivery_time' => $this->delivery_time->format('Y-m-d H:i'),
            'items' => OrderItemResource::collection($this->whenLoaded('itemsWithProducts'))
        ];
    }
}
