<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $order = $this->orderService->createOrder(
            hmoCode: $data['hmo_code'],
            providerName: $data['provider_name'],
            encounterDate: $data['encounter_date'],
            orderItems: $data['items'],
        );

        return response()->json(OrderResource::make($order), Response::HTTP_CREATED);
    }
}
