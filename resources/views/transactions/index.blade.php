@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container">
    <h1>Transactions</h1>

    @if (session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3 d-flex">
        <a href="{{ route('transactions.create') }}" class="btn btn-create">Create Transaction</a>
        <a href="{{ route('transactions.import.form') }}" class="btn btn-create">Import Transactions</a>
    </div>

    @if ($transactions->isEmpty())
        <p>No transactions found.</p>
    @else
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ ucfirst($transaction->type) }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-create btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
