<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MeetingController extends Controller
{
    /**
     * =========================================
     * INDEX: LIST MARKETING MEETINGS
     * =========================================
     */
public function index()
{
    $loggedInUserId = Auth::id(); // current marketing user

    // ✅ Only meetings created by this marketing user
    $meetings = Meeting::with('client')
        ->where('requested_by', $loggedInUserId)
        ->latest()
        ->get();

    // ✅ Only clients created by this marketing user (for dropdown)
    $clients = Client::where('status', 'active')
        ->where('created_by', $loggedInUserId)
        ->orderBy('name')
        ->get();

    return view('Marketing.meetings.index', compact('meetings', 'clients'));
}

    /**
     * =========================================
     * STORE: MARKETING SCHEDULES MEETING (AJAX)
     * =========================================
     */
    public function store(Request $request)
    {
        // If client_id is empty string or "manual", set to null
        if ($request->client_id === '' || $request->client_id === 'manual' || $request->client_id === null) {
            $request->merge(['client_id' => null]);
        }

        // Custom validation: Either client_id OR manual_client_name must be provided
        $rules = [
            'meeting_type'       => 'required|string|max:100',
            'purpose'            => 'required|string|max:500',
            'meeting_date'       => 'required|date',
            'meeting_time'       => 'required|date_format:H:i',
        ];

        // Add conditional validation for client fields
        if ($request->client_id) {
            // If client_id is provided, validate it exists
            $rules['client_id'] = 'exists:clients,id';
        } else {
            // If no client_id, then manual_client_name is required
            $rules['manual_client_name'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Create the meeting
        $meeting = Meeting::create([
            'client_id'          => $request->client_id,
            'manual_client_name' => $request->client_id ? null : $request->manual_client_name,
            'meeting_type'       => $request->meeting_type,
            'purpose'            => $request->purpose,
            'meeting_date'       => $request->meeting_date,
            'meeting_time'       => $request->meeting_time,
            'requested_by'       => Auth::id(),
            'status'             => 'pending_approval',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Meeting scheduled and sent for admin approval',
            'redirect' => route('marketing.meetings.index'), 
            'data'    => [
                'id'     => $meeting->id,
                'status' => $meeting->status,
            ],
        ]);
    }
}