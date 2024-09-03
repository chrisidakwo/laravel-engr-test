<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Hmo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testSubmitOrderScreenIsRendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testOrderCanBeCreated()
    {
        Hmo::factory()->create([
            'name' => 'HMO-A',
            'code' => 'HMO-A',
        ]);

        $response = $this->json('POST', '/api/orders', [
            'hmo_code' => 'HMO-A',
            'provider_name' => 'Verizon Health Services',
            'encounter_date' => '2024-08-29',
            'items' => [
                [
                    'name' => 'Paracetamol',
                    'unit_price' => 450,
                    'quantity' => 3,
                ]
            ],
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'providerName',
            'encounterDate',
            'totalAmount',
            'items',
        ]);

        $response->assertJsonFragment([
            'providerName' => 'Verizon Health Services',
            'encounterDate' => '2024-08-29',
            'totalAmount' => 1350,
        ]);

        $response->assertJsonPath('items.0.name', 'Paracetamol');
        $response->assertJsonPath('items.0.price', 450);
        $response->assertJsonPath('items.0.qty', 3);
        $response->assertJsonPath('items.0.subTotal', 1350);
    }

    public function testCannotSubmitInvalidHmoData(): void
    {
        $response = $this->json('POST', '/api/orders', [
            'hmo_code' => 'HMO-A',
            'provider_name' => 'Verizon Health Services',
            'encounter_date' => '2024-08-29',
            'items' => [
                [
                    'name' => 'Paracetamol',
                    'unit_price' => 450,
                    'quantity' => 3,
                ]
            ],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('hmo_code');
        $response->assertJsonFragment([
            'message' => 'The selected hmo code is invalid.',
        ]);
    }

    public function testCannotSubmitWithInvalidUnitPriceOrQuantity()
    {
        Hmo::factory()->create([
            'name' => 'HMO-A',
            'code' => 'HMO-A',
        ]);

        $response = $this->json('POST', '/api/orders', [
            'hmo_code' => 'HMO-A',
            'provider_name' => 'Verizon Health Services',
            'encounter_date' => '2024-08-29',
            'items' => [
                [
                    'name' => 'Paracetamol',
                    'unit_price' => 0,
                    'quantity' => null,
                ]
            ],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['items.0.unit_price', 'items.0.quantity']);
    }
}
