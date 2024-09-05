<?php

namespace App\Http\Controllers;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transactions.index', [
            'transactions' => Transaction::select(['id', 'type', 'amount', 'description', 'created_at'])
                ->latest()
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
}
