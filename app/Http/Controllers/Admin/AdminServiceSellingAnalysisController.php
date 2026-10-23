<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminServiceSellingAnalysisController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // 📅 DATE FILTER (OPTIONAL)
        // =========================
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->end_date 
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        // =========================
        // 📦 ORDER QUERY (with optional date filter)
        // =========================
        $orderQuery = Order::query();

        if ($startDate && $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // =========================
        // 📊 SERVICE STATISTICS VIA ORDER_ITEMS (more flexible)
        // =========================
        $serviceStatsQuery = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->select(
                'services.id as service_id',
                'services.service_name',
                DB::raw('COUNT(order_items.id) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('services.id', 'services.service_name');

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $serviceStatsQuery->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        $serviceStats = $serviceStatsQuery->orderByDesc('total_revenue')->get();

        // =========================
        // 📈 TOTALS (using Order query)
        // =========================
        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (clone $orderQuery)->sum('final_amount') ?? 0;

        // =========================
        // 🏆 TOP & LOWEST SERVICE
        // =========================
        $topService = $serviceStats->first();
        $lowestService = $serviceStats->last();

        // =========================
        // 📊 DAILY SALES TREND (for chart)
        // =========================
        $dailySalesQuery = Order::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(final_amount) as revenue')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC');

        if ($startDate && $endDate) {
            $dailySalesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $dailySales = $dailySalesQuery->get();

        // Chart data (empty if no sales)
        $chartLabels  = $dailySales->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $chartOrders  = $dailySales->pluck('total_orders');
        $chartRevenue = $dailySales->pluck('revenue');

        // =========================
        // 📦 RETURN VIEW
        // =========================
        return view('admin.services.sellinganalysis', [
            'serviceStats'  => $serviceStats,
            'totalOrders'   => $totalOrders,
            'totalRevenue'  => $totalRevenue,
            'topService'    => $topService,
            'lowestService' => $lowestService,
            'startDate'     => $startDate ? $startDate->format('Y-m-d') : '',
            'endDate'       => $endDate ? $endDate->format('Y-m-d') : '',
            'chartLabels'   => $chartLabels,
            'chartOrders'   => $chartOrders,
            'chartRevenue'  => $chartRevenue,
        ]);
    }
}