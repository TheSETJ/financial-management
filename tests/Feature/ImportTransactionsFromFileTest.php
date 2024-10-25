<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportTransactionsFromFileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_imports_transactions_from_csv_file()
    {
        $data = [
            ['type' => 'received', 'amount' => 1000, 'description' => 'Income'],
            ['type' => 'transferred', 'amount' => 500, 'description' => 'Transfer'],
            ['type' => 'paid', 'amount' => 200, 'description' => 'Payment'],
        ];

        $content = collect($data)->map(function ($row) {
            return implode(',', $row);
        })->implode("\n");

        $file = UploadedFile::fake()->createWithContent('transactions.csv', $content)->mimeType('text/csv');

        $response = $this->post(route('transactions.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('transactions.import.form'));
        $response->assertSessionHas('message', 'Import complete. 3 records processed successfully. 0 errors found.');

        foreach ($data as $row) {
            $this->assertDatabaseHas('transactions', $row);
        }
    }

    #[Test]
    public function it_handles_rows_with_errors()
    {
        $data = [
            ['type' => 'recieved', 'amount' => 1000, 'description' => 'Income #1'], // invalid type
            ['type' => 'received', 'amount' => 'invalid_amount', 'description' => 'Income #2'], // invalid amount
            ['type' => 'transferred', 'amount' => null, 'description' => 'Transfer'], // missing amount
            ['type' => 'paid', 'amount' => 200, 'description' => 'Payment']
        ];

        $content = collect($data)->map(function ($row) {
            return implode(',', $row);
        })->implode("\n");

        $file = UploadedFile::fake()->createWithContent('transactions.csv', $content)->mimeType('text/csv');

        $response = $this->post(route('transactions.import'), [
            'file' => $file,
        ]);
    
        $response->assertRedirect(route('transactions.import.form'));
        $response->assertSessionHas('message', 'Import complete. 1 records processed successfully. 3 errors found.');

        $response->assertSessionHas('errors', [
            'Row #1' => ['The selected type is invalid.'],
            'Row #2' => ['The amount field must be a number.', 'The amount field must be greater than 0.'],
            'Row #3' => ['The amount field is required.']
        ]);

        $this->assertDatabaseMissing('transactions', $data[0]);
        $this->assertDatabaseMissing('transactions', $data[1]);
        $this->assertDatabaseMissing('transactions', $data[2]);
        $this->assertDatabaseHas('transactions', $data[3]);
    }

    #[Test]
    public function it_fails_with_missing_file()
    {
        $response = $this->post(route('transactions.import'));

        $response->assertSessionHasErrors([
            'file' => 'The file field is required.',
        ]);

        $this->assertDatabaseEmpty('transactions');
    }

    #[Test]
    public function it_fails_with_invalid_file_format()
    {
        $file = UploadedFile::fake()->create('transactions.txt')->mimeType('text/plain');

        $data = [
            'file' => $file,
        ];

        $response = $this->post(route('transactions.import'), $data);

        $response->assertSessionHasErrors([
            'file' => 'The file field must be a file of type: csv.'
        ]);
    }
}
