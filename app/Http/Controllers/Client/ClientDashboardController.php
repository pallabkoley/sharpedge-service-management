<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Task;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    /**
     * Client Dashboard
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Get Logged-in Client
        |--------------------------------------------------------------------------
        */
        $client = Client::where('user_id', auth()->id())->first();

        if (!$client) {
            abort(403, 'Client profile not found');
        }

        $clientId = $client->id;

        /*
        |--------------------------------------------------------------------------
        | 2. Dashboard Summary Cards
        |--------------------------------------------------------------------------
        */

        // Total Orders
        $totalOrders = Order::where('client_id', $clientId)->count();

        // Active Orders
        $activeOrders = Order::where('client_id', $clientId)
            ->whereIn('order_status', [
                'PENDING_PAYMENT',
                'PAYMENT_SUBMITTED',
                'CREDIT_PENDING'
            ])
            ->count();

        // Pending Client Actions
        $pendingActions = Task::whereHas('order', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->where('status', 'waiting_client')
            ->count();

        // Total Paid Amount
        $totalPaidAmount = Order::where('client_id', $clientId)
            ->where('order_status', 'PAID')
            ->sum('final_amount');

        /*
        |--------------------------------------------------------------------------
        | 3. Current Active Order
        |--------------------------------------------------------------------------
        */
        $currentOrder = Order::with('service')
            ->where('client_id', $clientId)
            ->whereIn('order_status', [
                'PENDING_PAYMENT',
                'PAYMENT_SUBMITTED',
                'CREDIT_PENDING'
            ])
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 4. Order Status Chart (Donut Chart Data)
        |--------------------------------------------------------------------------
        */
        $statusData = Order::where('client_id', $clientId)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        // Chart labels and values
        $chartLabels = $statusData->keys();
        $chartValues = $statusData->values();

        /*
        |--------------------------------------------------------------------------
        | 5. Recent Orders
        |--------------------------------------------------------------------------
        */
        $recentOrders = Order::with('service')
            ->where('client_id', $clientId)
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 6. Return View
        |--------------------------------------------------------------------------
        */
        return view('Client.dashboard', compact(
            'client',
            'totalOrders',
            'activeOrders',
            'pendingActions',
            'totalPaidAmount',
            'currentOrder',
            'recentOrders',
            'chartLabels',
            'chartValues'
        ));
    }
}
