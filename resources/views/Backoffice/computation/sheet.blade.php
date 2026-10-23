{{-- resources/views/Backoffice/computation/sheet.blade.php --}}
@extends('layouts.Backoffice')

@section('styles')
    @include('Backoffice.computation.partials.styles')
@endsection

@section('content')
    @include('Backoffice.computation.partials.toolbar')

    <div id="sheet-wrapper">
        <div id="computation-sheet">

            {{-- All Sections --}}
            @include('Backoffice.computation.partials.header')
            @include('Backoffice.computation.partials.master-information')
            @include('Backoffice.computation.partials.bank-details')
            @include('Backoffice.computation.partials.barcode')
            @include('Backoffice.computation.partials.exempt-income')
            
            {{-- SECTION 1: SALARY --}}
            @include('Backoffice.computation.summaries.salary-summary')
            
            {{-- SECTION 2: HOUSE PROPERTY --}}
            @include('Backoffice.computation.summaries.house-property-summary')
            
            {{-- SECTION 3: BUSINESS / PROFESSION --}}
            @include('Backoffice.computation.summaries.business-summary')
            
            {{-- SECTION 4: CAPITAL GAINS --}}
            @include('Backoffice.computation.summaries.capital-gain-summary')
            
            {{-- SECTION 5: VDA --}}
            @include('Backoffice.computation.summaries.vda-summary')
            
            {{-- SECTION 6: OTHER SOURCES --}}
            @include('Backoffice.computation.summaries.other-source-summary')
            
            {{-- SECTION 7: DEDUCTIONS --}}
            @include('Backoffice.computation.summaries.deduction-summary')
            
            {{-- SECTION 8: GROSS TOTAL INCOME --}}
            @include('Backoffice.computation.partials.gross-total')
            
            {{-- SECTION 9: TAXABLE INCOME --}}
            @include('Backoffice.computation.partials.taxable-income')
            
            {{-- SECTION 10: TAX CALCULATION --}}
            @include('Backoffice.computation.partials.tax-computation')
            
            {{-- SECTION 11: TAX PAID --}}
            @include('Backoffice.computation.partials.tax-paid-summary')
            
            {{-- SECTION 12: VERIFICATION --}}
            @include('Backoffice.computation.partials.verification')
            
            {{-- SECTION 13: FOOTER --}}
            @include('Backoffice.computation.partials.footer')

        </div>
    </div>

    {{-- Modals --}}
    @include('Backoffice.computation.modals.salary-modal')
    @include('Backoffice.computation.modals.house-property-modal')
    @include('Backoffice.computation.modals.business-modal')
    @include('Backoffice.computation.modals.capital-gain-modal')
    @include('Backoffice.computation.modals.vda-modal')
    @include('Backoffice.computation.modals.other-source-modal')
    @include('Backoffice.computation.modals.deduction-modal')
    @include('Backoffice.computation.modals.tax-paid-modal')

@endsection

@section('scripts')
    @include('Backoffice.computation.scripts.common-script')
    @include('Backoffice.computation.scripts.salary-script')
    @include('Backoffice.computation.scripts.house-property-script')
    @include('Backoffice.computation.scripts.business-script')
    @include('Backoffice.computation.scripts.capital-gain-script')
    @include('Backoffice.computation.scripts.vda-script')
    @include('Backoffice.computation.scripts.other-source-script')
    @include('Backoffice.computation.scripts.deduction-script')
    {{-- 🔥 CRITICAL: tax-computation-script MUST come BEFORE tax-paid-script --}}
    @include('Backoffice.computation.scripts.tax-computation-script')
    @include('Backoffice.computation.scripts.tax-paid-script')

    {{-- Direct binding fallback --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dyn-btn.add').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var group = this.dataset.group;
                    var tbody = this.dataset.tbody || group + '_rows';
                    var template = this.dataset.template || 'tpl_' + group;
                    if (group && !group.startsWith('modal_')) {
                        if (typeof addDynRow === 'function') {
                            addDynRow(group, tbody, template);
                        }
                    }
                });
            });
            document.querySelectorAll('tbody[id$="_rows"]').forEach(function(tbody) {
                tbody.addEventListener('click', function(e) {
                    var delBtn = e.target.closest('.dyn-btn.del');
                    if (delBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        var group = delBtn.dataset.group;
                        if (group && !group.startsWith('modal_')) {
                            if (typeof deleteDynRow === 'function') {
                                deleteDynRow(delBtn, group, group + '_rows');
                            }
                        }
                    }
                });
            });
            console.log('✅ Direct button binding complete.');
        });
    </script>
@endsection