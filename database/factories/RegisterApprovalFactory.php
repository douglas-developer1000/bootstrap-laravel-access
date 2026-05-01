<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RegisterApproval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegisterApproval>
 */
final class RegisterApprovalFactory extends Factory
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
            'phone' => fake()->numerify('###########'),
            'token' => fake()->md5(),
            'expiration_data' => fake()->dateTime()->modify('+1 day')
        ];
    }
}
