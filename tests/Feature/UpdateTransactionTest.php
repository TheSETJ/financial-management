<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('validData')]

    public function it_updates_a_transaction_when_data_is_valid($requestData, $key, $updateValue)
    {
        $transaction = Transaction::factory([
            'user_id' => $authUser = User::factory()->create()
        ])->create();

        $this->actingAs($authUser)
            ->putJson(route('transactions.update', ['transaction' => $transaction->id]), $requestData)
            ->assertOk();

        $transaction->refresh();

        $this->assertEquals($updateValue, $transaction->{$key});
    }

    #[Test]
    #[DataProvider('invalidData')]
    public function it_doesnt_update_a_transaction_when_data_is_not_valid($requestData, $validationErrors, $key, $updateValue)
    {
        $transaction = Transaction::factory([
            'user_id' => $authUser = User::factory()->create()
        ])->create();

        $this->actingAs($authUser)
            ->putJson(route('transactions.update', ['transaction' => $transaction->id]), $requestData)
            ->assertUnprocessable()
            ->assertInvalid($validationErrors);

        $transaction->refresh();

        $this->assertNotEquals($updateValue, $transaction->{$key});
    }

    #[Test]
    public function it_rejects_update_request_when_it_is_not_authenticated()
    {
        $transaction = Transaction::factory([
            'user_id' => User::factory()->create()
        ])->create();

        $this->putJson(route('transactions.update', ['transaction' => $transaction->id]), [])
            ->assertUnauthorized();
    }

    #[Test]
    public function it_rejects_update_request_when_transaction_doesnt_exist()
    {
        $this->actingAs(User::factory()->create())
            ->putJson(route('transactions.update', ['transaction' => 1]), [])->assertNotFound();
    }

    #[Test]
    public function it_rejects_update_request_when_transaction_doesnt_belong_to_authenticated_user()
    {
        $transaction = Transaction::factory([
            'user_id' => User::factory()->create()
        ])->create();

        $this->actingAs(User::factory()->create())
            ->putJson(route('transactions.update', ['transaction' => $transaction->id]), [])
            ->assertForbidden();
    }

    public static function validData(): array
    {
        $faker = \Faker\Factory::create();

        $type = $faker->randomElement(['paid', 'received', 'transferred']);
        $amount = rand(1, getrandmax());
        $description = $faker->sentence;
        $performedAt = $faker->dateTime;

        return [
            'Type of a transaction can be changed' => [
                [
                    'type' => $type,
                ],
                'type',
                $type
            ],
            'Amount of a transaction can be changed' => [
                [
                    'amount' => $amount,
                ],
                'amount',
                $amount
            ],
            'Description of a transaction can be changed' => [
                [
                    'description' => $description,
                ],
                'description',
                $description
            ],
            'Date and time of a transaction can be changed' => [
                [
                    'performed_at' => $performedAt->format('Y-m-d H:i:s')
                ],
                'performed_at',
                Carbon::parse($performedAt)
            ],
        ];
    }

    public static function invalidData(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'Type can be either of paid, received, or transferred' => [
                [
                    'type' => $type = 'invalid',
                ],
                ['type'],
                'type',
                $type
            ],
            'Amount must be an integer' => [
                [
                    'amount' => $amount = $faker->randomFloat()
                ],
                ['amount'],
                'amount',
                $amount
            ],
            'Amount cannot be zero' => [
                [
                    'amount' => $amount = 0,
                ],
                ['amount'],
                'amount',
                $amount
            ],
            'Amount cannot be negative' => [
                [
                    'amount' => $amount = -rand(1, getrandmax()),
                ],
                ['amount'],
                'amount',
                $amount
            ],
            'Description must be a string' => [
                [
                    'description' => $description = 1,
                ],
                ['description'],
                'description',
                $description
            ],
            'Performed at must be a date in "Y-m-d H:i:s" format' => [
                [
                    'performed_at' => $performedAt = 'A day of my life',
                ],
                ['performed_at'],
                'performed_at',
                $performedAt
            ],
        ];
    }
}
