<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');