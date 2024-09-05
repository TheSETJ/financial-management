<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetTransactionsListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_no_transactions_when_none_exist()
    {
        $response = $this->getJson(route('api.transactions.index'));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_returns_transactions_sorted_newest_first()
    {
        Transaction::factory()->count(3)->sequence(
            ['description' => 'Transaction A', 'created_at' => now()->subMinutes(3)],
            ['description' => 'Transaction B', 'created_at' => now()->subMinutes(2)],
            ['description' => 'Transaction C', 'created_at' => now()->subMinutes(1)],
        )->create();

        $response = $this->getJson(route('api.transactions.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'type', 'amount', 'description', 'created_at'],
            ],
        ]);
        $response->assertJson([
            'data' => [
                ['description' => 'Transaction C'],
                ['description' => 'Transaction B'],
                ['description' => 'Transaction A'],
            ]
        ]);
    }

    #[Test]
    public function it_returns_specified_number_of_transactions_per_page()
    {
        Transaction::factory()->createMany(
            collect(range('A', 'Z'))
                ->map(
                    fn ($alphabet) => [
                        'description' => "Transaction $alphabet",
                        'created_at' => now()->subMinutes(ord('Z') - ord($alphabet))
                    ]
                )
                ->toArray()
        );

        $response = $this->getJson(route('api.transactions.index', ['per-page' => 10]));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJson([
            'data' => [
                ['description' => 'Transaction Z'],
                ['description' => 'Transaction Y'],
                ['description' => 'Transaction X'],
                ['description' => 'Transaction W'],
                ['description' => 'Transaction V'],
                ['description' => 'Transaction U'],
                ['description' => 'Transaction T'],
                ['description' => 'Transaction S'],
                ['description' => 'Transaction R'],
                ['description' => 'Transaction Q'],
            ]
        ]);
    }

    #[Test]
    public function it_returns_correct_transactions_for_specified_page()
    {
        Transaction::factory()->createMany(
            collect(range('A', 'Z'))
                ->map(
                    fn ($alphabet) => [
                        'description' => "Transaction $alphabet",
                        'created_at' => now()->subMinutes(ord('Z') - ord($alphabet))
                    ]
                )
                ->toArray()
        );

        $response = $this->getJson(route('api.transactions.index', ['per-page' => 10, 'page' => 2]));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJson([
            'data' => [
                ['description' => 'Transaction P'],
                ['description' => 'Transaction O'],
                ['description' => 'Transaction N'],
                ['description' => 'Transaction M'],
                ['description' => 'Transaction L'],
                ['description' => 'Transaction K'],
                ['description' => 'Transaction J'],
                ['description' => 'Transaction I'],
                ['description' => 'Transaction H'],
                ['description' => 'Transaction G'],
            ]
        ]);
    }
}
