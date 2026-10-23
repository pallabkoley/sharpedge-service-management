{{-- ============================================================
    PORTAL IDs – TEST VERSION (Red border to verify file)
============================================================ --}}

<div class="row">
    <div class="col-12">

        {{-- 🔥 TEST: Red border to confirm the file is loaded --}}
        <div style="border: 4px solid red; padding: 15px; margin-bottom: 20px; background: #fff0f0;">
            <strong style="color: red; font-size: 18px;">✅ TEST – এই মেসেজ দেখলে বুঝবেন ফাইলটি লোড হচ্ছে!</strong>
        </div>

        {{-- আপনার মূল কন্টেন্ট (ফুল-উইডথ লেআউট) --}}
        <!-- Add Portal Credential Card (full width) -->
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

                    {{-- Fields in one row --}}
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

                    {{-- Notes --}}
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

        {{-- Stored Credentials --}}
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
                            <div class="row mt-2 small">
                                <div class="col-md-4">
                                    <span class="text-muted text-uppercase fs-10">Username</span><br>
                                    <span class="fw-semibold">{{ $credential->login_username ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted text-uppercase fs-10">Password</span><br>
                                    <span class="fw-semibold">
                                        <span class="password-mask">••••••••</span>
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

{{-- Edit Modal (same as before) --}}
<div class="modal fade" id="editPortalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editPortalForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Portal Credential</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- fields -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fs-10 { font-size: 0.65rem; letter-spacing: 0.02em; }
    .case-entry { transition: box-shadow 0.15s ease; }
    .case-entry:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.06) !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .text-info { color: #0dcaf0 !important; }
    .password-input { border-right: 0; }
    .password-toggle { border-left: 0; background: #fff; }
    .password-toggle:hover { background: #f1f3f5; }
    .toggle-credential-password { color: #6c757d; cursor: pointer; }
    .toggle-credential-password:hover { color: #0d6efd; }
</style>

<script>
    $(document).ready(function() {
        $(document).on('click', '.password-toggle', function() {
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

        $(document).on('click', '.toggle-credential-password', function() {
            let maskSpan = $(this).closest('span').find('.password-mask');
            let icon = $(this).find('i');
            if (maskSpan.text() === '••••••••') {
                maskSpan.text('••••••••');
                icon.toggleClass('bi-eye bi-eye-slash');
            } else {
                maskSpan.text('••••••••');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        window.editPortal = function(id) {
            var clientId = {{ $client->id }};
            var form = document.getElementById('editPortalForm');
            form.action = '/admin/client-master/' + clientId + '/portal-credentials/' + id;
            new bootstrap.Modal(document.getElementById('editPortalModal')).show();
        };
    });
</script>