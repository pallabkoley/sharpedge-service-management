<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Client;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /* =====================================================
       LIST TICKETS (MARKETING DASHBOARD)
    ===================================================== */
    public function index()
    {
        $tickets = SupportTicket::where('created_by', auth()->id())
            ->with(['client', 'order'])
            ->latest()
            ->paginate(10);
// dd($tickets);
        // Needed for CREATE TICKET POPUP
        $clients = Client::where('created_by', auth()->id())->get();

        return view('Marketing.support-tickets.index', compact('tickets', 'clients'));
    }

    /* =====================================================
       CREATE TICKET (MARKETING CAN GENERATE)
    ===================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'ticket_no'        => SupportTicket::generateTicketNo(),
            'client_id'        => $request->client_id,
            'order_id'         => $request->order_id,
            'created_by'       => auth()->id(),
            'created_by_role'  => 'marketing',
            'title'            => $request->title,
            'description'      => $request->description,
            'priority'         => $request->priority,
            'ticket_type'      => 'support',
            'status'           => 'open',
            'sla_hours'        => 24,
            'due_at'           => now()->addHours(24),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support ticket generated successfully',
            'ticket'  => $ticket,
        ]);
    }

    /* =====================================================
       VIEW TICKET + MESSAGES (AJAX POPUP)
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
            'is_overdue' => $supportTicket->isOverdue(),
        ]);
    }

    /* =====================================================
       MARKETING REPLY (CLIENT VISIBLE)
    ===================================================== */
    public function reply(Request $request, SupportTicket $supportTicket)
    {
        if ($supportTicket->assigned_to !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'user_id'           => auth()->id(),
            'user_role'         => 'marketing',
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        if (in_array($supportTicket->status, ['open', 'assigned'])) {
            $supportTicket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
        ]);
    }

    /* =====================================================
       MARK AS PENDING (WAITING FOR CLIENT)
    ===================================================== */
    public function markPending(Request $request, SupportTicket $supportTicket)
    {
        if ($supportTicket->assigned_to !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'pending_reason' => 'required|string|max:255',
        ]);

        $supportTicket->update([
            'status'         => 'pending',
            'pending_reason' => $request->pending_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket marked as pending',
        ]);
    }
}