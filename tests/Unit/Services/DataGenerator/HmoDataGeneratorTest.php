<?php

declare(strict_types=1);

namespace Tests\Unit\Services\DataGenerator;

use App\Models\Hmo;
use App\Services\DataGenerator\HmoDataGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HmoDataGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testItGeneratesHmoCodeAsExpected(): void
    {
        $generator = new HmoDataGenerator();
        $hmoCode = $generator->generateUniqueCode();

        $codePrefix = config('data.generators.hmo.code_prefix');

        [$prefix, $digits] = explode('-', $hmoCode);

        self::assertEquals($codePrefix, $prefix);
        self::assertEquals(6, strlen($digits));
        self::assertEquals(10, strlen($hmoCode));
        self::assertEquals('000001', $digits);
    }

    public function testItUsesLastSavedHmoCodeFromCache(): void
    {
        Cache::set('lastHmoCode', '89301');

        config([
            'data.generators.hmo.code_prefix' => 'ABC',
        ]);

        $generator = new HmoDataGenerator();
        $hmoCode = $generator->generateUniqueCode();

        self::assertEquals('ABC-089302', $hmoCode);
    }

    public function testItGetsLastHmoCodeFromDatabase(): void
    {
        config([
            'data.generators.hmo.code_prefix' => 'ABC',
        ]);

        Hmo::factory(4)->create();

        $generator = new HmoDataGenerator();
        $hmoCode = $generator->generateUniqueCode();

        $lastCachedCode = Cache::get('lastHmoCode');

        self::assertEquals('ABC-000005', $hmoCode);
        self::assertEquals(5, $lastCachedCode);
    }
}
