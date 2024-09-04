<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function store(TransactionStoreRequest $request)
    {
        Transaction::create($request->validated());

        return response()->json([], 201);
    }
}
