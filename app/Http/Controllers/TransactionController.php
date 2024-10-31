<?php

namespace App\Http\Controllers;
use App\Http\Requests\TransactionImportRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\LazyCollection;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transactions.index', [
            'transactions' => Transaction::select(['id', 'type', 'amount', 'description', 'created_at'])
                ->latest()
                ->latest('id')
                ->simplePaginate(
                    request()->query('per-page', 20)
                )
        ]);
    }

    public function store(TransactionStoreRequest $request)
    {
        Transaction::create($request->validated());

        return redirect()->route('transactions.index');
    }

    public function create()
    {
        return view('transactions.create');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index');
    }

    public function importForm()
    {
        return view('transactions.import');
    }

    public function import(TransactionImportRequest $request)
    {
        $filePath = $request->file('file')->getRealPath();

        $file = fopen($filePath, 'r');

        if ($file === false) {
            $error = error_get_last();

            logger()->error('Failed to open uploaded file.', [
                'file' => $filePath,
                'error' => Arr::get($error, 'message'),
            ]);

            return redirect()->route('transactions.import.form')->withErrors([
                'file' => 'Failed to process the uploaded file due to a server-side error.',
            ]);
        }

        $errors = [];
        $validRows = [];

        LazyCollection::make(function () use ($file) {
            while (($row = fgetcsv($file)) !== false) {
                yield $row;
            }
        })->each(function ($row, $key) use (&$validRows, &$errors) {
            if (empty(array_filter($row))) {
                return;
            }

            $data = [
                'type' => Arr::get($row, 0),
                'amount' => Arr::get($row, 1),
                'description' => Arr::get($row, 2),
            ];

            $validator = validator($data, (new TransactionStoreRequest)->rules());

            if ($validator->fails()) {
                $errors['Row #' . ($key + 1)] = $validator->errors()->all();
            } else {
                $validRows[] = $data + ['created_at' => now(), 'updated_at' => now()];
            }
        });

        fclose($file);

        Transaction::insert($validRows);

        return redirect()->route('transactions.import.form')->with([
            'message' => sprintf(
                'Import complete. %d records processed successfully. %d errors found.',
                count($validRows),
                count($errors)
            ),
            'errors' => $errors,
        ]);
    }
}
