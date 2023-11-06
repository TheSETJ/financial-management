<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function store(CreateTransactionRequest $request)
    {
        Transaction::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json([], 201);
    }
}
