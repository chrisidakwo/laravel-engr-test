<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'providerName' => $this->provider_name,
            'encounterDate' => $this->encounter_date->format('Y-m-d'),
            'totalAmount' => (float) $this->total_amount,

            'items' => OrderItemResource::collection($this->orderItems),
        ];
    }
}
