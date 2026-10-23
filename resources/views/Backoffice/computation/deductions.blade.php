@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-8">

            @include('Backoffice.computation.partials.stepper', ['current' => 'deductions'])

            <div class="card shadow mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Deduction Details</h5>
                </div>

                <div class="card-body" id="dynamic-form-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading form...</p>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('backoffice.computations.income', $computation->id) }}" class="btn btn-secondary">
                        ← Back
                    </a>
                    <button type="button" id="next-step-btn" class="btn btn-primary">
                        Next: Summary →
                    </button>
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
    let currentStep = 'deductions';

    // MAPPING: frontend code (data-code) → actual database head code
    const CODE_MAP = {
        '80c': 'sec_80c',
        '80d': 'sec_80d',
        '80tta': 'sec_80tta',
        '80e': 'sec_80e'
    };

    function getLimitText(code) {
        const limits = {
            '80c': '(Max ₹1,50,000)',
            '80d': '(Max ₹25,000 for self & family, ₹50,000 for senior citizens)',
            '80tta': '(Max ₹10,000)',
            '80ttb': '(Max ₹50,000 for senior citizens)',
            '80e': '(No limit – interest on education loan)',
            '80ee': '(Max ₹50,000 – home loan interest for first-time buyers)'
        };
        return limits[code] || '';
    }

    function calculateLocalTotal() {
        let total = 0;
        $('.head-input').each(function() {
            let val = parseFloat($(this).val()) || 0;
            total += val;
        });
        $('#total-deduction-display').text(total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        return total;
    }

    function loadForm() {
        $.get(`/backoffice/computations/${computationId}/structure/${currentStep}`, function(data) {
            renderForm(data.groups, data.values);
            updatePreview(data.summary);
        });
    }

    function renderForm(groups, savedValues) {
        let html = '';
        let group = groups.find(g => g.code === 'deductions') || groups[0];

        if (!group) {
            html = '<p>No deductions found</p>';
        } else {
            html += `
                <div class="card shadow p-4 rounded-4">
                    <h5 class="mb-3">📉 ${escapeHtml(group.name)} (Chapter VI-A)</h5>
            `;

            for (let head of group.heads) {
                let frontCode = head.code;
                let dbCode = CODE_MAP[frontCode] || frontCode;
                let value = savedValues?.[dbCode] ?? 0;
                let limitHint = getLimitText(frontCode);
                html += `
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            ${escapeHtml(head.name)}
                            ${limitHint ? `<small class="text-muted ms-2">${limitHint}</small>` : ''}
                        </label>
                        <input type="number"
                            class="form-control head-input"
                            data-code="${escapeHtml(frontCode)}"
                            value="${value}"
                            min="0"
                            step="1">
                    </div>
                `;
            }

            html += `
                    <hr>
                    <h6 class="fw-bold mt-3">
                        Total Deduction: ₹ 
                        <span id="total-deduction-display">0</span>
                    </h6>
                </div>
            `;
        }

        $('#dynamic-form-container').html(html);
        attachEvents();
        calculateLocalTotal();
    }

    function attachEvents() {
        $('.head-input').on('input', function() {
            if ($(this).val() < 0) $(this).val(0);
            calculateLocalTotal();

            let values = {};
            $('.head-input').each(function() {
                let frontCode = $(this).data('code');
                let dbCode = CODE_MAP[frontCode] || frontCode;
                let value = parseFloat($(this).val()) || 0;
                values[dbCode] = value;
            });

            $.post({
                url: `/backoffice/computations/${computationId}/step/${currentStep}`,
                data: {
                    _token: '{{ csrf_token() }}',
                    values: values
                },
                success: function(response) {
                    if (response.success && response.summary) {
                        updatePreview(response.summary);
                        // ✅ FIX: Recalculate local total after backend updates
                        calculateLocalTotal();
                    }
                }
            });
        });
    }

    function updatePreview(summary) {
        if (!summary) return;
        $('#gross-income').text((summary.gross_income || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#total-deduction').text((summary.total_deductions || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#taxable-income').text((summary.taxable_income || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#tax-amount').text((summary.tax_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        // ❌ REMOVED: $('#total-deduction-display').text(...) – no longer overwritten
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    $('#next-step-btn').click(() => {
        window.location.href = `/backoffice/computations/${computationId}/summary`;
    });

    $(document).ready(function () {
        loadForm();
    });
</script>
@endsection