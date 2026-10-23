{{-- ─── TABS ─── --}}
<ul class="nav nav-tabs-custom" id="registerTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="debit-tab" data-bs-toggle="tab" data-bs-target="#debitTab" type="button" role="tab" aria-controls="debitTab" aria-selected="true" onclick="switchTab('debit')">
            <i class="bi bi-file-earmark-text"></i> Debit Note Register
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="credit-tab" data-bs-toggle="tab" data-bs-target="#creditTab" type="button" role="tab" aria-controls="creditTab" aria-selected="false" onclick="switchTab('credit')">
            <i class="bi bi-file-earmark-check"></i> Credit Note Register
        </button>
    </li>
</ul>