<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('validData')]
    public function it_creates_a_transaction_when_data_is_valid($requestData, $databaseData)
    {
        $this->actingAs($authUser =User::factory()->create())
            ->postJson(route('transactions.store'), $requestData)
            ->assertCreated();

        // now() returns a UTC datetime when used in a data provider
        // but in a test case now() respects the configured timezone
        $databaseData['performed_at'] = $databaseData['performed_at'] ?? now();
        $databaseData['user_id'] = $authUser->id;

        $this->assertDatabaseHas('transactions', $databaseData);
    }

    #[Test]
    #[DataProvider('invalidData')]
    public function it_doesnt_create_a_transaction_when_data_is_not_valid($requestData, $validationErrors)
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('transactions.store'), $requestData)
            ->assertUnprocessable()
            ->assertInvalid($validationErrors);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_doesnt_create_a_transaction_when_request_is_not_authenticated()
    {
        $this->postJson(route('transactions.store'), [])->assertUnauthorized();
    }

    public static function validData(): array
    {
        $faker = \Faker\Factory::create();

        $type = $faker->randomElement(['paid', 'received', 'transferred']);
        $amount = rand(1, getrandmax());
        $description = $faker->sentence;
        $performedAt = $faker->dateTime;

        return [
            'A transaction requires a type and a positive integer amount' => [
                [
                    'type' => $type,
                    'amount' => $amount,
                ],
                [
                    'type' => $type,
                    'amount' => $amount,
                    'description' => null,
                    'performed_at' => null,
                ]
            ],
            'A transaction can have a description' => [
                [
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $description,
                ],
                [
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $description,
                    'performed_at' => null,
                ],
            ],
            'A transaction may have manual date and time' => [
                [
                    'type' => $type,
                    'amount' => $amount,
                    'performed_at' => $performedAt->format('Y-m-d H:i:s'),
                ],
                [
                    'type' => $type,
                    'amount' => $amount,
                    'description' => null,
                    'performed_at' => Carbon::parse($performedAt)
                ],
            ],
        ];
    }

    public static function invalidData(): array
    {
        $faker = \Faker\Factory::create();

        $types = ['paid', 'received', 'transferred'];

        return [
            'Type is required' => [
                [
                    'amount' => rand(1, getrandmax()),
                ],
                ['type'],
            ],
            'Type can be either of paid, received, or transferred' => [
                [
                    'type' => 'invalid',
                    'amount' => rand(1, getrandmax()),
                ],
                ['type'],
            ],
            'Amount is required' => [
                [
                    'type' => $faker->randomElement($types),
                ],
                ['amount'],
            ],
            'Amount must be an integer' => [
                [
                    'type' => $faker->randomElement($types),
                    'amount' => $faker->randomFloat(),
                ],
                ['amount'],
            ],
            'Amount cannot be zero' => [
                [
                    'type' => $faker->randomElement($types),
                    'amount' => 0,
                ],
                ['amount'],
            ],
            'Amount cannot be negative' => [
                [
                    'type' => $faker->randomElement($types),
                    'amount' => -rand(1, getrandmax()),
                ],
                ['amount'],
            ],
            'Description must be a string' => [
                [
                    'type' => $faker->randomElement($types),
                    'amount' => rand(1, getrandmax()),
                    'description' => 1,
                ],
                ['description'],
            ],
            'Performed at must be a date in "Y-m-d H:i:s" format' => [
                [
                    'type' => $faker->randomElement($types),
                    'amount' => rand(1, getrandmax()),
                    'performed_at' => 'A day of my life',
                ],
                ['performed_at'],
            ],
        ];
    }
}