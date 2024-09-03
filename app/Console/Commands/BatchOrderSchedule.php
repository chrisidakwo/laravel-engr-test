<?php

namespace App\Console\Commands;

use App\Mail\BatchOrderProcessedMail;
use App\Models\Batch;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class BatchOrderSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:batch-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch orders for the previous month';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $lastMonth = date('M', strtotime("last day of -1 month"));
        $lastMonthYear = date('Y', strtotime("last day of -1 month"));

        Batch::query()->with(['hmo'])->where('name', 'LIKE', "%{$lastMonth} {$lastMonthYear}")
            ->chunk(100, function (Collection $batches) {
                $batches->each(function (Batch $batch) {
                    Mail::to([$batch->hmo->email])->send(new BatchOrderProcessedMail($batch));
                });
            });
    }
}
