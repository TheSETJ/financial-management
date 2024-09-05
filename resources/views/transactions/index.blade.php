@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container">
    <h1>Transactions</h1>

    <a href="{{ route('transactions.create') }}" class="btn-create">
        Create New Transaction
    </a>

    @if($transactions->isEmpty())
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
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->type }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    @endif
</div>
@endsection
