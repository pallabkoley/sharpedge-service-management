{{-- /home2/xinksof4/sharpedge.xinksoft.site/resources/views/Marketing/payment/steps/step-1.blade.php --}}
<form action="{{ route('marketing.orders.process-step') }}" method="POST">
    @csrf
    <input type="hidden" name="step" value="1">
    
    <!-- Step Header -->
    <div class="step-header mb-5">
        <h2 class="fw-bold text-gray-800 mb-2">Step 1: Select Client</h2>
        <p class="text-gray-600">Choose a client from your assigned clients list.</p>
    </div>

    <!-- Client Selection -->
    <div class="mb-4">
        <h5 class="fw-semibold text-gray-700 mb-3">Select Client</h5>
       <select name="client_id" id="client_id" 
        class="form-select form-select-lg select2" required>
            <option value="">-- Select Client --</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" 
                        data-client-name="{{ $client->name }}"
                       
                        data-client-email="{{ $client->email }}">
                    {{ $client->name }} - ({{ $client->email }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Action Button -->
    <div class="d-flex justify-content-end mt-5 pt-4 border-top">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="continueBtn" disabled>
            Continue to Service Selection
        </button>
    </div>
</form>

<style>
    .step-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .step-header p {
        color: #718096;
        font-size: 0.95rem;
    }
    
    .form-select-lg {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        padding: 12px 16px;
        height: auto;
    }
    
    .form-select-lg:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        outline: none;
    }
    
    .btn-primary {
        background-color: #4299e1;
        border-color: #4299e1;
        padding: 12px 30px;
        font-weight: 500;
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .btn-primary:hover {
        background-color: #3182ce;
        border-color: #3182ce;
    }
    
    .btn-primary:disabled {
        background-color: #cbd5e0;
        border-color: #cbd5e0;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .border-top {
        border-top: 1px solid #e2e8f0;
    }
    
    .text-gray-800 {
        color: #2d3748;
    }
    
    .text-gray-700 {
        color: #4a5568;
    }
    
    .text-gray-600 {
        color: #718096;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.getElementById('client_id');
        const continueBtn = document.getElementById('continueBtn');
        
        // Update button state based on selection
        clientSelect.addEventListener('change', function() {
            if (this.value) {
                continueBtn.disabled = false;
            } else {
                continueBtn.disabled = true;
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const clientId = clientSelect.value;
            if (!clientId) {
                e.preventDefault();
                alert('Please select a client first.');
                return false;
            }
            
            // Show loading state
            const originalText = continueBtn.innerHTML;
            continueBtn.innerHTML = 'Processing...';
            continueBtn.disabled = true;
            
            // Allow form to submit normally
            return true;
        });
    });
</script>

<script>
$(document).ready(function() {

    $('#client_id').select2({
        placeholder: "Search Client...",
        allowClear: true,
        width: '100%'
    });

    const continueBtn = document.getElementById('continueBtn');

    $('#client_id').on('change', function () {
        continueBtn.disabled = !$(this).val();
    });

    $('form').on('submit', function(e) {
        if (!$('#client_id').val()) {
            e.preventDefault();
            alert('Please select a client first.');
            return false;
        }

        continueBtn.innerHTML = 'Processing...';
        continueBtn.disabled = true;
    });

});
</script>