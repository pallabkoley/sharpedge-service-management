<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\LeavePolicy;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    /**
     * Marketing Leave Apply Page
     * - show leave types dropdown
     * - show my leave requests list
     * - show stats (pending/approved/rejected)
     * - show leave balance (max / used / left)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $staff = Staff::query()
            ->where('user_id', $userId)
            ->first();

        $leaveTypes = LeaveType::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $myLeaves = LeaveRequest::query()
            ->with(['leaveType'])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate(10);

        $stats = LeaveRequest::query()
            ->where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        // Leave Balance (Yearly) - only approved reduces balance
        $currentYear = Carbon::now()->year;

        $leaveBalances = LeaveType::query()
            ->with('policy')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($type) use ($userId, $currentYear) {

                $maxPerYear = $type->policy?->max_per_year ?? 0;

                $approvedUsed = LeaveRequest::query()
                    ->where('user_id', $userId)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'approved')
                    ->whereYear('from_date', $currentYear)
                    ->sum('total_days');

                $left = max(0, $maxPerYear - $approvedUsed);

                return [
                    'leave_type_id'   => $type->id,
                    'leave_type_name' => $type->name,
                    'max_per_year'    => $maxPerYear,
                    'approved_used'   => (float) $approvedUsed,
                    'leave_left'      => (float) $left,
                ];
            });

        return view('Marketing.leave_requests.index', compact(
            'leaveTypes',
            'myLeaves',
            'stats',
            'staff',
            'leaveBalances'
        ));
    }

    /**
     * Store Leave Request (Apply Leave)
     */
   public function store(Request $request)
{
    // If half day, force to_date = from_date
    if ($request->is_half_day) {
        $request->merge([
            'to_date' => $request->from_date
        ]);
    }

    $request->validate([
        'leave_type_id' => 'required|exists:leave_types,id',
        'from_date'     => 'required|date',
        'to_date'       => 'required|date|after_or_equal:from_date',
        'reason'        => 'required|string|max:1000',

        'is_half_day'   => 'nullable|boolean',
        'half_day_type' => 'required_if:is_half_day,1|in:first_half,second_half',

        'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $userId = Auth::id();

    $staff = Staff::where('user_id', $userId)->first();

    if (!$staff) {
        return redirect()->back()->with('error', 'Staff profile not found for this user.');
    }

    $from = Carbon::parse($request->from_date)->startOfDay();
    $to   = Carbon::parse($request->to_date)->startOfDay();

    $isHalfDay = (bool) $request->is_half_day;

    // Calculate total days
    $totalDays = $isHalfDay ? 0.5 : ($from->diffInDays($to) + 1);

    /**
     * Overlap Check
     */
    $overlapExists = LeaveRequest::where('user_id', $userId)
        ->whereIn('status', ['pending', 'approved'])
        ->where(function ($q) use ($from, $to) {
            $q->whereBetween('from_date', [$from->toDateString(), $to->toDateString()])
              ->orWhereBetween('to_date', [$from->toDateString(), $to->toDateString()])
              ->orWhere(function ($q2) use ($from, $to) {
                  $q2->where('from_date', '<=', $from->toDateString())
                     ->where('to_date', '>=', $to->toDateString());
              });
        })
        ->exists();

    if ($overlapExists) {
        return redirect()->back()->with('error', 'You already have a leave request for this date range.');
    }

    /**
     * Leave Policy Check
     */
    $policy = LeavePolicy::where('leave_type_id', $request->leave_type_id)->first();

    if (!$policy) {
        return redirect()->back()->with('error', 'Leave policy not configured for this leave type. Contact admin.');
    }

    $currentYear = Carbon::now()->year;

    $approvedTaken = LeaveRequest::where('user_id', $userId)
        ->where('leave_type_id', $request->leave_type_id)
        ->where('status', 'approved')
        ->whereYear('from_date', $currentYear)
        ->sum('total_days');

    $remaining = $policy->max_per_year - $approvedTaken;

    if ($totalDays > $remaining) {
        return redirect()->back()->with(
            'error',
            'Leave limit exceeded. Remaining: ' . $remaining . ' day(s) for this leave type.'
        );
    }

    DB::beginTransaction();

    try {

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('leaveappy', 'public');
        }

        LeaveRequest::create([
            'user_id'          => $userId,
            'staff_id'         => $staff->id,
            'leave_type_id'    => $request->leave_type_id,
            'from_date'        => $from->toDateString(),
            'to_date'          => $to->toDateString(),
            'total_days'       => $totalDays,
            'is_half_day'      => $isHalfDay ? 1 : 0,
            'half_day_type'    => $isHalfDay ? $request->half_day_type : null,
            'reason'           => $request->reason,
            'attachment'       => $attachmentPath,
            'status'           => 'pending',
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Leave request submitted successfully!');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    /**
     * Cancel Leave Request (Only Pending)
     */
    public function cancel($id)
    {
        $userId = Auth::id();

        $leave = LeaveRequest::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave requests can be cancelled.');
        }

        // Delete attachment if exists
        if ($leave->attachment && Storage::disk('public')->exists($leave->attachment)) {
            Storage::disk('public')->delete($leave->attachment);
        }

        $leave->delete();

        return redirect()->back()->with('success', 'Leave request cancelled successfully!');
    }

    /**
     * View Leave Attachment (Marketing user can view own attachment)
     */
    public function viewAttachment($id)
    {
        $userId = Auth::id();

        $leave = LeaveRequest::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$leave->attachment || !Storage::disk('public')->exists($leave->attachment)) {
            abort(404, 'Attachment not found.');
        }

        return response()->file(storage_path('app/public/' . $leave->attachment));
    }
}
