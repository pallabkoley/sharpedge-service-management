@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- LEFT SIDE -->
        <div class="col-lg-8">

            @include('Backoffice.computation.partials.stepper', ['current' => 'summary'])

            <div class="card shadow-sm mt-3 rounded-4">
                <div class="card-header bg-white border-bottom rounded-top-4">
                    <h5 class="mb-0 fw-bold">Tax Summary</h5>
                </div>

                <div class="card-body p-4">

                    <!-- CLEAN SUMMARY CARDS (white + border) -->
                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white transition-card">
                                <small class="text-muted">Gross Income</small>
                                <h5 class="mb-0 fw-semibold">
                                    ₹ {{ number_format($summary->gross_income, 2) }}
                                </h5>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white transition-card">
                                <small class="text-muted">Total Deductions</small>
                                <h5 class="mb-0 fw-semibold">
                                    ₹ {{ number_format($summary->total_deductions, 2) }}
                                </h5>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white transition-card">
                                <small class="text-muted">Taxable Income</small>
                                <h5 class="mb-0 fw-semibold">
                                    ₹ {{ number_format($summary->taxable_income, 2) }}
                                </h5>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white transition-card">
                                <small class="text-muted">Tax Payable</small>
                                <h5 class="mb-0 fw-semibold text-primary">
                                    ₹ {{ number_format($summary->tax_amount, 2) }}
                                </h5>
                            </div>
                        </div>

                    </div>

                    <!-- CLEAN DETAIL BLOCK (replaces the old dark grey block) -->
                    <div class="mt-4 border rounded-3">

                        <div class="d-flex justify-content-between p-3 border-bottom">
                            <span class="text-muted">Gross Income</span>
                            <span class="fw-semibold">₹ {{ number_format($summary->gross_income, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between p-3 border-bottom">
                            <span class="text-muted">Total Deductions</span>
                            <span class="fw-semibold">₹ {{ number_format($summary->total_deductions, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between p-3 border-bottom">
                            <span class="text-muted">Taxable Income</span>
                            <span class="fw-semibold">₹ {{ number_format($summary->taxable_income, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between p-3">
                            <span class="fw-bold">Tax Payable</span>
                            <span class="fw-bold text-primary">
                                ₹ {{ number_format($summary->tax_amount, 2) }}
                            </span>
                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('backoffice.computations.deductions', $computation->id) }}"
                           class="btn btn-outline-secondary me-2">
                            ← Back
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            🖨️ Print
                        </button>
                    </div>

                    <form action="{{ route('backoffice.computations.submit', $computation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="btn btn-primary px-4"
                                onclick="return confirm('Finalize this computation?')">
                            Submit →
                        </button>
                    </form>
                </div>

            </div>

        </div>

        <!-- RIGHT SIDE (consistent preview panel) -->
        <div class="col-lg-4">
            @include('Backoffice.computation.partials.preview-panel')
        </div>

    </div>
</div>

<style>
    .transition-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
    }
    @media print {
        .btn, .card-footer, .stepper, .col-lg-4 {
            display: none !important;
        }
        .col-lg-8 {
            width: 100% !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Format number as Indian currency
    function format(num) {
        return Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    // Update preview panel elements
    function updatePreview(summary) {
        if (!summary) return;
        document.getElementById('gross-income').innerText = format(summary.gross_income || 0);
        document.getElementById('total-deduction').innerText = format(summary.total_deductions || 0);
        document.getElementById('taxable-income').innerText = format(summary.taxable_income || 0);
        document.getElementById('tax-amount').innerText = format(summary.tax_amount || 0);
    }

    // Call on page load with server-side data
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview({
            gross_income: {{ $summary->gross_income ?? 0 }},
            total_deductions: {{ $summary->total_deductions ?? 0 }},
            taxable_income: {{ $summary->taxable_income ?? 0 }},
            tax_amount: {{ $summary->tax_amount ?? 0 }}
        });
    });
</script>
@endsection