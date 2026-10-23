<script>
// ============================================================
//  BUSINESS / PROFESSION – VIEW ONLY
//  Fetches from Trading / Profit & Loss module
//  No save logic, no database operations
// ============================================================

// ----- Open View Modal -----
function openBpModal() {
    const modalEl = document.getElementById('bpModal');
    if (!modalEl) {
        alert('Business modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- calcBP is a no-op (no calculations needed) -----
function calcBP() {
    // Business income is fetched from Trading / P&L module
    // No calculations performed here
}
</script>