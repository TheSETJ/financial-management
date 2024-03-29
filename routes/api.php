<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store')->middleware('auth');
Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update')->middleware('auth');
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index')->middleware('auth');
