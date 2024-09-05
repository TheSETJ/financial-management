<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTransactionPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_create_transaction_form()
    {
        $response = $this->get(route('transactions.create'));

        $response->assertOk();
        $response->assertViewIs('transactions.create');
        $response->assertSee('Create a New Transaction');
        $response->assertSee('Type:');
        $response->assertSee('Amount:');
        $response->assertSee('Description:');
    }
}
