@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-8">

            {{-- 🔥 Stepper যোগ করা হলো --}}
            @include('Backoffice.computation.partials.stepper', ['current' => 'assets'])

            <div class="card shadow mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assets Details</h5>
                </div>

                <div class="card-body">
                    <form id="assetsForm">
                        @csrf

                        <div class="mb-3">
                            <label>Cash in Hand</label>
                            <input type="number" name="values[cash_in_hand]" class="form-control"
                                value="{{ $values['cash_in_hand'] ?? 0 }}">
                        </div>

                        <div class="mb-3">
                            <label>Bank Balance</label>
                            <input type="number" name="values[bank_balance]" class="form-control"
                                value="{{ $values['bank_balance'] ?? 0 }}">
                        </div>

                        <div class="mb-3">
                            <label>Debtors</label>
                            <input type="number" name="values[debtors]" class="form-control"
                                value="{{ $values['debtors'] ?? 0 }}">
                        </div>

                        <div class="mb-3">
                            <label>Closing Stock</label>
                            <input type="number" name="values[closing_stock]" class="form-control"
                                value="{{ $values['closing_stock'] ?? 0 }}">
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary px-4" onclick="saveAssets()">
                                Save Assets
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('backoffice.computations.trading', $computation) }}" class="btn btn-secondary">
                        ← Back to Trading
                    </a>
                    <a href="{{ route('backoffice.computations.summary', $computation) }}" class="btn btn-success">
                        Go to Summary →
                    </a>
                </div>
            </div>

        </div>

        {{-- 🔥 Preview Panel যোগ করা হলো --}}
        <div class="col-lg-4">
            @include('Backoffice.computation.partials.preview-panel')
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function saveAssets() {
    let values = {};
    document.querySelectorAll('#assetsForm input[name^="values["]').forEach(input => {
        let key = input.name.match(/values\[(.*?)\]/)[1];
        values[key] = parseFloat(input.value) || 0;
    });

    fetch("{{ route('backoffice.computations.saveStep', [$computation->id, 'assets']) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            _token: "{{ csrf_token() }}",
            values: values
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assets Saved Successfully');
            // Reload preview panel after save
            fetch("{{ route('backoffice.computations.calculate', $computation->id) }}")
                .then(res => res.json())
                .then(d => {
                    if (d.summary) {
                        document.getElementById('gross-income').innerText = format(d.summary.gross_income);
                        document.getElementById('total-deduction').innerText = format(d.summary.total_deduction);
                        document.getElementById('taxable-income').innerText = format(d.summary.taxable_income);
                        document.getElementById('tax-amount').innerText = format(d.summary.tax_amount);
                    }
                });
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(() => alert('Save failed. Check network.'));
}

function format(num) {
    return Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2 });
}
</script>
@endsection