@extends('layouts.Backoffice')

@section('page-title', 'Balance Sheet')
@section('header', 'Balance Sheet')

@section('content')
<div class="container-fluid py-4">

    {{-- Back Button & Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📋 Balance Sheet</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            ← Back
        </a>
    </div>

    {{-- Main Balance Sheet Card --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

            {{-- Two Column Layout (Accounting Standard) --}}
            <div class="row g-4">

                {{-- ✅ LEFT SIDE : ASSETS --}}
                <div class="col-md-6">
                    <div class="border-end-md pe-3">
                        <h4 class="text-primary mb-3 pb-2 border-bottom">📦 Assets</h4>

                        {{-- Current Assets --}}
                        <div class="mb-3">
                            <h6 class="text-secondary fw-semibold">Current Assets</h6>
                            <div class="ps-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Cash in Hand</span>
                                    <span class="font-monospace">{{ number_format($cash, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Bank Balance</span>
                                    <span class="font-monospace">{{ number_format($bank, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Debtors</span>
                                    <span class="font-monospace">{{ number_format($debtors, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Closing Stock</span>
                                    <span class="font-monospace">{{ number_format($stock, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Future: Fixed Assets যোগ করতে পারো --}}
                        {{-- <div class="mb-3">
                            <h6 class="text-secondary">Fixed Assets</h6>
                            <div class="ps-3">...</div>
                        </div> --}}

                        <hr class="my-3">
                        <div class="d-flex justify-content-between fw-bold fs-6">
                            <span>Total Assets</span>
                            <span class="font-monospace">{{ number_format($assets, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ✅ RIGHT SIDE : LIABILITIES & CAPITAL --}}
                <div class="col-md-6">
                    <h4 class="text-danger mb-3 pb-2 border-bottom">⚖️ Liabilities & Capital</h4>

                    {{-- Liabilities Section --}}
                    <div class="mb-4">
                        <h6 class="text-secondary fw-semibold">Liabilities</h6>
                        <div class="ps-3">
                            @if(($creditors ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>Creditors</span>
                                <span class="font-monospace">{{ number_format($creditors, 2) }}</span>
                            </div>
                            @endif

                            @if(($loan ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>Loan</span>
                                <span class="font-monospace">{{ number_format($loan, 2) }}</span>
                            </div>
                            @endif

                            @if(($businessLoss ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span>Business Loss</span>
                                <span class="font-monospace">{{ number_format($businessLoss, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Capital Section --}}
                    <div class="mb-4">
                        <h6 class="text-secondary fw-semibold">Capital / Equity</h6>
                        <div class="ps-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Net Profit (Retained)</span>
                                <span class="font-monospace">{{ number_format($netProfit, 2) }}</span>
                            </div>
                            {{-- Future: Capital introduced, drawings etc. --}}
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="d-flex justify-content-between fw-bold fs-6">
                        <span>Total Liabilities & Capital</span>
                        <span class="font-monospace">{{ number_format($liabilities + $capital + $netProfit, 2) }}</span>
                    </div>

                    {{-- ✅ Accounting Equation Check --}}
                    @if(abs($assets - ($liabilities + $capital)) < 0.01)
                        <div class="alert alert-success mt-3 py-2 text-center small">
                            ✅ <strong>Accounting Equation Satisfied</strong> : Assets = Liabilities + Capital
                        </div>
                    @else
                        <div class="alert alert-danger mt-3 py-2 text-center small">
                            ⚠️ <strong>Error</strong> : Assets �n� Liabilities + Capital. Please check data.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Optional: Notes / Footer --}}
    <div class="text-muted small text-center mt-4">
        Generated on {{ now()->format('d-m-Y h:i A') }} | TaxFirm Bangladesh
    </div>
</div>
@endsection