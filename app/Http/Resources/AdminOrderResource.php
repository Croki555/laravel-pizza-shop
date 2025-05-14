<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
{
    /**
     * @return array{
     *     status: string|null,
     *     delivery_address: string|null,
     *     delivery_time: string|null,
     *     user: UserResource|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->whenLoaded('status', fn () => $this->status->name),
            'delivery_address' => $this->delivery_address,
            'delivery_time' => $this->delivery_time->format('Y-m-d H:i'),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
        ];
    }
}
