{{-- resources/views/backoffice/sales-reconciliation/register.blade.php --}}
@extends('layouts.Backoffice')

@section('title', 'Register Sales Reconciliation')

@section('styles')
    {{-- Same CSS as in index --}}
    <style>/* ... copy CSS ... */</style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @include('backoffice.sales-reconciliation.partials._header')
        @include('backoffice.sales-reconciliation.partials._tabs')
        <div class="mt-3">
            @include('backoffice.sales-reconciliation.partials._table', ['type' => 'register'])
        </div>
        @include('backoffice.sales-reconciliation.partials._footer')
    </div>
@endsection

@section('scripts')
    @include('backoffice.sales-reconciliation.scripts')
@endsection