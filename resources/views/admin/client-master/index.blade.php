@extends('layouts.admin')

@section('title', 'Client Master')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2 text-primary"></i>Client Master
            </h4>
            <p class="text-muted small mb-0">
                Manage all clients from a single dashboard
            </p>
        </div>

        <a href="{{ route('admin.client-master.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Client
        </a>
    </div>


    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.client-master.index') }}"
                  class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">
                        Search
                    </label>

                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Name, code, phone, email, GST, PAN..."
                           value="{{ request('search') }}">
                </div>


                <div class="col-md-3">
                    <label class="form-label fw-semibold small">
                        Client Type
                    </label>

                    <select name="client_type"
                            class="form-select form-select-sm">

                        <option value="">All Types</option>

                        <option value="individual"
                            {{ request('client_type') == 'individual' ? 'selected' : '' }}>
                            Individual
                        </option>

                        <option value="company"
                            {{ request('client_type') == 'company' ? 'selected' : '' }}>
                            Company
                        </option>

                        <option value="firm"
                            {{ request('client_type') == 'firm' ? 'selected' : '' }}>
                            Firm
                        </option>

                        <option value="huf"
                            {{ request('client_type') == 'huf' ? 'selected' : '' }}>
                            HUF
                        </option>

                        <option value="trust"
                            {{ request('client_type') == 'trust' ? 'selected' : '' }}>
                            Trust
                        </option>

                    </select>
                </div>


                <div class="col-md-3">
                    <label class="form-label fw-semibold small">
                        Status
                    </label>

                    <select name="status"
                            class="form-select form-select-sm">

                        <option value="">All Status</option>

                        <option value="active"
                            {{ request('status') == 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                        <option value="suspended"
                            {{ request('status') == 'suspended' ? 'selected' : '' }}>
                            Suspended
                        </option>

                    </select>
                </div>


                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit"
                            class="btn btn-primary btn-sm w-100">

                        <i class="bi bi-search me-1"></i>
                        Filter

                    </button>
                </div>

            </form>

        </div>
    </div>


    <!-- Client Table -->
    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover datatable"
                       id="clientsTable">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Client Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>GST</th>
                            <th>PAN</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>


                    <tbody>

                        @forelse($clients as $index => $client)

                            <tr>

                                {{-- Serial Number --}}
                                <td>
                                    {{ $index + 1 }}
                                </td>


                                {{-- Client Code --}}
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $client->client_code ?? 'N/A' }}
                                    </span>
                                </td>


                                {{-- Client Name --}}
                                <td>

                                    <div class="d-flex align-items-center">

                                        @if($client->profile_photo)

                                            <img src="{{ asset('storage/' . $client->profile_photo) }}"
                                                 class="rounded-circle me-2"
                                                 style="width:32px;height:32px;object-fit:cover;">

                                        @else

                                            <div class="bg-primary text-white rounded-circle
                                                        d-flex align-items-center
                                                        justify-content-center me-2"
                                                 style="width:32px;height:32px;font-weight:600;">

                                                {{ strtoupper(substr($client->name ?? 'N', 0, 1)) }}

                                            </div>

                                        @endif


                                        <span class="fw-semibold">
                                            {{ $client->name ?? 'N/A' }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Company --}}
                                <td>
                                    {{ $client->company_name ?? 'N/A' }}
                                </td>


                                {{-- Phone --}}
                                <td>
                                    {{ $client->phone ?? 'N/A' }}
                                </td>


                                {{-- Email --}}
                                <td>
                                    {{ $client->email ?? 'N/A' }}
                                </td>


                                {{-- GST --}}
                                <td>
                                    {{ $client->gst_number ?? 'N/A' }}
                                </td>


                                {{-- PAN --}}
                                <td>
                                    {{ $client->pan ?? 'N/A' }}
                                </td>


                                {{-- Client Type --}}
                                <td>

                                    <span class="badge bg-info">
                                        {{ ucfirst($client->client_type ?? 'N/A') }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @php
                                        $colors = [
                                            'active' => 'success',
                                            'inactive' => 'secondary',
                                            'suspended' => 'danger'
                                        ];
                                    @endphp

                                    <span class="badge bg-{{ $colors[$client->status] ?? 'secondary' }}">

                                        {{ ucfirst($client->status ?? 'N/A') }}

                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td class="text-center">

                                    <div class="btn-group btn-group-sm">

                                        {{-- View --}}
                                        <a href="{{ route('admin.client-master.show', $client) }}"
                                           class="btn btn-outline-primary"
                                           title="View">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route('admin.client-master.edit', $client) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                title="Delete"
                                                onclick="confirmDelete(
                                                    '{{ route('admin.client-master.destroy', $client) }}',
                                                    '{{ addslashes($client->name ?? 'Client') }}'
                                                )">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="11"
                                    class="text-center py-4 text-muted">

                                    No clients found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Total Client Count --}}
            <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted small">

                    Showing
                    <strong>{{ $clients->count() }}</strong>
                    client{{ $clients->count() != 1 ? 's' : '' }}

                </div>

            </div>

        </div>

    </div>

</div>


@include('admin.client-master.modals.delete-confirmation-modal')

@endsection


@push('scripts')

<script>

function confirmDelete(url, name) {

    document.getElementById('deleteItemName').textContent = name;

    document.getElementById('deleteForm').action = url;

    new bootstrap.Modal(
        document.getElementById('deleteModal')
    ).show();

}

</script>

@endpush