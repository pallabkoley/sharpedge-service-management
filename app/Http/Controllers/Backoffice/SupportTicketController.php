<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Client;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{

/* =====================================================
   LIST ASSIGNED TICKETS
===================================================== */
public function index(Request $request)
{
    $query = SupportTicket::where('assigned_to', auth()->id())
        ->with(['client','order'])
        ->latest();

    // Status filter (your blade already supports it)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $tickets = $query->get();

    // Clients for dropdown
    $clients = Client::select('id','name','email')
        ->orderBy('name')
        ->get();

    return view('Backoffice.support-tickets.index', compact('tickets','clients'));
}


/* =====================================================
   SHOW TICKET (AJAX)
===================================================== */
public function show(SupportTicket $supportTicket)
{
    

    $supportTicket->load([
        'client',
        'order',
        'messages.user'
    ]);

    return response()->json([
        'ticket'     => $supportTicket,
        'messages'   => $supportTicket->messages,
        'time_left'  => $supportTicket->remainingTime(),
        'is_overdue' => $supportTicket->isOverdue()
    ]);
}


/* =====================================================
   CREATE TICKET
===================================================== */
public function store(Request $request)
{
    $request->validate([
        'client_id'   => 'required|exists:clients,id',
        'title'       => 'required|string|max:255',
        'description' => 'required|string'
    ]);

    $ticket = SupportTicket::create([
        'ticket_no'        => SupportTicket::generateTicketNo(),
        'client_id'        => $request->client_id,
        'created_by'       => auth()->id(),
        'created_by_role'  => 'backoffice',
        'assigned_to'      => auth()->id(),
        'title'            => $request->title,
        'description'      => $request->description,
        'ticket_type'      => 'support',
        'status'           => 'in_progress',
        'priority'         => 'medium',
        'sla_hours'        => 24,
        'due_at'           => now()->addHours(24)
    ]);

    SupportTicketMessage::create([
        'support_ticket_id' => $ticket->id,
        'user_id'           => auth()->id(),
        'user_role'         => 'backoffice',
        'message'           => $request->description,
        'is_internal'       => false
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Ticket created successfully',
        'ticket'  => $ticket
    ]);
}


/* =====================================================
   REPLY TO TICKET
===================================================== */
public function reply(Request $request, SupportTicket $supportTicket)
{
   

    $request->validate([
        'message'     => 'required|string',
        'is_internal' => 'nullable|boolean'
    ]);

    SupportTicketMessage::create([
        'support_ticket_id' => $supportTicket->id,
        'user_id'           => auth()->id(),
        'user_role'         => 'backoffice',
        'message'           => $request->message,
        'is_internal'       => $request->boolean('is_internal')
    ]);

    if ($supportTicket->isOpen() || $supportTicket->isAssigned()) {
        $supportTicket->update([
            'status' => 'in_progress'
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Reply sent successfully'
    ]);
}


/* =====================================================
   MARK PENDING
===================================================== */
public function markPending(Request $request, SupportTicket $supportTicket)
{
  

    $request->validate([
        'pending_reason' => 'required|string|max:255'
    ]);

    $supportTicket->update([
        'status'         => 'pending',
        'pending_reason' => $request->pending_reason
    ]);

    return response()->json([
        'success'=>true,
        'message'=>'Ticket marked as pending'
    ]);
}


/* =====================================================
   MARK RESOLVED
===================================================== */
public function markResolved(SupportTicket $supportTicket)
{
   

    $supportTicket->update([
        'status'         => 'resolved',
        'pending_reason' => null
    ]);

    return response()->json([
        'success'=>true,
        'message'=>'Ticket marked as resolved'
    ]);
}

}