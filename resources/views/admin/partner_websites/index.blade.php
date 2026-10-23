@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Partner Websites</h1>
                <p class="text-muted mb-0">Manage external partner websites and their logos</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWebsiteModal">
                <i class="bi bi-plus-lg me-2"></i>Add Website
            </button>
        </div>
    </div>

    <!-- Websites Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover datatable" id="websitesTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th style="width: 80px;">Logo</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="websitesTableBody">
                    @forelse($websites as $website)
                    <tr data-id="{{ $website->id }}">
                        <td>
                            <input type="number" class="form-control form-control-sm order-input" 
                                   value="{{ $website->display_order }}" style="width: 70px;" 
                                   data-id="{{ $website->id }}">
                        </td>
                        <td>
                            @if($website->logo)
                                <img src="{{ asset('storage/'.$website->logo) }}" alt="{{ $website->name }}" 
                                     style="width: 50px; height: 50px; object-fit: contain;" class="rounded border">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $website->name }}</td>
                        <td>
                            <a href="{{ $website->url }}" target="_blank" class="text-decoration-none">
                                {{ Str::limit($website->url, 40) }}
                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                            </a>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" 
                                       data-id="{{ $website->id }}" 
                                       {{ $website->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary edit-website" 
                                        data-id="{{ $website->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-website" 
                                        data-id="{{ $website->id }}" data-name="{{ $website->name }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-globe display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Partner Websites</h5>
                            <p class="text-muted mb-0">Click "Add Website" to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Website Modal -->
<div class="modal fade" id="addWebsiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Partner Website</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addWebsiteForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Website Name</label>
                        <input type="text" class="form-control" name="name" required>
                        <div class="invalid-feedback">Name is required</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/webp" required>
                        <div class="form-text">JPG, PNG, WEBP (max 2MB)</div>
                        <div class="invalid-feedback">Logo is required (image file)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Website URL</label>
                        <input type="url" class="form-control" name="url" required>
                        <div class="invalid-feedback">Enter a valid URL (including https://)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" value="0">
                        <div class="form-text">Lower numbers appear first</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveWebsiteBtn">
                        <span id="saveBtnText">Save Website</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Website Modal -->
<div class="modal fade" id="editWebsiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Partner Website</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editWebsiteForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Website Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <div class="mb-2">
                            <img id="editLogoPreview" src="" style="max-height: 80px;" class="border rounded p-1">
                        </div>
                        <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Leave empty to keep current logo</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Website URL</label>
                        <input type="url" class="form-control" id="editUrl" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="editOrder" name="display_order">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="editActive" name="is_active" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateWebsiteBtn">
                        <span id="updateBtnText">Update Website</span>
                        <span class="spinner-border spinner-border-sm d-none" id="updateSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem;
}
.required:after {
    content: " *";
    color: #dc2626;
}
.order-input {
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Toast helper
function showToast(message, type = 'success') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1055;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    $('.toast').remove();
    $('body').append(toastHtml);
    const toast = new bootstrap.Toast($('.toast').last()[0], { delay: 3000 });
    toast.show();
}

// Add Website
$('#addWebsiteForm').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    const formData = new FormData(form);
    $('#saveWebsiteBtn').prop('disabled', true);
    $('#saveBtnText').text('Saving...');
    $('#saveSpinner').removeClass('d-none');

    $.ajax({
        url: "{{ route('admin.partner-websites.store') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(res) {
            if (res.status) {
                showToast(res.message);
                $('#addWebsiteModal').modal('hide');
                location.reload(); // simple reload – could also append row
            }
        },
        error: function(xhr) {
            let err = xhr.responseJSON?.message || 'Error saving website';
            showToast(err, 'danger');
        },
        complete: function() {
            $('#saveWebsiteBtn').prop('disabled', false);
            $('#saveBtnText').text('Save Website');
            $('#saveSpinner').addClass('d-none');
        }
    });
});

// Load Edit Modal
$(document).on('click', '.edit-website', function() {
    const id = $(this).data('id');
    $.get(`/admin/partner-websites/${id}`, function(res) {
        if (res.status) {
            const w = res.data;
            $('#editId').val(w.id);
            $('#editName').val(w.name);
            $('#editUrl').val(w.url);
            $('#editOrder').val(w.display_order);
            $('#editActive').prop('checked', w.is_active == 1);
            if (w.logo) {
                $('#editLogoPreview').attr('src', `/storage/${w.logo}`);
            } else {
                $('#editLogoPreview').attr('src', '');
            }
            $('#editWebsiteModal').modal('show');
        }
    }).fail(() => showToast('Failed to load website data', 'danger'));
});

// Update Website
$('#editWebsiteForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    const formData = new FormData(this);
    formData.append('_method', 'POST'); // Laravel method spoofing

    $('#updateWebsiteBtn').prop('disabled', true);
    $('#updateBtnText').text('Updating...');
    $('#updateSpinner').removeClass('d-none');

    $.ajax({
        url: `/admin/partner-websites/${id}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(res) {
            if (res.status) {
                showToast(res.message);
                $('#editWebsiteModal').modal('hide');
                location.reload();
            }
        },
        error: function(xhr) {
            let err = xhr.responseJSON?.message || 'Update failed';
            showToast(err, 'danger');
        },
        complete: function() {
            $('#updateWebsiteBtn').prop('disabled', false);
            $('#updateBtnText').text('Update Website');
            $('#updateSpinner').addClass('d-none');
        }
    });
});

// Toggle active status
$(document).on('change', '.toggle-status', function() {
    const checkbox = $(this);
    const id = checkbox.data('id');
    const isActive = checkbox.prop('checked');
    $.ajax({
        url: `/admin/partner-websites/toggle/${id}`,
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(res) {
            if (res.status) {
                showToast(res.message);
            } else {
                checkbox.prop('checked', !isActive);
                showToast('Error updating status', 'danger');
            }
        },
        error: function() {
            checkbox.prop('checked', !isActive);
            showToast('Server error', 'danger');
        }
    });
});

// Delete website
$(document).on('click', '.delete-website', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    if (confirm(`Delete "${name}"? This action cannot be undone.`)) {
        $.ajax({
            url: `/admin/partner-websites/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.status) {
                    showToast(res.message);
                    $(`tr[data-id="${id}"]`).remove();
                    if ($('#websitesTableBody tr').length === 0) {
                        $('#websitesTableBody').html(`
                            <tr><td colspan="6" class="text-center py-5">
                                <i class="bi bi-globe display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">No Partner Websites</h5>
                                <p class="text-muted mb-0">Click "Add Website" to get started.</p>
                            </td></tr>
                        `);
                    }
                }
            },
            error: function() {
                showToast('Delete failed', 'danger');
            }
        });
    }
});

// Update display order on change (debounced)
let orderTimeout;
$(document).on('change', '.order-input', function() {
    clearTimeout(orderTimeout);
    const input = $(this);
    const id = input.data('id');
    const newOrder = input.val();
    orderTimeout = setTimeout(() => {
        $.ajax({
            url: `/admin/partner-websites/${id}`,
            method: 'POST',
            data: {
                _method: 'POST',
                display_order: newOrder,
                name: input.closest('tr').find('td:nth-child(3)').text().trim(),
                url: input.closest('tr').find('td:nth-child(4) a').attr('href'),
                is_active: input.closest('tr').find('.toggle-status').prop('checked') ? 1 : 0
            },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.status) showToast('Order updated', 'success');
            },
            error: function() {
                showToast('Failed to update order', 'danger');
            }
        });
    }, 500);
});
</script>
@endpush