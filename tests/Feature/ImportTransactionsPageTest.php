<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportTransactionsPageTest extends TestCase
{
    #[Test]
    public function it_displays_the_import_transactions_page()
    {
        $response = $this->get(route('transactions.import.form'));

        $response->assertOk();
        $response->assertViewIs('transactions.import');
        $response->assertSee('Import Transactions');
        $response->assertSee('form');
        $response->assertSee('file');
        $response->assertSee('Upload File');
    }

    #[Test]
    public function it_displays_message_and_errors_after_import()
    {
        Session::flash('message', 'Import complete. 1 records processed successfully. 3 errors found.');
        Session::flash('errors', [
            'Row #1' => ['The selected type is invalid.'],
            'Row #2' => ['The amount field must be a number.', 'The amount field must be greater than 0.'],
            'Row #3' => ['The amount field is required.']
        ]);

        $response = $this->get(route('transactions.import.form'));

        $response->assertOk();
        $response->assertSee('Import complete. 1 records processed successfully. 3 errors found.');
        $response->assertSee('The selected type is invalid.');
        $response->assertSee('The amount field must be a number.');
        $response->assertSee('The amount field must be greater than 0.');
        $response->assertSee('The amount field is required.');
    }
}
