<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RegisterOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegisterOrder>
 */
final class RegisterOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('###########')
        ];
    }
}
