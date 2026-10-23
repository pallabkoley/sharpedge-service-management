@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            @include('Backoffice.computation.partials.stepper', ['current' => 'income'])
            <div class="card shadow mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Income Details</h5>
                </div>
                <div class="card-body" id="dynamic-form-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading form...</p>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('backoffice.computations.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="button" id="next-step-btn" class="btn btn-primary">Next: Trading →</button>
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
    let currentStep = 'income';
    let debounceTimer;
    let dynamicIndex = 0;

    // Load form
    function loadForm() {
        $.get(`/backoffice/computations/${computationId}/structure/${currentStep}`, function(data) {
            renderForm(data.groups, data.values);
            updatePreview(data.summary);
        });
    }

    // Render form
    function renderForm(groups, savedValues) {
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
                                class="form-control head-input" 
                                data-code="${head.code}" 
                                data-group="${group.id}"
                                value="${value}"
                                ${isDisabled}
                            >
                        </div>
                    </div>
                `;
            }

            // + Add Field button
            html += `
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary add-field-btn" data-group="${group.id}">
                        + Add Field
                    </button>
                </div>
            `;

            html += `</div></div>`;
        }

        $('#dynamic-form-container').html(html);
        updateGroupTotals();
    }

    // GLOBAL EVENT HANDLER (works for both static and dynamic .head-input)
    $(document).on('input', '.head-input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            saveAndCalculate();
        }, 400);
    });

    // saveAndCalculate() – ensures correct order and updates
    function saveAndCalculate() {
        let values = {};

        $('.head-input').each(function() {
            let code = $(this).data('code');
            let value = $(this).val() || 0;
            values[code] = value;
        });

        // FIRST save to backend
        $.ajax({
            url: `/backoffice/computations/${computationId}/step/${currentStep}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                values: values
            },
            success: function () {
                // THEN calculate fresh values
                $.ajax({
                    url: `/backoffice/computations/${computationId}/calculate`,
                    method: 'GET',
                    success: function (data) {
                        if (!data.summary) return;

                        // Update preview panel – 🔥 FIXED KEY: use total_deductions, not total_deduction
                        $('#gross-income').text(format(data.summary.gross_income));
                        $('#total-deduction').text(format(data.summary.total_deductions));
                        $('#taxable-income').text(format(data.summary.taxable_income));
                        $('#tax-amount').text(format(data.summary.tax_amount));

                        // Update all calculated fields
                        for (let [code, val] of Object.entries(data.values)) {
                            let input = $(`input[data-code="${code}"]`);
                            if (input.prop('disabled')) {
                                input.val(val);
                            }
                        }

                        // 🔥 CRITICAL FIX: Recalculate group totals after values update
                        updateGroupTotals();
                    }
                });
            }
        });
    }

    // Safe preview update
    function updatePreview(summary) {
        if (!summary) return;

        $('#gross-income').text(format(summary.gross_income || 0));
        $('#total-deduction').text(format(summary.total_deductions || 0));
        $('#taxable-income').text(format(summary.taxable_income || 0));
        $('#tax-amount').text(format(summary.tax_amount || 0));
    }

    // 🔥 IMPROVED: Skip disabled (calculated) fields – prevents double counting
    function updateGroupTotals() {
        let groups = {};

        $('.head-input').each(function() {
            // Skip calculated fields (disabled)
            if ($(this).prop('disabled')) return;

            let group = $(this).data('group');
            let val = parseFloat($(this).val()) || 0;

            if (!groups[group]) groups[group] = 0;
            groups[group] += val;
        });

        for (let groupId in groups) {
            $(`#group-total-${groupId}`).text(format(groups[groupId]));
        }
    }

    // Format number as Indian currency
    function format(num) {
        return parseFloat(num).toLocaleString('en-IN', {
            minimumFractionDigits: 2
        });
    }

    // Escape HTML
    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Add dynamic row
    $(document).on('click', '.add-field-btn', function () {
        let groupId = $(this).data('group');
        dynamicIndex++;

        let newRow = `
            <div class="mb-3 row dynamic-row">
                <div class="col-sm-4">
                    <input type="text" class="form-control" placeholder="Description">
                </div>
                <div class="col-sm-6">
                    <input type="number" 
                        class="form-control head-input" 
                        data-code="dynamic_${dynamicIndex}" 
                        data-group="${groupId}" 
                        value="0">
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger btn-sm remove-row">×</button>
                </div>
            </div>
        `;

        $(this).closest('.card-body').append(newRow);
        updateGroupTotals();
    });

    // Remove dynamic row
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.dynamic-row').remove();
        updateGroupTotals();
    });

    // Next step redirects to TRADING
    $('#next-step-btn').click(() => {
        window.location.href = `/backoffice/computations/${computationId}/trading`;
    });

    // Initialise
    $(document).ready(function () {
        loadForm();
    });
</script>
@endsection