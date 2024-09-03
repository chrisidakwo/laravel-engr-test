<?php

namespace Database\Seeders;

use App\Enums\BatchPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private array $hmos = [
        ['name'=>'HMO A', 'code'=> 'HMO-A', 'batch_preference' => BatchPreference::ENCOUNTER_DATE->value],
        ['name'=>'HMO B', 'code'=> 'HMO-B', 'batch_preference' => BatchPreference::ORDER_RECEIPT_DATE->value],
        ['name'=>'HMO C', 'code'=> 'HMO-C', 'batch_preference' => BatchPreference::ORDER_RECEIPT_DATE->value],
        ['name'=>'HMO D', 'code'=> 'HMO-D', 'batch_preference' => BatchPreference::ENCOUNTER_DATE->value],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert(array_map(function ($hmo) {
            return [
                ...$hmo,
                'email' => fake()->safeEmail,
            ];
        }, $this->hmos));
    }
}
