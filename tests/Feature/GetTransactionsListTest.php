<?php

namespace Tests\Feature;

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
        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertViewIs('transactions.index');
        $response->assertSee('No transactions found.');
    }

    #[Test]
    public function it_returns_transactions_sorted_newest_first()
    {
        Transaction::factory()->count(3)->sequence(
            ['description' => 'Transaction A', 'created_at' => now()->subMinutes(3)],
            ['description' => 'Transaction B', 'created_at' => now()->subMinutes(2)],
            ['description' => 'Transaction C', 'created_at' => now()->subMinutes(1)],
        )->create();

        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertViewIs('transactions.index');
        $response->assertSeeInOrder(['Transaction C', 'Transaction B', 'Transaction A']);
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

        $response = $this->get(route('transactions.index', ['per-page' => 10]));

        $response->assertOk();
        $response->assertViewIs('transactions.index');
        $response->assertSeeInOrder([
            'Transaction Z',
            'Transaction Y',
            'Transaction X',
            'Transaction W',
            'Transaction V',
            'Transaction U',
            'Transaction T',
            'Transaction S',
            'Transaction R',
            'Transaction Q',
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

        $response = $this->get(route('transactions.index', ['per-page' => 10, 'page' => 2]));

        $response->assertOk();
        $response->assertViewIs('transactions.index');
        $response->assertSeeInOrder([
            'Transaction P',
            'Transaction O',
            'Transaction N',
            'Transaction M',
            'Transaction L',
            'Transaction K',
            'Transaction J',
            'Transaction I',
            'Transaction H',
            'Transaction G',
        ]);
    }
}
