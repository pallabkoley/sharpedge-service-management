<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BackofficeMeetingController extends Controller
{
    /**
     * =========================================
     * INDEX – SINGLE PAGE (LIST + POPUPS)
     * =========================================
     */
    public function index()
    {
        $meetings = Meeting::with([
                'client',
                'requester',
                'assignee',
                'availabilities'
            ])
            ->latest()
            ->get();

        return view('Backoffice.meetings.index', compact('meetings'));
    }

    /**
     * =========================================
     * VIEW CLIENT AVAILABILITY (AJAX POPUP)
     * =========================================
     */
    public function show(Meeting $meeting)
    {
        return response()->json([
            'status' => true,
            'meeting' => $meeting->load('availabilities', 'client'),
        ]);
    }

    /**
     * =========================================
     * SCHEDULE MEETING (FROM CLIENT AVAILABILITY)
     * =========================================
     */
    public function update(Request $request, Meeting $meeting)
    {
        $request->validate([
            'availability_id' => 'required|exists:meeting_availabilities,id',
        ]);

        $slot = $meeting->availabilities()
            ->where('id', $request->availability_id)
            ->firstOrFail();

        // 🔥 SAFE datetime creation (NO double date bug)
        $finalDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $slot->available_date->format('Y-m-d') . ' ' . $slot->start_time
        );

        $meeting->update([
            'final_datetime' => $finalDateTime,
            'status'         => 'scheduled',
            'assigned_to'    => Auth::id(),
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'meeting_scheduled',
            'performed_by' => Auth::id(),
            'source'       => 'backoffice',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Meeting scheduled successfully',
            'scheduled_at' => $finalDateTime->toDateTimeString(),
        ]);
    }

    /**
     * =========================================
     * MARK MEETING AS COMPLETED
     * =========================================
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->update([
            'status' => 'completed',
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'meeting_completed',
            'performed_by' => Auth::id(),
            'source'       => 'backoffice',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Meeting marked as completed',
        ]);
    }
}