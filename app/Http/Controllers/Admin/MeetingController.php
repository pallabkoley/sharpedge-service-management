<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * =========================================
     * ADMIN: LIST ALL MEETINGS
     * =========================================
     */
    public function index()
    {
        $meetings = Meeting::with(['client', 'requester', 'approver'])
            ->latest()
            ->get();

        return view('admin.meetings.index', compact('meetings'));
    }

    /**
     * =========================================
     * ADMIN: APPROVE MEETING
     * =========================================
     */
  public function approve(Meeting $meeting)
{
    if ($meeting->status !== 'pending_approval') {
        return redirect()->back()->with('error', 'Only pending meetings can be approved.');
    }

    $meeting->update([
        'status'      => 'approved',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Meeting approved successfully.');
}

public function reject(Meeting $meeting)
{
    if ($meeting->status !== 'pending_approval') {
        return redirect()->back()->with('error', 'Only pending meetings can be rejected.');
    }

    $meeting->update([
        'status'      => 'rejected',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Meeting rejected successfully.');
}
}