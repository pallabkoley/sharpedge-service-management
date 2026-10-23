@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Client Details</h1>
                <p class="text-muted mb-0">View complete client information and activity</p>
            </div>
            <div>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Clients
                </a>
            </div>
        </div>
    </div>

    <!-- Client Profile Card -->
    <div class="section-card mb-4">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <!-- Profile Photo -->
                    <div class="me-4">
                        @if($client->profile_photo)
                            <img src="{{ asset('storage/'.$client->profile_photo) }}" 
                                 alt="{{ $client->name }}" 
                                 class="rounded-circle border"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; font-size: 32px; color: white;">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $client->name }}</h2>
                        <div class="text-muted mb-2">
                            <i class="bi bi-envelope me-1"></i> {{ $client->email }} &nbsp;|&nbsp;
                            <i class="bi bi-telephone me-1"></i> {{ $client->phone }}
                        </div>
                        <div class="text-muted">
                            @if($client->company_name)
                                <i class="bi bi-building me-1"></i> {{ $client->company_name }}
                                @if($client->city) &nbsp;|&nbsp; @endif
                            @endif
                            @if($client->city)
                                <i class="bi bi-geo-alt me-1"></i> {{ $client->city }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge {{ $client->status == 'active' ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                    {{ ucfirst($client->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @php
        $totalOrders = $client->orders->sum('final_amount');
        $totalPaid = $client->orders->sum(function($order) {
            return $order->payments->where('status', 'approved')->sum('amount');
        });
        $totalDue = $totalOrders - $totalPaid;
    @endphp
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">₹ {{ number_format($totalOrders, 2) }}</div>
                        <div class="card-label">Total Orders</div>
                    </div>
                    <div class="card-icon" style="background-color: #3b82f6;">
                        <i class="bi bi-cart text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value text-success">₹ {{ number_format($totalPaid, 2) }}</div>
                        <div class="card-label">Total Paid</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value text-danger">₹ {{ number_format($totalDue, 2) }}</div>
                        <div class="card-label">Total Due</div>
                    </div>
                    <div class="card-icon" style="background-color: #ef4444;">
                        <i class="bi bi-exclamation-triangle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('admin.orders.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create Order
        </a>
        <button type="button" class="btn btn-outline-secondary" id="downloadLedgerBtn">
            <i class="bi bi-download me-2"></i>Download Ledger
        </button>
    </div>

    <!-- Tabs -->
    <div class="section-card">
        <ul class="nav nav-tabs sticky-top bg-white pt-2" id="clientTabs" role="tablist" style="top: 0; z-index: 100; background: white;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Orders</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Payments</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">Invoices</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Ledger</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents (Order)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="client-documents-tab" data-bs-toggle="tab" data-bs-target="#client-documents" type="button" role="tab">Client Documents</button>
            </li>
        </ul>
        <div class="tab-content p-3">
            <!-- Orders Tab -->
            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Service</th>
                                <th>Amount (₹)</th>
                                <th>Paid (₹)</th>
                                <th>Due (₹)</th>
                                <th>Status</th>
                                <th>Task</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->orders as $order)
                                @php
                                    $paid = $order->payments->where('status', 'approved')->sum('amount');
                                    $due = $order->final_amount - $paid;
                                    $statusClass = $due == 0 ? 'bg-success' : ($paid > 0 ? 'bg-warning' : 'bg-danger');
                                    $statusText = $due == 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Pending');
                                @endphp
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->service->name ?? 'N/A' }}</td>
                                    <td>₹ {{ number_format($order->final_amount, 2) }}</td>
                                    <td>₹ {{ number_format($paid, 2) }}</td>
                                    <td>₹ {{ number_format($due, 2) }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td>
                                        @php
                                            $tasks = $order->tasks;
                                        @endphp
                                        @if($tasks->count() > 0)
                                            @php
                                                $completed = $tasks->whereIn('status', ['completed','verified'])->count();
                                                $total = $tasks->count();
                                            @endphp
                                            <span 
                                                class="badge bg-primary view-task-btn"
                                                style="cursor:pointer"
                                                data-tasks='@json($tasks)'
                                            >
                                                {{ $completed }}/{{ $total }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Task</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View Order">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments Tab -->
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Amount (₹)</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Proof</th>
                                <th>Order ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->orders->flatMap->payments as $payment)
                                <tr>
                                    <td>₹ {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->method) }}</td>
                                    <td>
                                        @if($payment->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($payment->proof_path)
                                            <a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank" class="btn btn-sm btn-link">View</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>#{{ $payment->order_id }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoices Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Order ID</th>
                                <th>Amount (₹)</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->orders as $order)
                                @php
                                    $paid = $order->payments->where('status', 'approved')->sum('amount');
                                    $due = $order->final_amount - $paid;
                                    $statusText = $due == 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Pending');
                                    $invoiceNo = 'INV-'.$order->id;
                                @endphp
                                <tr>
                                    <td>{{ $invoiceNo }}</td>
                                    <td>#{{ $order->id }}</td>
                                    <td>₹ {{ number_format($order->final_amount, 2) }}</td>
                                    <td><span class="badge {{ $due == 0 ? 'bg-success' : 'bg-warning' }}">{{ $statusText }}</span></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.download-invoice', $order->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-download"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No invoices available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ledger Tab -->
            <div class="tab-pane fade" id="ledger" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Reference ID</th>
                                <th>Debit (₹)</th>
                                <th>Credit (₹)</th>
                                <th>Balance (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ledgerEntries = [];
                                foreach($client->orders as $order) {
                                    $ledgerEntries[] = [
                                        'date' => $order->created_at,
                                        'type' => 'Order',
                                        'ref_id' => 'ORD-'.$order->id,
                                        'debit' => $order->final_amount,
                                        'credit' => 0,
                                    ];
                                }
                                foreach($client->orders->flatMap->payments as $payment) {
                                    if($payment->status == 'approved') {
                                        $ledgerEntries[] = [
                                            'date' => $payment->created_at,
                                            'type' => 'Payment',
                                            'ref_id' => 'PAY-'.$payment->id,
                                            'debit' => 0,
                                            'credit' => $payment->amount,
                                        ];
                                    }
                                }
                                usort($ledgerEntries, function($a, $b) {
                                    return $a['date']->timestamp - $b['date']->timestamp;
                                });
                                $balance = 0;
                            @endphp
                            @forelse($ledgerEntries as $entry)
                                @php
                                    $balance += $entry['debit'] - $entry['credit'];
                                    $debitClass = $entry['debit'] > 0 ? 'text-danger' : '';
                                    $creditClass = $entry['credit'] > 0 ? 'text-success' : '';
                                @endphp
                                <tr>
                                    <td>{{ $entry['date']->format('d M Y') }}</td>
                                    <td>{{ $entry['type'] }}</td>
                                    <td>{{ $entry['ref_id'] }}</td>
                                    <td class="{{ $debitClass }}">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}</td>
                                    <td class="{{ $creditClass }}">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}</td>
                                    <td><strong>₹ {{ number_format($balance, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No ledger entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Existing Documents Tab (Order Documents) -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Order ID</th>
                                <th>File Name</th>
                                <th>Upload Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allDocuments = collect();
                                foreach($client->orders as $order) {
                                    $documents = $order->documents()->with('documentMaster', 'files')->get();
                                    foreach($documents as $doc) {
                                        foreach($doc->files as $file) {
                                            $allDocuments->push([
                                                'document' => $doc,
                                                'file' => $file
                                            ]);
                                        }
                                    }
                                }
                            @endphp
                            @forelse($allDocuments as $item)
                                @php
                                    $document = $item['document'];
                                    $file = $item['file'];
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $document->documentMaster->document_name ?? 'Document' }}</strong>
                                        @if($document->documentMaster && $document->documentMaster->description)
                                            <br><small class="text-muted">{{ $document->documentMaster->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $document->order_id) }}" class="text-decoration-none">
                                            #{{ $document->order_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <i class="bi bi-file-earmark-text file-icon" data-file-name="{{ $file->file_name }}"></i>
                                        {{ $file->file_name }}
                                        <br><small class="text-muted file-size" data-file-size="{{ $file->file_size }}"></small>
                                    </td>
                                    <td>{{ $file->uploaded_at ? \Carbon\Carbon::parse($file->uploaded_at)->format('d M Y') : $file->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($document->status == 'VERIFIED')
                                            <span class="badge bg-success">Verified</span>
                                        @elseif($document->status == 'APPROVED')
                                            <span class="badge bg-info">Approved</span>
                                        @elseif($document->status == 'REJECTED')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">{{ $document->status ?? 'Pending' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary view-document"
                                                data-document-url="{{ asset('storage/'.$file->file_path) }}"
                                                data-document-name="{{ $document->documentMaster->document_name ?? 'Document' }}"
                                                data-file-name="{{ $file->file_name }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-folder2-open fs-1 text-muted d-block mb-2"></i>
                                        No documents uploaded for any orders.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- CLIENT DOCUMENTS TAB (with DataTables on file list)          -->
            <!-- ============================================================ -->
            <div class="tab-pane fade" id="client-documents" role="tabpanel">
                <div id="clientDocumentsApp">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi bi-folder2-open me-2 text-primary"></i>
                            Client Documents
                        </h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="createFolderBtn">
                                <i class="bi bi-plus-lg me-1"></i> Create Folder
                            </button>
                            <button type="button" class="btn btn-sm btn-success" id="uploadDocumentBtn">
                                <i class="bi bi-upload me-1"></i> Upload Document
                            </button>
                        </div>
                    </div>

                    <nav aria-label="breadcrumb" class="mb-3" id="breadcrumbNav">
                        <ol class="breadcrumb bg-light p-2 rounded">
                            <li class="breadcrumb-item"><a href="#" data-path="">Client Documents</a></li>
                        </ol>
                    </nav>

                    <div id="folderFileListView">
                        <!-- Dynamically rendered by JavaScript -->
                    </div>
                </div>
            </div>
            <!-- END Client Documents Tab -->

        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="order_id" class="form-label">Order</label>
                        <select class="form-select" name="order_id" required>
                            <option value="">Select Order</option>
                            @foreach($client->orders as $order)
                                <option value="{{ $order->id }}">#{{ $order->id }} - ₹ {{ number_format($order->final_amount, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="method" class="form-label">Payment Method</label>
                        <select class="form-select" name="method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="proof" class="form-label">Proof (optional)</label>
                        <input type="file" class="form-control" name="proof">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentViewerTitle">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <span id="documentTitle">Document Viewer</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="documentViewerContent" class="text-center p-4" style="min-height: 500px; max-height: 80vh; overflow: auto;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading document...</p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadDocumentBtn" class="btn btn-primary" target="_blank">
                    <i class="bi bi-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="taskContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createFolderForm">
                    <input type="hidden" id="createFolderCurrentPath" value="">
                    <div class="mb-3">
                        <label for="newFolderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="newFolderName" placeholder="e.g. 2024-25, GST Documents" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createFolderConfirm">Create Folder</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Folder Modal -->
<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renameFolderForm">
                    <input type="hidden" id="renameFolderOldName" value="">
                    <input type="hidden" id="renameFolderCurrentPath" value="">
                    <div class="mb-3">
                        <label for="newFolderNameRename" class="form-label">New Folder Name</label>
                        <input type="text" class="form-control" id="newFolderNameRename" placeholder="Enter new name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="renameFolderConfirm">Rename</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.clients.documents.upload', $client->id) }}" method="POST" enctype="multipart/form-data" id="uploadDocumentForm">
                    @csrf
                    <input type="hidden" name="folder_name" id="uploadFolderPath" value="">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Upload to Folder</label>
                            <p class="form-control-plaintext" id="currentFolderDisplay">/</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Files</label>
                            <input type="file" name="documents[]" multiple class="form-control" required
                                   accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.csv,.zip,.rar">
                            <small class="text-muted d-block mt-1">
                                Allowed: PDF, JPG, PNG, Excel (xls/xlsx), CSV, ZIP, RAR (Max 20MB)
                            </small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Visibility</label>
                            <select name="visibility" class="form-select" required>
                                <option value="client_visible">Client Visible</option>
                                <option value="internal">Internal Only</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload me-1"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- DataTables CSS (only needed for client docs table, but kept) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    .folder-card {
        position: relative;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        min-height: 170px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .folder-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    .folder-card .folder-icon {
        font-size: 42px;
        color: #f5b800;
        line-height: 1;
    }
    .folder-card .folder-content {
        margin-top: 12px;
    }
    .folder-card .folder-content h6 {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
    }
    .folder-card .folder-actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }
    .folder-card .folder-actions .btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 100;
        background: white;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #4b5563;
    }
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        border-bottom: 2px solid #3b82f6;
    }
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-3px);
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .breadcrumb {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }
    .breadcrumb-item a {
        text-decoration: none;
        color: #3b82f6;
        cursor: pointer;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .stats-card .card-value {
            font-size: 1.5rem;
        }
        .nav-tabs .nav-link {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
    }
    .document-viewer-pdf {
        width: 100%;
        height: 70vh;
        border: none;
    }
    .document-viewer-image {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
    }
    .document-viewer-other {
        padding: 2rem;
        text-align: center;
    }
    .alert {
        margin: 1rem;
    }

    /* DataTables overrides (only for client docs table) */
    #clientDocsTable_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
    #clientDocsTable_wrapper .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
    #clientDocsTable.dataTable {
        margin-top: 0 !important;
    }
    #clientDocsTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.2rem 0.6rem;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
    }
    #clientDocsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd;
        color: white !important;
        border-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS (only needed for client docs table) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    function getFileExtension(url) {
        return url.split('.').pop().toLowerCase();
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        let icon = 'bi-file-earmark';
        let color = 'text-secondary';

        switch(extension) {
            case 'pdf':
                icon = 'bi-file-earmark-pdf';
                color = 'text-danger';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
            case 'bmp':
                icon = 'bi-file-earmark-image';
                color = 'text-primary';
                break;
            case 'xls':
            case 'xlsx':
            case 'csv':
                icon = 'bi-file-earmark-excel';
                color = 'text-success';
                break;
            case 'doc':
            case 'docx':
                icon = 'bi-file-earmark-word';
                color = 'text-info';
                break;
            case 'zip':
            case 'rar':
                icon = 'bi-file-earmark-zip';
                color = 'text-warning';
                break;
            case 'txt':
                icon = 'bi-file-earmark-text';
                color = 'text-secondary';
                break;
            default:
                icon = 'bi-file-earmark';
                color = 'text-muted';
        }
        return { icon, color };
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function updateFileIconsAndSizes() {
        document.querySelectorAll('.file-icon').forEach(icon => {
            const fileName = icon.getAttribute('data-file-name');
            if (fileName) {
                const { icon: iconClass, color } = getFileIcon(fileName);
                icon.className = `bi ${iconClass} ${color}`;
            }
        });
        document.querySelectorAll('.file-size').forEach(sizeSpan => {
            const fileSize = sizeSpan.getAttribute('data-file-size');
            if (fileSize) {
                sizeSpan.textContent = formatFileSize(parseInt(fileSize));
            }
        });
    }
    
    function loadDocumentInModal(url, extension) {
        const contentDiv = $('#documentViewerContent');
        if (extension === 'pdf') {
            contentDiv.html(`<iframe src="${url}" class="document-viewer-pdf" frameborder="0"></iframe>`);
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(extension)) {
            contentDiv.html(`<img src="${url}" alt="Document" class="document-viewer-image" onerror="handleImageError(this)">`);
        } else if (extension === 'txt') {
            fetch(url)
                .then(response => response.text())
                .then(text => {
                    contentDiv.html(`<div class="document-viewer-other"><pre class="text-start" style="max-height: 70vh; overflow: auto; background: #f8f9fa; padding: 1rem; border-radius: 0.5rem;">${escapeHtml(text)}</pre></div>`);
                })
                .catch(() => {
                    contentDiv.html(`<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Could not load text file. <a href="${url}" target="_blank">Download</a> to view.</div>`);
                });
        } else {
            contentDiv.html(`<div class="document-viewer-other"><i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #6c757d;"></i><h5 class="mt-3">Preview not available</h5><p class="text-muted">This file type (${extension.toUpperCase()}) cannot be previewed directly.</p><a href="${url}" class="btn btn-primary mt-2" target="_blank"><i class="bi bi-download me-2"></i>Download File</a></div>`);
        }
    }
    
    window.handleImageError = function(img) {
        img.onerror = null;
        img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23999" stroke-width="2"%3E%3Crect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"%3E%3C/rect%3E%3Cpath d="M8 2v20M16 2v20M2 8h20M2 16h20"%3E%3C/path%3E%3C/svg%3E';
        $(img).closest('.document-viewer-content').html(`<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Could not load image. <a href="${img.src}" target="_blank">Open in new tab</a> to view.</div>`);
    };
    
    function getTaskColor(status) {
        switch (status) {
            case 'verified': return 'success';
            case 'completed': return 'success';
            case 'in_progress': return 'primary';
            case 'submitted': return 'info';
            case 'rejected': return 'danger';
            case 'assigned': return 'warning';
            default: return 'secondary';
        }
    }

    $(document).ready(function() {
        // =============================================================
        // CLIENT DOCUMENTS MANAGER – using server-built tree
        // =============================================================
        var folderTree = @json($folderTree);
        var clientId = {{ $client->id }};
        var currentPath = '';
        var deleteDocRoute = "{{ route('admin.clients.documents.delete', '') }}";

        function getSubtree(path) {
            if (!path) return folderTree;
            var parts = path.split('/');
            var current = folderTree;
            for (var i = 0; i < parts.length; i++) {
                if (!current[parts[i]]) return null;
                current = current[parts[i]];
            }
            return current;
        }

        function getChildren(path) {
            var subtree = getSubtree(path);
            if (!subtree) return null;
            var folders = [];
            var files = [];
            for (var key in subtree) {
                if (key === '_files') continue;
                folders.push(key);
            }
            if (subtree._files) {
                files = subtree._files;
            }
            return { folders: folders, files: files };
        }

        function renderCurrentFolder() {
            var children = getChildren(currentPath);
            var container = $('#folderFileListView');
            if (!children) {
                container.html('<div class="alert alert-warning">Folder not found.</div>');
                return;
            }

            var html = '';
            // Render folder cards
            if (children.folders.length > 0) {
                html += '<div class="row g-3">';
                children.folders.forEach(function(folder) {
                    var fullPath = currentPath ? currentPath + '/' + folder : folder;
                    html += `
                        <div class="col-md-4 col-lg-3">
                            <div class="folder-card" data-path="${fullPath}">
                                <div class="folder-icon"><i class="bi bi-folder-fill"></i></div>
                                <div class="folder-content">
                                    <h6 class="mb-1">${escapeHtml(folder)}</h6>
                                    <small class="text-muted">Click to open</small>
                                </div>
                                <div class="folder-actions">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rename-folder-btn" data-folder="${escapeHtml(folder)}" data-current-path="${escapeHtml(currentPath)}" title="Rename Folder">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-folder-btn" data-folder="${escapeHtml(folder)}" data-current-path="${escapeHtml(currentPath)}" title="Delete Folder">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }

            // Render files as a DataTable
            var visibleFiles = (children.files || []).filter(function(doc) {
                return !doc.file_name.startsWith('.');
            });

            if (visibleFiles.length > 0) {
                html += `
                    <div class="table-responsive mt-3">
                        <table class="table table-hover" id="clientDocsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>File</th>
                                    <th>Visibility</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                visibleFiles.forEach(function(doc) {
                    var visibilityBadge = doc.visibility === 'internal' 
                        ? `<span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>Internal</span>`
                        : `<span class="badge bg-success"><i class="bi bi-eye-fill me-1"></i>Client</span>`;
                    
                    var iconData = getFileIcon(doc.file_name);
                    var fileIcon = iconData.icon;
                    var iconColor = iconData.color;
                    
                    var fileSize = formatFileSize(parseInt(doc.file_size) || 0);
                    var date = new Date(doc.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                    var fileUrl = '/storage/' + doc.file_path;
                    var deleteUrl = deleteDocRoute + '/' + doc.id;
                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi ${fileIcon} ${iconColor} fs-5 me-2"></i>
                                    <div>
                                        <strong>${escapeHtml(doc.file_name)}</strong>
                                        ${doc.file_type ? `<br><small class="text-muted">${escapeHtml(doc.file_type)}</small>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>${visibilityBadge}</td>
                            <td>${fileSize}</td>
                            <td>${date}</td>
                            <td class="text-end">
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                                <form action="${deleteUrl}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this document?')">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            }

            if (children.folders.length === 0 && visibleFiles.length === 0) {
                html = '<div class="text-center py-5 border rounded bg-light"><i class="bi bi-folder2-open fs-1 text-muted d-block mb-3"></i><h6 class="text-muted">This folder is empty.</h6><p class="text-muted mb-3">Create a subfolder or upload documents.</p></div>';
            }

            container.html(html);
            updateBreadcrumb();
            $('#uploadFolderPath').val(currentPath);
            $('#currentFolderDisplay').text(currentPath || '/');

            // 🔥 Initialize DataTables on the client documents table if it exists
            if ($.fn.DataTable && $('#clientDocsTable').length) {
                // Destroy any existing instance
                if ($.fn.DataTable.isDataTable('#clientDocsTable')) {
                    $('#clientDocsTable').DataTable().destroy();
                }
                $('#clientDocsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    order: [[3, 'desc']], // sort by Date descending
                    columnDefs: [
                        { orderable: false, targets: [1, 4] } // Visibility and Actions columns not sortable
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries available",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        zeroRecords: "No matching records found"
                    }
                });
            }
        }

        function updateBreadcrumb() {
            var breadcrumb = $('#breadcrumbNav ol');
            breadcrumb.empty();
            breadcrumb.append(`<li class="breadcrumb-item"><a href="#" data-path="">Client Documents</a></li>`);
            if (currentPath) {
                var parts = currentPath.split('/');
                var pathAcc = '';
                parts.forEach(function(part, index) {
                    if (index > 0) pathAcc += '/';
                    pathAcc += part;
                    var isLast = (index === parts.length - 1);
                    if (isLast) {
                        breadcrumb.append(`<li class="breadcrumb-item active">${escapeHtml(part)}</li>`);
                    } else {
                        breadcrumb.append(`<li class="breadcrumb-item"><a href="#" data-path="${escapeHtml(pathAcc)}">${escapeHtml(part)}</a></li>`);
                    }
                });
            }
        }

        function navigateTo(path) {
            currentPath = path;
            renderCurrentFolder();
        }

        $(document).on('click', '.folder-card', function(e) {
            if ($(e.target).closest('.folder-actions').length) return;
            var path = $(this).data('path');
            if (path) navigateTo(path);
        });

        $(document).on('click', '#breadcrumbNav a', function(e) {
            e.preventDefault();
            var path = $(this).data('path');
            if (path !== undefined) navigateTo(path);
        });

        $('#createFolderBtn').click(function() {
            $('#createFolderCurrentPath').val(currentPath);
            $('#newFolderName').val('');
            $('#createFolderModal').modal('show');
        });

        $('#createFolderConfirm').click(function(e) {
            e.preventDefault();
            var folderName = $('#newFolderName').val().trim();
            if (!folderName) {
                alert('Please enter a folder name.');
                return;
            }
            var path = $('#createFolderCurrentPath').val();

            $.ajax({
                url: "{{ route('admin.clients.documents.folders.create', $client->id) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    folder_name: folderName,
                    current_path: path
                },
                success: function(resp) {
                    if (resp.success) {
                        window.location.reload();
                    } else {
                        alert(resp.message || 'Error creating folder.');
                    }
                },
                error: function() {
                    alert('Server error while creating folder.');
                }
            });
        });

        $(document).on('click', '.rename-folder-btn', function(e) {
            e.stopPropagation();
            var folder = $(this).data('folder');
            var path = $(this).data('current-path');
            $('#renameFolderOldName').val(folder);
            $('#renameFolderCurrentPath').val(path);
            $('#newFolderNameRename').val('');
            $('#renameFolderModal').modal('show');
        });

        $('#renameFolderConfirm').click(function(e) {
            e.preventDefault();
            var oldName = $('#renameFolderOldName').val();
            var newName = $('#newFolderNameRename').val().trim();
            var path = $('#renameFolderCurrentPath').val();

            if (!newName) {
                alert('Please enter a new folder name.');
                return;
            }

            $.ajax({
                url: "{{ route('admin.clients.documents.folders.rename', $client->id) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    old_name: oldName,
                    new_name: newName,
                    current_path: path
                },
                success: function(resp) {
                    if (resp.success) {
                        window.location.reload();
                    } else {
                        alert(resp.message || 'Error renaming folder.');
                    }
                },
                error: function() {
                    alert('Server error while renaming.');
                }
            });
        });

        $(document).on('click', '.delete-folder-btn', function(e) {
            e.stopPropagation();
            var folder = $(this).data('folder');
            var path = $(this).data('current-path');

            if (!confirm('Are you sure you want to permanently delete the folder "' + folder + '" and all its contents? This cannot be undone.')) {
                return;
            }

            $.ajax({
                url: "{{ route('admin.clients.documents.folders.delete', $client->id) }}",
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                    folder_name: folder,
                    current_path: path
                },
                success: function(resp) {
                    if (resp.success) {
                        window.location.reload();
                    } else {
                        alert(resp.message || 'Error deleting folder.');
                    }
                },
                error: function() {
                    alert('Server error while deleting.');
                }
            });
        });

        $('#uploadDocumentBtn').click(function() {
            $('#uploadFolderPath').val(currentPath);
            $('#currentFolderDisplay').text(currentPath || '/');
            $('#uploadDocumentModal').modal('show');
        });

        $('#downloadLedgerBtn').click(function() {
            alert('Download ledger feature will be implemented.');
        });

        updateFileIconsAndSizes();

        $(document).on('click', '.view-document', function(e) {
            e.preventDefault();
            const documentUrl = $(this).data('document-url');
            const documentName = $(this).data('document-name') || $(this).data('file-name') || 'Document';
            const fileExtension = getFileExtension(documentUrl);
            
            $('#documentTitle').text(documentName);
            $('#downloadDocumentBtn').attr('href', documentUrl);
            
            const contentDiv = $('#documentViewerContent');
            contentDiv.html(`<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading document...</p>`);
            
            const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
            modal.show();
            
            setTimeout(() => {
                loadDocumentInModal(documentUrl, fileExtension);
            }, 100);
        });

        $('#documentViewerModal').on('hidden.bs.modal', function() {
            $('#documentViewerContent').html(`<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading document...</p>`);
            $('#downloadDocumentBtn').attr('href', '#');
        });

        $(document).on('click', '.view-task-btn', function() {
            let tasks = JSON.parse($(this).attr('data-tasks'));
            let html = '';
            tasks.forEach(task => {
                html += `<div class="border p-2 mb-2 rounded"><strong>${task.task_name ?? 'Task'}</strong><br>Status: <span class="badge bg-${getTaskColor(task.status)}">${task.status}</span><br>Due: ${task.due_date ?? 'N/A'}</div>`;
            });
            $('#taskContent').html(html);
            let modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        });

        // Initial render
        renderCurrentFolder();
    });
</script>
@endpush