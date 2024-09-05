@extends('layouts.app')

@section('title', 'Create Transactions')

@section('content')
<h1>Create a New Transaction</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('transactions.store') }}" method="POST">
    @csrf
    <label for="type">Type:</label>
    <select name="type" id="type">
        <option value="paid">Paid</option>
        <option value="received">Received</option>
        <option value="transferred">Transferred</option>
    </select>
    <br>
    <label for="amount">Amount:</label>
    <input type="number" name="amount" id="amount" required>
    <br>
    <label for="description">Description:</label>
    <input type="text" name="description" id="description">
    <br>
    <button type="submit">Create Transaction</button>
</form>
@endsection
