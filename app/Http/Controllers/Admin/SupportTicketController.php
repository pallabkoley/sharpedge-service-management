<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /* =====================================================
       LIST ALL TICKETS (ADMIN INDEX PAGE)
    ===================================================== */
    public function index()
    {
        $tickets = SupportTicket::with([
            'client',
            'assignedUser',
            'assignedBy'
        ])->latest()->get();

        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'backoffice', 'marketing']);
        })->get();

        return view('admin.support-tickets.index', compact('tickets', 'staff'));
    }

    /* =====================================================
       CREATE TICKET (ADMIN)
    ===================================================== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'priority'    => 'required|in:low,medium,high',
            'client_id'   => 'nullable|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
            'ticket_type' => 'required|in:client_issue,internal_issue,order_issue,general_support',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $slaHours = match ($request->priority) {
            'high'   => 24,
            'medium' => 48,
            default  => 72,
        };

        $ticket = SupportTicket::create([
            'ticket_no'       => SupportTicket::generateTicketNo(),
            'client_id'       => $request->client_id,
            'created_by'      => auth()->id(),
            'created_by_role' => 'admin',
            'assigned_to'     => $request->assigned_to,
            'assigned_by'     => $request->assigned_to ? auth()->id() : null,
            'title'           => $request->title,
            'priority'        => $request->priority,
            'ticket_type'     => $request->ticket_type,
            'status'          => $request->assigned_to ? 'assigned' : 'open',
            'sla_hours'       => $slaHours,
            'due_at'          => now()->addHours($slaHours),
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => auth()->id(),
            'user_role'         => 'admin',
            'message'           => $request->message,
            'is_internal'       => $request->ticket_type === 'internal_issue',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'ticket'  => $ticket
        ]);
    }

    /* =====================================================
       FETCH TICKET DETAILS (FOR POPUP / MODAL ONLY)
    ===================================================== */
    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load([
            'client',
            'assignedUser',
            'assignedBy',
            'messages.user'
        ]);

        return response()->json([
            'success' => true,
            'ticket'  => $supportTicket,
            'messages'=> $supportTicket->messages
        ]);
    }

    /* =====================================================
       ADMIN REPLY
    ===================================================== */
    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'message'     => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'user_id'           => auth()->id(),
            'user_role'         => auth()->user()->role->name,
            'message'           => $request->message,
            'is_internal'       => $request->boolean('is_internal'),
        ]);

        if ($supportTicket->assigned_to === auth()->id() && $supportTicket->status === 'assigned') {
            $supportTicket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully'
        ]);
    }

    /* =====================================================
       UPDATE TICKET (STATUS / ASSIGN / PRIORITY)
    ===================================================== */
    public function update(Request $request, SupportTicket $supportTicket)
    {
        $validator = Validator::make($request->all(), [
            'status'         => 'sometimes|in:open,assigned,in_progress,pending,resolved,reopened,closed',
            'priority'       => 'sometimes|in:low,medium,high',
            'assigned_to'    => 'sometimes|nullable|exists:users,id',
            'pending_reason' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $role = auth()->user()->role->name;

        /* ASSIGNMENT */
        if ($request->has('assigned_to')) {
            abort_if($role !== 'admin', 403);
            abort_if($supportTicket->status === 'in_progress', 403);

            $supportTicket->update([
                'assigned_to' => $request->assigned_to,
                'assigned_by' => auth()->id(),
                'status'      => 'assigned',
            ]);
        }

        /* STATUS */
        if ($request->filled('status')) {
            if ($request->status === 'closed' && $role !== 'admin') {
                abort(403);
            }

            if (in_array($request->status, ['in_progress','pending','resolved'])) {
                abort_if(
                    $supportTicket->assigned_to !== auth()->id() && $role !== 'admin',
                    403
                );
            }

            $supportTicket->status = $request->status;

            if ($request->status !== 'pending') {
                $supportTicket->pending_reason = null;
            }
        }

        /* PRIORITY */
        if ($request->filled('priority') && $role === 'admin') {
            $slaHours = match ($request->priority) {
                'high'   => 24,
                'medium' => 48,
                default  => 72,
            };

            $supportTicket->priority  = $request->priority;
            $supportTicket->sla_hours = $slaHours;
            $supportTicket->due_at    = now()->addHours($slaHours);
        }

        if ($request->filled('pending_reason')) {
            $supportTicket->pending_reason = $request->pending_reason;
        }

        $supportTicket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully'
        ]);
    }

    /* =====================================================
       DELETE (ADMIN ONLY)
    ===================================================== */
    public function destroy(SupportTicket $ticket)
    {
        abort_if(auth()->user()->role->name !== 'admin', 403);

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }
}