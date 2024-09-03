<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BatchPreference;
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

    public function testItReturnsErrorOnEmptyRequestBody()
    {
        $response = $this->json('POST', '/api/orders', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['hmo_code', 'provider_name', 'encounter_date', 'items']);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'hmo_code',
                'provider_name',
                'encounter_date',
                'items',
            ]
        ]);
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

    public function testItCorrectlyBatchesAnOrder(): void
    {
        $hmo1 = Hmo::factory()->create([
            'name' => 'HMO-A',
            'code' => 'HMO-A',
            'batch_preference' => BatchPreference::ENCOUNTER_DATE->value,
        ]);

        $hmo2 = Hmo::factory()->create([
            'name' => 'HMO-B',
            'code' => 'HMO-B',
            'batch_preference' => BatchPreference::ORDER_RECEIPT_DATE->value,
        ]);

        $orderDetails = [
            'provider_name' => 'Verizon Health Services',
            'encounter_date' => '2024-08-29',
            'items' => [
                [
                    'name' => 'Paracetamol',
                    'unit_price' => 450,
                    'quantity' => 3,
                ]
            ],
        ];

        $this->json('POST', '/api/orders', [
            'hmo_code' => $hmo1->code,
            ...$orderDetails,
        ]);

        $this->json('POST', '/api/orders', [
            'hmo_code' => $hmo2->code,
            ...$orderDetails,
        ]);


        $this->assertDatabaseCount('batches', 2);

        // Since $hmo1 uses encounter date, batch name will carry Aug 2024
        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo1->id,
            'provider_name' => "Verizon Health Services",
            'name' => "Verizon Health Services Aug 2024"
        ]);

        // Since $hmo2 uses order sent date, batch name will carry Sep 2024
        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo2->id,
            'provider_name' => "Verizon Health Services",
            'name' => "Verizon Health Services Sep 2024"
        ]);
    }
}
