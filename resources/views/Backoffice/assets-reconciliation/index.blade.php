@extends('layouts.Backoffice')

@section('styles')
    @include('Backoffice.assets-reconciliation.partials._styles')
@endsection

@section('content')

    {{-- Hidden fields --}}
    <input type="hidden" id="computationId" value="{{ $computation->id ?? '' }}" />

    <div class="paper fade-up delay-1">
        <div class="wm" id="wmText">DRAFT</div>

        <div class="paper-inner">
            @include('Backoffice.assets-reconciliation.partials._toolbar')

            @include('Backoffice.assets-reconciliation.partials._header')

            @include('Backoffice.assets-reconciliation.partials._cards')

            {{-- 🆕 TOOLBAR – added here --}}
            

            @include('Backoffice.assets-reconciliation.partials._assets_table')

            @include('Backoffice.assets-reconciliation.partials._investments_table')

            @include('Backoffice.assets-reconciliation.partials._security_table')

            @include('Backoffice.assets-reconciliation.partials._footer')

        </div>

    </div>

    @include('Backoffice.assets-reconciliation.partials._breakup_modal')

    @include('Backoffice.assets-reconciliation.partials._float_panel')

    <div id="toastContainer" class="toast-container"></div>

@endsection

@section('scripts')
    @include('Backoffice.assets-reconciliation.partials._scripts')
@endsection