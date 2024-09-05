<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_fails_with_missing_transaction_type()
    {
        $data = [
            'type' => null,
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);
        
        $response->assertSessionHasErrors([
            'type' => 'The type field is required.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_fails_with_invalid_transaction_type()
    {
        $data = [
            'type' => 'played',
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertSessionHasErrors([
            'type' => 'The selected type is invalid.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_creates_paid_transaction_successfully()
    {
        $data = [
            'type' => 'paid',
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertRedirectToRoute('transactions.index');

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_creates_received_transaction_successfully()
    {
        $data = [
            'type' => 'received',
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertRedirectToRoute('transactions.index');

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_creates_transferred_transaction_successfully()
    {
        $data = [
            'type' => 'transferred',
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertRedirectToRoute('transactions.index');

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_fails_with_missing_amount()
    {
        $data = [
            'type' => 'paid',
            'amount' => null,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertSessionHasErrors([
            'amount' => 'The amount field is required.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_fails_with_invalid_amount_type()
    {
        $data = [
            'type' => 'paid',
            'amount' => 'a',
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertSessionHasErrors([
            'amount' => 'The amount field must be a number.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_fails_with_negative_amount()
    {
        $data = [
            'type' => 'paid',
            'amount' => -1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertSessionHasErrors([
            'amount' => 'The amount field must be greater than 0.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_fails_with_zero_amount()
    {
        $data = [
            'type' => 'paid',
            'amount' => 0,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertSessionHasErrors([
            'amount' => 'The amount field must be greater than 0.'
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_creates_transaction_with_positive_amount()
    {
        $data = [
            'type' => 'paid',
            'amount' => 1,
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertRedirectToRoute('transactions.index');

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_creates_transaction_with_description()
    {
        $data = [
            'type' => 'paid',
            'amount' => 1,
            'description' => 'Buying bread'
        ];

        $response = $this->post(route('transactions.store'), $data);

        $response->assertRedirectToRoute('transactions.index');

        $this->assertDatabaseHas('transactions', $data);
    }
}
