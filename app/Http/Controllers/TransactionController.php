<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'created_at'=> ['nullable', 'date_format:Y-m-d H:i:s,Y-m-d'],
        ]);

        Transaction::create([
            'amount' => $request->amount,
            'description' => $request->description,
            'created_at' => $request->created_at ?? now(),
        ]);

        return response()->json([], 201);
    }
}
