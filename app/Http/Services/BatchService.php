<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Enums\BatchPreference;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class BatchService
{
    public function getHmoBatchMonth(BatchPreference $batchPreference, Carbon $orderDate, Carbon $encounterDate): string
    {
        return match ($batchPreference) {
            BatchPreference::ORDER_RECEIPT_DATE => $orderDate->format('M Y'),
            BatchPreference::ENCOUNTER_DATE => $encounterDate->format('M Y'),
        };
    }

    public function batchOrder(int $orderId, int $hmoId, string $providerName, string $batchMonth): Batch
    {
        $batch = Batch::query()->firstOrCreate([
            'hmo_id' => $hmoId,
            'provider_name' => $providerName,
            'name' => "$providerName $batchMonth"
        ]);

        $batch->orders()->attach($orderId, [
            'created_at' => Date::now(),
        ]);

        return $batch;
    }
}
