<?php

namespace App\Jobs;

use App\Http\Services\BatchService;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchOrder implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    protected Order $order;
    protected BatchService $batchService;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load(['orderItems', 'hmo']);
        $this->batchService = resolve(BatchService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $providerName = $this->order->provider_name;
        $hmoBatchPreference = $this->order->hmo->batch_preference;

        $batchMonth = $this->batchService->getHmoBatchMonth(
            $hmoBatchPreference,
            $this->order->created_at,
            $this->order->encounter_date,
        );

        $this->batchService->batchOrder(
            orderId: $this->order->id,
            hmoId: $this->order->hmo_id,
            providerName: $providerName,
            batchMonth: $batchMonth,
        );
    }
}
