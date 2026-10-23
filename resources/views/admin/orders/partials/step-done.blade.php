<div class="text-center py-5">
    <div class="mb-4">
        <div class="checkmark-circle">
            <div class="checkmark">✓</div>
        </div>
    </div>
    
    <h3 class="text-success">Order Created Successfully!</h3>
    <p class="lead">Service purchased for <strong>{{ $order->client->name }}</strong></p>
    
    <div class="card mx-auto mt-4" style="max-width: 400px;">
        <div class="card-body text-left">
            <h5 class="card-title">Order Details</h5>
            <table class="table table-sm">
                <tr>
                    <td>Order ID:</td>
                    <td><strong>{{ $order->order_id }}</strong></td>
                </tr>
                <tr>
                    <td>Service:</td>
                    <td>{{ $order->items->first()->service->service_name }}</td>
                </tr>
                <tr>
                    <td>Amount:</td>
                    <td class="text-success">
                        <strong>₹{{ number_format($order->final_amount, 2) }}</strong>
                    </td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><span class="badge badge-success">PAID</span></td>
                </tr>
                <tr>
                    <td>Invoice:</td>
                    <td>
                        <span class="badge badge-info">Generated</span>
                        <small class="text-muted">#{{ $order->invoice_path }}</small>
                    </td>
                </tr>
                <tr>
                    <td>Tasks Created:</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $order->tasks->count() }} tasks
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-warning">
                    <i class="fas fa-tasks mr-2"></i>
                    <strong>Next Steps:</strong> Tasks have been created and are available in 
                    Task Management for assignment.
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <form method="POST" action="{{ route('admin.orders.processStep') }}" class="d-inline">
                @csrf
                <input type="hidden" name="step" value="4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Another Order
                </button>
            </form>
            <a href="{{ route('admin.orders.show', $order->id) }}" 
               class="btn btn-outline-info ml-2">
                <i class="fas fa-eye mr-2"></i> View Order Details
            </a>
        </div>
    </div>
</div>

<style>
.checkmark-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #28a745;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.checkmark {
    color: white;
    font-size: 40px;
    font-weight: bold;
}
</style>