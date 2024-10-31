<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::any('/', function() {
    return redirect(route('transactions.index'));
});

Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
Route::get('/transactions/import', [TransactionController::class, 'importForm'])->name('transactions.import.form');
Route::post('/transactions/import', [TransactionController::class, 'import'])->name('transactions.import');