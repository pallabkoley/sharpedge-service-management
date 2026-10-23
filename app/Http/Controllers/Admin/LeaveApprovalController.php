<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveApprovalController extends Controller
{
    /**
     * Admin Leave Approval Page
     * - list all leave requests
     * - search + filter status
     * - show stats
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::query()
            ->with(['leaveType', 'staff', 'user', 'approvedBy'])
            ->latest();

        // Search (staff name, staff code, leave type, reason)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('leaveType', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staff', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%{$search}%")
                         ->orWhere('staff_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q4) use ($search) {
                      $q4->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->paginate(10);

        // Stats
        $stats = LeaveRequest::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        return view('admin.leave_approvals.index', compact('leaveRequests', 'stats'));
    }

    /**
     * Approve leave request
     * - only pending can be approved
     */
    public function approve($id)
    {
        $leave = LeaveRequest::query()->findOrFail($id);

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request is already processed. You cannot change it.');
        }

        $leave->update([
            'status'           => 'approved',
            'approved_by'      => Auth::id(),
            'approved_at'      => Carbon::now(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject leave request
     * - only pending can be rejected
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::query()->findOrFail($id);

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request is already processed. You cannot change it.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => Carbon::now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Leave request rejected successfully.');
    }
public function showDetails($id)
{
    $leave = LeaveRequest::with([
            'leaveType:id,name',
            'staff:id,staff_code,name,department,designation',
            'user:id,name,email',
            'approvedBy:id,name'
        ])
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'leave' => $leave
    ]);
}

}
