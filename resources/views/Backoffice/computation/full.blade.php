@extends('layouts.Backoffice')

@section('styles')
<style>
    /* EXCEL-STYLE TAX COMPUTATION SHEET */
    body {
        background: white;
        font-size: 12px;
    }
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px 15px;
    }
    .sheet-title {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #aaa;
        padding-bottom: 8px;
    }
    .excel-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background: white;
    }
    .excel-table th,
    .excel-table td {
        border: 1px solid #c0c0c0;
        padding: 6px 8px;
        vertical-align: middle;
    }
    .excel-table th {
        background-color: #e9ecef;
        font-weight: 600;
    }
    .label-cell {
        width: 40%;
        background-color: #f8f9fa;
    }
    .input-cell {
        width: 60%;
    }
    input, select, textarea {
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 0;
        padding: 4px 6px;
        font-size: 12px;
        background: white;
        font-family: monospace;
    }
    input:read-only, input[readonly] {
        background-color: #e9ecef;
        font-weight: bold;
    }
    input.calculated-field {
        background-color: #d1e7dd !important;
        font-weight: bold;
    }
    .text-right {
        text-align: right;
    }
    .section-header th {
        background-color: #dee2e6;
        font-size: 13px;
    }
    /* No modern shadows, no cards */
    .no-card {
        background: none;
        box-shadow: none;
        padding: 0;
    }
    @media print {
        .no-print { display: none; }
        body { margin: 0; padding: 0; }
        input { border: none; background: transparent; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="sheet-title no-print">Income Tax Computation – Excel Style</div>

    {{-- Blade‑rendered ERP sheet --}}
    @foreach($sections as $section)
        <table class="excel-table">
            <thead class="section-header">
                <tr>
                    <th colspan="3">
                        {{ $section->title }}
                    </th>
                </tr>
                <tr>
                    <th width="50%">Particulars</th>
                    <th width="25%">Current Year</th>
                    <th width="25%">Previous Year</th>
                </tr>
            </thead>
            <tbody>
                @foreach($section->heads as $head)
                    @php
                        $currentYear = $values->get($head->id . '_current_year');
                        $previousYear = $values->get($head->id . '_previous_year');
                    @endphp
                    <tr class="{{ $head->row_type }}">
                        <td>{{ $head->title }}</td>
                        <td>
                            <input type="text"
                                   class="head-input"
                                   data-head-id="{{ $head->id }}"
                                   data-column="current_year"
                                   value="{{ $currentYear->value ?? '' }}"
                                   @if(!$head->is_editable) readonly @endif>
                        </td>
                        <td>
                            <input type="text"
                                   class="head-input"
                                   data-head-id="{{ $head->id }}"
                                   data-column="previous_year"
                                   value="{{ $previousYear->value ?? '' }}"
                                   @if(!$head->is_editable) readonly @endif>
                        </td>
                    </tr>

                    {{-- Child rows (if any) --}}
                    @foreach($head->children as $child)
                        @php
                            $childCurrent = $values->get($child->id . '_current_year');
                            $childPrevious = $values->get($child->id . '_previous_year');
                        @endphp
                        <tr class="child-row">
                            <td style="padding-left:40px;">{{ $child->title }}</td>
                            <td>
                                <input type="text"
                                       class="head-input"
                                       data-head-id="{{ $child->id }}"
                                       data-column="current_year"
                                       value="{{ $childCurrent->value ?? '' }}">
                            </td>
                            <td>
                                <input type="text"
                                       class="head-input"
                                       data-head-id="{{ $child->id }}"
                                       data-column="previous_year"
                                       value="{{ $childPrevious->value ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    let debounceTimer = null;

    $('.head-input').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            saveValues();
        }, 500);
    });

    function saveValues()
    {
        let values = {};

        $('.head-input').each(function () {
            let headId = $(this).data('head-id');
            let column = $(this).data('column');
            let value = $(this).val();

            if (!values[headId]) {
                values[headId] = {};
            }
            values[headId][column] = value;
        });

        $.ajax({
            url: "{{ route('backoffice.computations.save-values', $computation->id) }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                values: values
            },
            success: function (response) {
                console.log(response);
            }
        });
    }
});
</script>
@endsection