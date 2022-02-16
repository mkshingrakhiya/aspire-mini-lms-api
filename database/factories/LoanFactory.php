<?php

namespace Database\Factories;

use App\Enums\PaymentFrequency;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $reviewer = User::factory()->create(['role' => UserRole::REVIEWER]);

        return [
            'client_id' => $client->id,
            'amount' => $this->faker->numberBetween(10000, 10000000),
            'term' => $this->faker->numberBetween(3, 52 * 10),
            'annual_interest_rate' => $this->faker->randomFloat(null, 4, 12),
            'repayment_frequency' => PaymentFrequency::WEEKLY,
            'reviewer_id' => $reviewer->id,
        ];
    }
}
