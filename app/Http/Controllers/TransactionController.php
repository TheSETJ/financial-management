<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\ListTransactionsRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
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

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());

        return response()->json([], 200);
    }

    public function index(ListTransactionsRequest $request)
    {
        $transactions = Transaction::where('user_id', auth()->id());

        foreach (explode(' ', $request->search) as $searchPhrase) {
            $transactions->where('description', 'like', '%' . $searchPhrase . '%');
        }

        if ($request->start_of_performed_at) {
            $transactions->where('performed_at','>=', $request->start_of_performed_at);
        }

        if ($request->end_of_performed_at) {
            $transactions->where('performed_at','<=', $request->end_of_performed_at);
        }

        if ($request->min_amount) {
            $transactions->where('amount', '>=', $request->min_amount);
        }

        if ($request->max_amount) {
            $transactions->where('amount', '<=', $request->max_amount);
        }

        $transactions->orderByDesc('performed_at')->orderByDesc('id');

        return TransactionResource::collection(
            $transactions->paginate($request->per_page ?? 15)
        );
    }
}
