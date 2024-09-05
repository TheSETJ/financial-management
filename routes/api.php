<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});
