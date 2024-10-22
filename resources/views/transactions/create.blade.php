@extends('layouts.app')

@section('title', 'Create Transactions')

@section('content')
<div class="container">
    <h1>Create a New Transaction</h1>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST" class="transaction-form">
        @csrf
        <div class="form-group mb-3">
            <label for="type">Type:</label>
            <select name="type" id="type" class="form-control">
                <option value="paid">Paid</option>
                <option value="received">Received</option>
                <option value="transferred">Transferred</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" required class="form-control">
        </div>

        <div class="form-group mb-3">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" class="form-control">
        </div>

        <div class="form-group d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Create Transaction</button>
            <a href="{{ url()->previous() ?: route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
