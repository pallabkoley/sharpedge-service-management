@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            @include('Backoffice.computation.partials.stepper', ['current' => 'trading'])
            <div class="card shadow mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Trading Account</h5>
                </div>
                <div class="card-body" id="trading-form-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading trading form...</p>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('backoffice.computations.income', $computation) }}" class="btn btn-secondary">← Back to Income</a>
                    <button type="button" id="next-step-btn" class="btn btn-primary">Next: Deductions →</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @include('Backoffice.computation.partials.preview-panel')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let computationId = {{ $computation->id }};
    let currentStep = 'trading';
    let debounceTimer;

    // Load form structure
    function loadForm() {
        $.get(`/backoffice/computations/${computationId}/structure/${currentStep}`, function(data) {
            renderTradingForm(data.groups, data.values);
            updatePreview(data.summary);
        }).fail(function() {
            $('#trading-form-container').html('<div class="alert alert-danger">Failed to load trading form.</div>');
        });
    }

    // Render trading form (static, no "add field" buttons)
    function renderTradingForm(groups, savedValues) {
        let html = '';

        for (let group of groups) {
            html += `
                <div class="card mb-3">
                    <div class="card-header bg-light fw-bold d-flex justify-content-between">
                        ${escapeHtml(group.name)}
                        <span>₹ <span id="group-total-${group.id}">0</span></span>
                    </div>
                    <div class="card-body">
            `;

            for (let head of group.heads) {
                let value = savedValues[head.code] || 0;
                let isDisabled = head.head_type === 'calculated' ? 'disabled' : '';

                html += `
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">${escapeHtml(head.name)}</label>
                        <div class="col-sm-8">
                            <input 
                                type="number" 
                                class="form-control trading-input" 
                                data-code="${head.code}" 
                                data-group="${group.id}"
                                data-calculated="${head.head_type === 'calculated' ? '1' : '0'}"
                                value="${value}"
                                ${isDisabled}
                            >
                        </div>
                    </div>
                `;
            }

            html += `</div></div>`;
        }

        $('#trading-form-container').html(html);
        updateGroupTotals(); // initial call
    }

    // Save and calculate on input
    $(document).on('input', '.trading-input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            saveAndCalculate();
        }, 400);
    });

    function saveAndCalculate() {
        let values = {};

        $('.trading-input').each(function() {
            let code = $(this).data('code');
            let value = $(this).val() || 0;
            values[code] = value;
        });

        $.ajax({
            url: `/backoffice/computations/${computationId}/step/${currentStep}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                values: values
            },
            success: function () {
                $.ajax({
                    url: `/backoffice/computations/${computationId}/calculate`,
                    method: 'GET',
                    success: function (data) {
                        if (!data.summary) return;

                        updatePreview(data.summary);

                        // Update calculated fields from backend
                        for (let [code, val] of Object.entries(data.values)) {
                            let input = $(`input[data-code="${code}"]`);
                            if (input.length && input.val() != val) {
                                input.val(val);
                            }
                        }

                        updateGroupTotals();
                    }
                });
            }
        });
    }

    function updatePreview(summary) {
        if (!summary) return;
        $('#gross-income').text(format(summary.gross_income || 0));
        $('#total-deduction').text(format(summary.total_deduction || 0));
        $('#taxable-income').text(format(summary.taxable_income || 0));
        $('#tax-amount').text(format(summary.tax_amount || 0));
    }

    // ✅ FIXED: Group total now shows GROSS PROFIT (Sales - Purchase - Direct Expenses)
    function updateGroupTotals() {
        // Get values from the specific fields (adjust codes as per your DB)
        let sales = parseFloat($('input[data-code="sales"]').val()) || 0;
        let purchase = parseFloat($('input[data-code="purchase"]').val()) || 0;
        let directExpense = parseFloat($('input[data-code="direct_expense"]').val()) || 0;

        // Gross Profit = Sales - Purchase - Direct Expenses
        let grossProfit = sales - purchase - directExpense;

        // Update the group total span (assuming only one group in trading page)
        // Find all spans with id starting with "group-total-" and update them
        $('[id^="group-total-"]').each(function() {
            $(this).text(format(grossProfit));
        });
    }

    function format(num) {
        return parseFloat(num).toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Next button: go to deductions
  $('#next-step-btn').click(() => {
    window.location.href = `/backoffice/computations/${computationId}/assets`;
});

    $(document).ready(function () {
        loadForm();
    });
</script>
@endsection