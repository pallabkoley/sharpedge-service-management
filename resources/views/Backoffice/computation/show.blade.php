@extends('layouts.Backoffice')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4>Computation Details #{{ $computation->id }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Client:</strong> {{ $computation->client->name ?? 'N/A' }}</p>
            <p><strong>Client Type:</strong> {{ $computation->clientType->name ?? 'N/A' }}</p>
            <p><strong>Financial Year:</strong> {{ $computation->financial_year }}</p>
            <p><strong>Assessment Year:</strong> {{ $computation->assessment_year }}</p>
            <p><strong>Status:</strong> {{ ucfirst($computation->status) }}</p>
            <hr>
            <h5>Summary</h5>
            <table class="table table-bordered w-50">
                <tr><th>Gross Income</th><td>{{ number_format($summary->gross_income, 2) }}</td></tr>
                <tr><th>Total Deductions</th><td>{{ number_format($summary->total_deduction, 2) }}</td></tr>
                <tr><th>Taxable Income</th><td>{{ number_format($summary->taxable_income, 2) }}</td></tr>
                <tr><th>Tax Amount</th><td>{{ number_format($summary->tax_amount, 2) }}</td></tr>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('backoffice.computations.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection