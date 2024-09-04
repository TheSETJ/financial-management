<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(TransactionType::getValues()),
            'amount' => $this->faker->numberBetween(1, 1000000),
        ];
    }
}
