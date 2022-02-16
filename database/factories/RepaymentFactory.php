<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class RepaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $loan = Loan::factory()->create()->refresh();

        return [
            'loan_id' => $loan->id,
            'due' => $this->faker->randomFloat(null, 100, 10000),
            'interest' => $this->faker->randomFloat(null, 10, 100),
            'outstanding' => $this->faker->randomFloat(null, 100, 1000),
            'due_on' => $this->faker->dateTime(),
            'paid_on' => $this->faker->dateTime(),
        ];
    }
}
