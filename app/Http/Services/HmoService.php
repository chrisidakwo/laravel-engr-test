<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Hmo;

class HmoService
{
    public function getHmoByCode(string $hmoCode): ?Hmo
    {
        return Hmo::query()->where('code', $hmoCode)->first();
    }
}
