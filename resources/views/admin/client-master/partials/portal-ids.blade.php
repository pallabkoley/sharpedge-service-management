{{-- ============================================================
    PORTAL IDs – Full Width (First Screenshot Style)
============================================================ --}}

<div class="row">
    <div class="col-12">
        {{-- Add Portal Credential – Full Width Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-plus-circle me-2"></i> Add Portal Credential
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="alert alert-warning small py-2">
                    <i class="bi bi-shield-lock me-1"></i>
                    Credentials are stored securely using Laravel encryption.
                </div>

                <form action="{{ route('admin.client-master.portal-credentials.store', $client) }}" method="POST">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Portal <span class="text-danger">*</span></label>
                            <select name="portal" class="form-select form-select-sm" required>
                                <option value="">Select</option>
                                <option value="GST Portal">GST Portal</option>
                                <option value="Income Tax">Income Tax</option>
                                <option value="MCA">MCA</option>
                                <option value="TDS">TDS</option>
                                <option value="TRACES">TRACES</option>
                                <option value="EPFO">EPFO</option>
                                <option value="ESIC">ESIC</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Other Portal Name</label>
                            <input type="text" name="other_portal_name" class="form-control form-control-sm" placeholder="If 'Other' selected above">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Login ID / Username</label>
                            <input type="text" name="login_username" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Password</label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" class="form-control form-control-sm password-input" placeholder="Enter password">
                                <button class="btn btn-outline-secondary btn-sm password-toggle" type="button" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Leave blank to keep existing password (if editing).</small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Notes</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Any additional details..."></textarea>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-plus-lg me-1"></i> Add Credential
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stored Credentials – Full Width --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-database me-2"></i> Stored Credentials
                </h6>
            </div>
            <div class="card-body pt-0">
                @if($client->portalCredentials->count())
                    @foreach($client->portalCredentials as $credential)
                        <div class="case-entry border rounded-3 p-3 mb-3 bg-white shadow-sm">
                            {{-- Credential header --}}
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold fs-6 text-dark">{{ $credential->portal }}</span>
                                    @if($credential->other_portal_name)
                                        <span class="badge bg-info-subtle text-info ms-1">{{ $credential->other_portal_name }}</span>
                                    @endif
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editPortal({{ $credential->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    {{-- Delete with both client and credential parameters --}}
                                    <form method="POST" 
                                          action="{{ route('admin.client-master.portal-credentials.destroy', ['client' => $client->id, 'credential' => $credential->id]) }}"
                                          style="display:inline-block;"
                                          onsubmit="return confirm('Delete this portal credential?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Credential details – 3 columns --}}
                            <div class="row mt-2 small">
                                <div class="col-md-4">
                                    <span class="text-muted text-uppercase fs-10">Username</span><br>
                                    <span class="fw-semibold">{{ $credential->login_username ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted text-uppercase fs-10">Password</span><br>
                                    <span class="fw-semibold">
                                        <span class="password-mask" data-password="{{ $credential->password }}" data-visible="false">
                                            ••••••••
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link p-0 ms-1 toggle-credential-password" style="font-size:12px;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </span>
                                </div>
                                @if($credential->notes)
                                    <div class="col-md-4">
                                        <span class="text-muted text-uppercase fs-10">Notes</span><br>
                                        <span class="fw-semibold">{{ $credential->notes }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-key fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">No portal credentials saved.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
    EDIT PORTAL CREDENTIAL MODAL (fully rewritten)
============================================================ --}}

<div class="modal fade" id="editPortalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="editPortalForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i> Edit Portal Credential
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div id="editPortalLoading" class="text-center py-3 d-none">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="ms-2">Loading...</span>
                    </div>

                    <div id="editPortalFields">

                        {{-- Portal --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Portal <span class="text-danger">*</span></label>
                            <select name="portal" id="edit_portal" class="form-select form-select-sm" required>
                                <option value="GST Portal">GST Portal</option>
                                <option value="Income Tax">Income Tax</option>
                                <option value="MCA">MCA</option>
                                <option value="TDS">TDS</option>
                                <option value="TRACES">TRACES</option>
                                <option value="EPFO">EPFO</option>
                                <option value="ESIC">ESIC</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        {{-- Other Portal --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Other Portal Name</label>
                            <input type="text" name="other_portal_name" id="edit_other_portal_name" class="form-control form-control-sm">
                        </div>

                        {{-- Username --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Login ID / Username</label>
                            <input type="text" name="login_username" id="edit_login_username" class="form-control form-control-sm">
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="edit_password" class="form-control" placeholder="Enter new password">
                                <button type="button" class="btn btn-outline-secondary" id="editPasswordToggle">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Leave blank to keep existing password.</small>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" id="edit_notes" class="form-control form-control-sm" rows="3"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="editPortalSubmit">
                        <i class="bi bi-check-lg me-1"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    .fs-10 {
        font-size: 0.65rem;
        letter-spacing: 0.02em;
    }
    .case-entry {
        transition: box-shadow 0.15s ease;
    }
    .case-entry:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06) !important;
    }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .text-info { color: #0dcaf0 !important; }
    .password-input {
        border-right: 0;
    }
    .password-toggle {
        border-left: 0;
        background: #fff;
    }
    .password-toggle:hover {
        background: #f1f3f5;
    }
    .toggle-credential-password {
        color: #6c757d;
        cursor: pointer;
    }
    .toggle-credential-password:hover {
        color: #0d6efd;
    }
</style>

{{-- Scripts --}}
<script>
$(document).ready(function () {

    // --------------------------------------------------------------
    // 1. Password toggle for Add form
    // --------------------------------------------------------------
    $(document).on('click', '.password-toggle', function () {
        let input = $(this).closest('.input-group').find('.password-input');
        let icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // --------------------------------------------------------------
    // 2. Password toggle for stored credentials (show/hide)
    // --------------------------------------------------------------
    $(document).on('click', '.toggle-credential-password', function () {
        const maskSpan = $(this).closest('span').find('.password-mask');
        const icon = $(this).find('i');
        const actualPassword = maskSpan.attr('data-password');

        if (maskSpan.attr('data-visible') === 'true') {
            maskSpan.text('••••••••');
            maskSpan.attr('data-visible', 'false');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            maskSpan.text(actualPassword);
            maskSpan.attr('data-visible', 'true');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // --------------------------------------------------------------
    // 3. EDIT PORTAL – fetch and populate
    // --------------------------------------------------------------
    window.editPortal = function (id) {
        const clientId = {{ $client->id }};

        const modalElement = document.getElementById('editPortalModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

        const form = document.getElementById('editPortalForm');
        const loading = document.getElementById('editPortalLoading');
        const fields = document.getElementById('editPortalFields');
        const submitButton = document.getElementById('editPortalSubmit');

        // Set the correct PUT URL
        form.action = "{{ url('admin/client-master') }}/" + clientId + "/portal-credentials/" + id;

        // Reset fields to defaults
        document.getElementById('edit_portal').value = 'GST Portal';
        document.getElementById('edit_other_portal_name').value = '';
        document.getElementById('edit_login_username').value = '';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_notes').value = '';

        // Show loading, hide fields
        loading.classList.remove('d-none');
        fields.classList.add('d-none');
        submitButton.disabled = true;

        modal.show();

        // Fetch existing credential data
        const fetchUrl = "{{ url('admin/client-master') }}/" + clientId + "/portal-credentials/" + id;

        fetch(fetchUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Fill fields
            document.getElementById('edit_portal').value = data.portal || 'GST Portal';
            document.getElementById('edit_other_portal_name').value = data.other_portal_name || '';
            document.getElementById('edit_login_username').value = data.login_username || '';
            document.getElementById('edit_notes').value = data.notes || '';
            // Password intentionally left blank

            loading.classList.add('d-none');
            fields.classList.remove('d-none');
            submitButton.disabled = false;
        })
        .catch(error => {
            console.error('Portal credential loading error:', error);
            loading.innerHTML = `
                <div class="text-danger small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Failed to load portal credential.
                </div>
            `;
            submitButton.disabled = true;
        });
    };

    // --------------------------------------------------------------
    // 4. Edit modal password toggle
    // --------------------------------------------------------------
    $(document).on('click', '#editPasswordToggle', function () {
        const input = $('#edit_password');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

});
</script>