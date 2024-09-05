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

    #[Test]
    public function it_displays_validation_errors()
    {
        $response = $this->get(route('transactions.create'));

        $response = $this->post(route('transactions.store'), ['amount' => 1]);

        $response->assertRedirect(route('transactions.create'));

        $response = $this->get(route('transactions.create'));

        $response->assertOk();
        $response->assertSee('The type field is required.');
    }
}
