<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_existing_transaction()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->deleteJson(route('api.transactions.destroy', $transaction));

        $response->assertNoContent();

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    #[Test]
    public function it_fails_when_transaction_does_not_exist()
    {
        $response = $this->deleteJson(route('api.transactions.destroy', 0));

        $response->assertNotFound();
    }
}
