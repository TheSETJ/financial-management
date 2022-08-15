<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testCreatingTransaction()
    {
        $transactionData = [
            'amount' => $this->faker->randomDigitNot(0) * 1000,
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear()->format(
                $this->faker->randomElement(['Y-m-d H:i:s', 'Y-m-d'])
            ),
        ];

        $this->postJson(route('transactions.store'), $transactionData)->assertCreated();

        $this->assertDatabaseHas(
            'transactions',
            ['created_at' => Carbon::parse($transactionData['created_at'])->format('Y-m-d H:i:s')] + $transactionData
        );
    }

    public function testCreatingTransactionWithoutAmount()
    {
        $transactionData = [
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];

        $this->postJson(route('transactions.store'), $transactionData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function testCreatingTransactionWithNegetiveAmount()
    {
        $transactionData = [
            'amount' => -$this->faker->randomDigitNot(0) * 1000,
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];

        $this->postJson(route('transactions.store'), $transactionData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function testCreatingTransactionWithAmountZero()
    {
        $transactionData = [
            'amount' => 0,
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];

        $this->postJson(route('transactions.store'), $transactionData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function testCreatingTransactionWithoutDescription()
    {
        $transactionData = [
            'amount' => $this->faker->randomDigitNot(0) * 1000,
            'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];

        $this->postJson(route('transactions.store'), $transactionData)->assertCreated();

        $this->assertDatabaseHas('transactions', $transactionData + ['description' => null]);
    }

    public function testCreatingTransactionWithoutCreateDate()
    {
        $transactionData = [
            'amount' => $this->faker->randomDigitNot(0) * 1000,
            'description' => $this->faker->sentence(),
        ];

        $this->postJson(route('transactions.store'), $transactionData)->assertCreated();

        $this->assertDatabaseHas('transactions', $transactionData + ['created_at' => now()->format('Y-m-d H:i:s')]);
    }

    public function testCreatingTransactionWithBadFormattedCreateDate()
    {
        $transactionData = [
            'amount' => $this->faker->randomDigitNot(0) * 1000,
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear()->format('Ymd'),
        ];

        $this->postJson(route('transactions.store'), $transactionData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['created_at']);
    }
}
