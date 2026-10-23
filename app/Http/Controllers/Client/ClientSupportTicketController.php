<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientSupportTicketController extends Controller
{
    /**
     * Ticket listing page
     */
    public function index()
    {
        $client = Auth::user()->client;

        $tickets = SupportTicket::where('client_id', $client->id)
            ->with(['order.service'])
            ->latest()
            ->get();

        $orders = Order::where('client_id', $client->id)
            ->latest()
            ->get();

        return view('Client.tickets.index', compact('tickets', 'orders'));
    }

    /**
     * Store new ticket (Client)
     */
    public function store(Request $request)
    {
        $client = Auth::user()->client;

        $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'title'    => 'required|string|max:255',
            'message'  => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        return DB::transaction(function () use ($request, $client) {

            $orderId = null;

            if ($request->filled('order_id')) {
                $order = Order::where('id', $request->order_id)
                    ->where('client_id', $client->id)
                    ->firstOrFail();

                $orderId = $order->id;
            }

            $slaHours = match ($request->priority) {
                'high'   => 24,
                'medium' => 48,
                default  => 72,
            };

            $ticket = SupportTicket::create([
                'ticket_no'       => SupportTicket::generateTicketNo(),
                'client_id'       => $client->id,
                'order_id'        => $orderId,
                'created_by'      => Auth::id(),
                'created_by_role' => 'client',
                'title'           => $request->title,
                'ticket_type'     => $orderId ? 'order_issue' : 'general_support',
                'status'          => 'open',
                'priority'        => $request->priority,
                'sla_hours'       => $slaHours,
                'due_at'          => now()->addHours($slaHours),
            ]);

            // First message
            SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id'           => Auth::id(),
                'user_role'         => 'client',
                'message'           => $request->message,
                'is_internal'       => false,
            ]);

            return response()->json([
                'message' => 'Ticket raised successfully',
                'ticket'  => $ticket,
            ]);
        });
    }

    /**
     * View ticket with messages (Client)
     */
    public function show($id)
    {
        $client = Auth::user()->client;

        $ticket = SupportTicket::with([
                'order',
                'messages.user'
            ])
            ->where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        return response()->json([
            'ticket'     => $ticket,
            'messages'   => $ticket->messages->where('is_internal', false)->values(),
            'time_left'  => $ticket->remainingTime(),
            'is_overdue' => $ticket->isOverdue(),
        ]);
    }

    /**
     * Client reply to ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        
        // dd($ticket);
        $request->validate([
            'message' => 'required|string',
        ]);


        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => Auth::id(),
            'user_role'         => 'client',
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        // Optional: auto-move status back to in_progress
        if (in_array($ticket->status, ['pending', 'resolved'])) {
            $ticket->update([
                'status' => 'in_progress'
            ]);
        }

        return response()->json([
            'message' => 'Reply sent successfully',
        ]);
    }

    /**
     * Client confirms resolution
     */
    public function confirmResolved(SupportTicket $ticket)
    {

        $ticket->update([
            'client_confirmation' => 'accepted',
            'status'              => 'closed',
        ]);

        return response()->json([
            'message' => 'Ticket closed successfully',
        ]);
    }

    /**
     * Client reopens ticket
     */
    public function reopen(Request $request, SupportTicket $ticket)
    {
        
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $ticket->update([
            'client_confirmation' => 'rejected',
            'status'              => 'reopened',
            'pending_reason'      => $request->reason,
        ]);
        

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => Auth::id(),
            'user_role'         => 'client',
            'message'           => 'Ticket reopened: ' . $request->reason,
            'is_internal'       => false,
        ]);

        return response()->json([
            'message' => 'Ticket reopened successfully',
        ]);
    }
}