<?php

namespace Database\Factories;

use App\Enums\BatchPreference;
use App\Services\DataGenerator\HmoDataGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hmo>
 */
class HmoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'code' => (new HmoDataGenerator())->generateUniqueCode(),
            'email' => $this->faker->unique()->safeEmail(),
            'batch_preference' => $this->faker->randomElement(array_column(BatchPreference::cases(), 'value'))
        ];
    }
}
