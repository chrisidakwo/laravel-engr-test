<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Jobs\BatchOrder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderService
{
    public function __construct(protected HmoService $hmoService)
    {
    }

    public function createOrder(string $hmoCode, string $providerName, string $encounterDate, array $orderItems): Order
    {
        $order = Order::query()->create([
            'hmo_id' => $this->hmoService->getHmoByCode($hmoCode)?->id,
            'provider_name' => $providerName,
            'encounter_date' => $encounterDate,
            'total_amount' => array_reduce($orderItems, function ($sum, $orderItem) {
                return $sum + ($orderItem['quantity'] * $orderItem['unit_price']);
            }, 0),
        ]);

        // Create order items
        $order->orderItems()->createMany(array_map(function ($item) use ($order) {
            return [
                'order_id' => $order->id,
                'item' => $item['name'],
                'price' => $item['unit_price'],
                'qty' => $item['quantity'],
            ];
        }, $orderItems));

        // Batch order
        dispatch(new BatchOrder($order));

        return $order;
    }
}
