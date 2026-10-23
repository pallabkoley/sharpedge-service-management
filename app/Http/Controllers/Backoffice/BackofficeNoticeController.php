<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class BackofficeNoticeController extends Controller
{
    /**
     * Show notices for backoffice panel
     */
    public function index()
    {
        // Get notices that are active and for backoffice role or all roles
        $notices = Notice::where('status', 'active')
            ->where(function ($query) {
                $query->where('target_role', 'backoffice')
                      ->orWhere('target_role', 'all');
            })
            ->where(function ($query) {
                $query->whereDate('start_date', '<=', now())
                      ->orWhereNull('start_date');
            })
            ->where(function ($query) {
                $query->whereDate('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Backoffice.notices.index', compact('notices'));
    }
}
