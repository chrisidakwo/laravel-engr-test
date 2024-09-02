<?php

declare(strict_types=1);

namespace App\Services\DataGenerator;

use App\Models\Hmo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class HmoDataGenerator
{
    /**
     * Generates the HMO unique/identity code.
     *
     * The code has a prefix and 6 digits, separated by a hyphen. Eg: ABC-389048
     */
    public function generateUniqueCode(): string
    {
        $codePrefix = config('data.generators.hmo.code_prefix');

        // Get the last generated code directly from cache or load from DB, save to cache, and then return
        $lastGeneratedCodeDigits = Cache::remember('lastHmoCode', Date::now()->addMonths(6), function () {
            $latestHmo = Hmo::query()->latest()->first();
            if ($latestHmo) {
                $explodedCode = explode("-", $latestHmo->code);
                return (int) end($explodedCode);
            }

            return 0;
        });

        $newCodeDigits = str_pad((string) ($lastGeneratedCodeDigits + 1), 6, '0', STR_PAD_LEFT);
        $newHmoCode = sprintf("%s-%s", $codePrefix, $newCodeDigits);

        $hmoWithCodeAlreadyExists = Hmo::query()->where('code', $newHmoCode)->exists();
        if ($hmoWithCodeAlreadyExists) {
            return $this->generateUniqueCode();
        }

        // Reset the cache value
        Cache::set('lastHmoCode', (int) $newCodeDigits);

        return $newHmoCode;
    }
}
