@extends('layouts.Backoffice')

@section('styles')
    @include('Backoffice.debit-credit-note-register.partials._header_styles')
@endsection

@section('content')
    {{-- Hidden fields for JavaScript --}}
    <input type="hidden" id="computationId" value="{{ $computation->id ?? '' }}" />
    <input type="hidden" id="registerId" value="" /> {{-- will be set by JavaScript --}}

    {{-- Sidebar --}}
    @include('Backoffice.debit-credit-note-register.partials._sidebar')

    <div class="paper fade-up delay-1">
        <div class="wm" id="wmText">DRAFT</div>
        <div class="paper-inner">

            {{-- ✅ Toolbar FIRST (above header) – identical to Purchase Reconciliation --}}
            @include('Backoffice.debit-credit-note-register.partials._topbar')

            {{-- ✅ Header SECOND (below toolbar) --}}
            @include('Backoffice.debit-credit-note-register.partials._header')

            {{-- Tabs --}}
            @include('Backoffice.debit-credit-note-register.partials._tabs')

            {{-- GST Toolbar --}}
            @include('Backoffice.debit-credit-note-register.partials._toolbar')

            {{-- Tab Content --}}
            <div class="tab-content" id="registerTabContent">
                {{-- Debit Tab --}}
                <div class="tab-pane fade show active" id="debitTab" role="tabpanel" aria-labelledby="debit-tab">
                    @include('Backoffice.debit-credit-note-register.debit')
                </div>

                {{-- Credit Tab --}}
                <div class="tab-pane fade" id="creditTab" role="tabpanel" aria-labelledby="credit-tab">
                    @include('Backoffice.debit-credit-note-register.credit')
                </div>
            </div>

            {{-- Signature Footer --}}
            @include('Backoffice.debit-credit-note-register.partials._signature')

        </div><!-- /paper-inner -->
        <div class="pgnum">Page 1 of 1</div>
    </div>

    {{-- Breakup Modal --}}
    @include('Backoffice.debit-credit-note-register.partials._breakup-modal')

    {{-- Toast Container --}}
    <div id="toastContainer" class="toast-container"></div>
@endsection

@section('scripts')
    @include('Backoffice.debit-credit-note-register.partials._scripts')
@endsection