<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Services;

use App\Http\Services\OrderService;
use App\Models\Hmo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = resolve(OrderService::class);
    }

    public function testItCreatesAnOrderCorrectly(): void
    {
        Hmo::factory()->create([
            'code' => 'HMO-A',
        ]);

        $order = $this->orderService->createOrder(
            hmoCode: 'HMO-A',
            providerName: 'ECI Healthcare',
            encounterDate: '2024-08-28',
            orderItems: [
                [
                    'name' => 'Paracetamol',
                    'unit_price' => '450',
                    'quantity' => '13',
                ]
            ]
        );

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'provider_name' => 'ECI Healthcare',
            'total_amount' => 5850,
        ]);
    }
}
