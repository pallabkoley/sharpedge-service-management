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
    let currentStep = 'income';

    function loadForm() {
        $.get(`/backoffice/computations/${computationId}/structure/${currentStep}`, function(data) {
            renderForm(data.groups, data.values);
            updatePreview(data.summary);
        });
    }

    function renderForm(groups, savedValues) {
        let html = '';
        for (let group of groups) {
            html += `<div class="card mb-3">
                        <div class="card-header bg-light fw-bold">${escapeHtml(group.name)}</div>
                        <div class="card-body">`;
            for (let head of group.heads) {
                let value = savedValues[head.code] || 0;
                html += `
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">${escapeHtml(head.name)}</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control head-input" data-code="${head.code}" value="${value}">
                            ${head.formula ? `<small class="text-muted">Formula: ${escapeHtml(head.formula)}</small>` : ''}
                        </div>
                    </div>
                `;
            }
            html += `</div></div>`;
        }
        $('#dynamic-form-container').html(html);
        attachEvents();
    }

    function attachEvents() {
        $('.head-input').on('change', function() {
            let code = $(this).data('code');
            let value = $(this).val();
            let values = {};
            values[code] = value;
            $.post(`/backoffice/computations/${computationId}/step/${currentStep}`, {
                _token: '{{ csrf_token() }}',
                values: values
            }).done(() => {
                $.get(`/backoffice/computations/${computationId}/calculate`, function(data) {
                    updatePreview(data.summary);
                    for (let [code, val] of Object.entries(data.values)) {
                        $(`input[data-code="${code}"]`).val(val);
                    }
                });
            });
        });
    }

    function updatePreview(summary) {
        $('#gross-income').text(parseFloat(summary.gross_income).toLocaleString('en-IN', {minimumFractionDigits:2}));
        $('#total-deduction').text(parseFloat(summary.total_deduction).toLocaleString('en-IN', {minimumFractionDigits:2}));
        $('#taxable-income').text(parseFloat(summary.taxable_income).toLocaleString('en-IN', {minimumFractionDigits:2}));
        $('#tax-amount').text(parseFloat(summary.tax_amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
    }

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    $('#next-step-btn').click(() => {
        window.location.href = `/backoffice/computations/${computationId}/deductions`;
    });

    loadForm();
</script>
@endsection