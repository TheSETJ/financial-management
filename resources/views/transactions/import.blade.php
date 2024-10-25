@extends('layouts.app')

@section('title', 'Import Transactions')

@section('content')
<div class="container">
    <h1>Import Transactions</h1>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if (session('errors'))
        <div class="alert alert-warning">
            <strong>Errors found during import:</strong>
            <ul>
                @foreach (session('errors') as $row => $errorList)
                    <li>{{ $row }}: {{ implode(', ', $errorList) }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.import') }}" method="POST" enctype="multipart/form-data" class="transaction-import-form">
        @csrf

        <div class="form-group mb-3">
            <label for="file">Upload File:</label>
            <input type="file" name="file" id="file" accept=".csv" required class="form-control">
        </div>

        <div class="form-group d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Import Transactions</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
