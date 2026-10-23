<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingAvailability;
use App\Models\MeetingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientMeetingController extends Controller
{
    /**
     * Client Meetings Dashboard
     */
public function index()
{
    $user = Auth::user();

    if (!$user->client) {
        abort(403, 'Client profile not found');
    }

    $meetings = Meeting::where('client_id', $user->client->id)
        ->select([
            'id',
            'client_id',
            'purpose',
            'meeting_type',
            'status',
            'final_datetime'
        ])
        ->orderBy('id', 'desc') // 🔥 do NOT use latest()
        ->get()
        ->map(function ($meeting) {
            $meeting->scheduled_at = $meeting->final_datetime;
            return $meeting;
        });

    return view('Client.meetings.index', compact('meetings'));
}

    /**
     * Create meeting
     */
    public function store(Request $request)
    {
        $user = Auth::user();

      

        $request->validate([
            'purpose'      => 'required|string',
            'meeting_type' => 'nullable|string|max:100',
        ]);

        $meeting = Meeting::create([
            'client_id'    => $user->client->id,
            'requested_by' => $user->id,
            'meeting_type' => $request->meeting_type,
            'purpose'      => $request->purpose,
            'status'       => 'requested',
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'meeting_requested_by_client',
            'performed_by' => $user->id,
            'source'       => 'client',
        ]);

        // 👇 make sure scheduled_at exists for JS
        $meeting->scheduled_at = $meeting->final_datetime;

        return response()->json([
            'status'  => true,
            'meeting' => $meeting,
        ]);
    }

    /**
     * Submit availability
     */
    public function submitAvailability(Request $request, Meeting $meeting)
    {
        $user = Auth::user();

       

        $request->validate([
            'slots'              => 'required|array|min:1',
            'slots.*.date'       => 'required|date',
            'slots.*.start_time' => 'required',
            'slots.*.end_time'   => 'required|after:slots.*.start_time',
        ]);

        MeetingAvailability::where('meeting_id', $meeting->id)
            ->where('created_by', $user->id)
            ->delete();

        foreach ($request->slots as $slot) {
            MeetingAvailability::create([
                'meeting_id'     => $meeting->id,
                'available_date' => $slot['date'],
                'start_time'     => $slot['start_time'],
                'end_time'       => $slot['end_time'],
                'created_by'     => $user->id,
            ]);
        }

        $meeting->update([
            'status' => 'availability_provided',
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'client_added_availability',
            'performed_by' => $user->id,
            'source'       => 'client',
        ]);

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * Update meeting
     */
    public function update(Request $request, Meeting $meeting)
    {
        $user = Auth::user();

      

        $request->validate([
            'purpose'      => 'required|string',
            'meeting_type' => 'nullable|string|max:100',
        ]);

        $meeting->update([
            'purpose'      => $request->purpose,
            'meeting_type' => $request->meeting_type,
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'client_updated_meeting',
            'performed_by' => $user->id,
            'source'       => 'client',
        ]);

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * Request reschedule
     */
    public function requestReschedule(Meeting $meeting)
    {
        $user = Auth::user();

      

        $meeting->update([
            'status' => 'reschedule_requested',
        ]);

        MeetingLog::create([
            'meeting_id'   => $meeting->id,
            'action'       => 'client_requested_reschedule',
            'performed_by' => $user->id,
            'source'       => 'client',
        ]);

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * Delete meeting
     */
    public function destroy(Meeting $meeting)
    {
        $user = Auth::user();

       

        $meeting->delete();

        return response()->json([
            'status' => true,
        ]);
    }
}