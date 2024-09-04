<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function store()
    {
        $validated = request()->validate([
            'type' => ['required', Rule::in(['paid', 'received', 'transferred'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['string'],
        ]);

        Transaction::create($validated);

        return response()->json([], 201);
    }
}
