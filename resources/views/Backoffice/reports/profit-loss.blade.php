@extends('layouts.Backoffice')

@section('page-title', 'Income Statement')
@section('header', 'Income Statement')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="text-center mb-4">
        <h3 class="fw-bold mb-0">THE SHARP EDGE INDIA</h3>
        <h5 class="mb-0">INCOME STATEMENT</h5>
        <small>For the Financial Year Ended 31-Mar-2025</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th class="text-end">Amount (₹)</th>
                    </tr>
                </thead>

                <tbody>

                    {{-- Salary --}}
                    <tr>
                        <td>Salary Income</td>
                        <td class="text-end">{{ number_format($salary,2) }}</td>
                    </tr>

                    {{-- House Property --}}
                    <tr>
                        <td>Less: Income from House Property (Loss)</td>
                        <td class="text-end text-danger">
                            ({{ number_format(abs($houseProperty),2) }})
                        </td>
                    </tr>

                    {{-- Business --}}
                    <tr>
                        <td>Business / Professional Income</td>
                        <td class="text-end">{{ number_format($businessProfit,2) }}</td>
                    </tr>

                    {{-- Capital Gain --}}
                    <tr>
                        <td>Capital Gains</td>
                        <td class="text-end">{{ number_format($capitalGain,2) }}</td>
                    </tr>

                    {{-- Other --}}
                    <tr>
                        <td>Other Income</td>
                        <td class="text-end">{{ number_format($otherIncome,2) }}</td>
                    </tr>

                    {{-- TOTAL --}}
                    <tr class="fw-bold border-top border-3">
                        <td>Total Income</td>
                        <td class="text-end">{{ number_format($totalIncome,2) }}</td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="card-footer text-muted small text-end">
            Generated on {{ now()->format('d-m-Y h:i A') }}
        </div>
    </div>

</div>
@endsection