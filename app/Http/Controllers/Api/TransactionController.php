<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function store(TransactionStoreRequest $request)
    {
        Transaction::create($request->validated());

        return response()->json([], 201);
    }

    public function index()
    {
        return response()->json(
            Transaction::select(['id', 'type', 'amount', 'description', 'created_at'])
                ->latest()
                ->simplePaginate(
                    request()->query('per-page', 20)
                )
        );
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->noContent();
    }
}
