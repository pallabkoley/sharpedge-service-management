@extends('layouts.admin')

@section('title', 'Client Master - ' . $client->name)

@section('content')
<div class="container-fluid">

    @include('admin.client-master.partials.header', ['client' => $client])

    <!-- Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="clientTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="edit-details-tab" data-bs-toggle="tab" data-bs-target="#edit-details" type="button">
                <i class="bi bi-pencil-square me-1"></i> Edit Details
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="cases-tab" data-bs-toggle="tab" data-bs-target="#cases" type="button">
                <i class="bi bi-gavel me-1"></i> GST &amp; IT Cases
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="compliances-tab" data-bs-toggle="tab" data-bs-target="#compliances" type="button">
                <i class="bi bi-check2-all me-1"></i> Compliances
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="turnover-tab" data-bs-toggle="tab" data-bs-target="#turnover" type="button">
                <i class="bi bi-graph-up me-1"></i> Turnover
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="portal-tab" data-bs-toggle="tab" data-bs-target="#portal" type="button">
                <i class="bi bi-key me-1"></i> Portal IDs
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                <i class="bi bi-grid me-1"></i> Client Summary
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="clientTabsContent">
        <div class="tab-pane fade show active" id="edit-details" role="tabpanel">
            @include('admin.client-master.partials.edit-details', ['client' => $client])
        </div>
        <div class="tab-pane fade" id="cases" role="tabpanel">
            @include('admin.client-master.partials.cases', ['client' => $client])
        </div>
        <div class="tab-pane fade" id="compliances" role="tabpanel">
            @include('admin.client-master.partials.compliances', ['client' => $client])
        </div>
        <div class="tab-pane fade" id="turnover" role="tabpanel">
            @include('admin.client-master.partials.turnover', ['client' => $client])
        </div>
        <div class="tab-pane fade" id="portal" role="tabpanel">
@include('admin.client-master.partials.portal-ids', ['client' => $client])
        </div>
        <div class="tab-pane fade" id="summary" role="tabpanel">
            @include('admin.client-master.partials.summary', ['client' => $client])
        </div>
    </div>

</div>

<!-- Modals -->
@include('admin.client-master.modals.delete-confirmation-modal')
@include('admin.client-master.modals.bank-account-modal', ['client' => $client])



@endsection

@push('styles')
<style>
.nav-tabs-custom .nav-link {
    color: #64748b;
    font-weight: 500;
    border: none;
    padding: 10px 20px;
    border-radius: 8px 8px 0 0;
    font-size: 14px;
}
.nav-tabs-custom .nav-link:hover {
    background-color: #f1f5f9;
    color: #1e293b;
}
.nav-tabs-custom .nav-link.active {
    background-color: #fff;
    color: #1d4ed8;
    border-bottom: 3px solid #1d4ed8;
}
</style>
@endpush