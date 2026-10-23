<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNoticeController extends Controller
{
    /**
     * MAIN VIEW (ONLY RETURN VIEW)
     */
    public function index()
    {
        $notices = Notice::with('creator')
            ->latest()
            ->get();

        $roles = Role::whereIn('name', ['backoffice', 'marketing'])->get();

        return view('admin.notices.index', compact('notices', 'roles'));
    }

    /**
     * STORE NOTICE (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'target_role' => 'required|in:backoffice,marketing,all',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive',
        ]);

        $notice = Notice::create([
            'title'       => $request->title,
            'message'     => $request->message,
            'target_role' => $request->target_role,
            'status'      => $request->status,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice created successfully',
            'notice'  => $notice,
        ]);
    }

    /**
     * FETCH SINGLE NOTICE (AJAX – EDIT MODAL)
     */
    public function show(Notice $notice)
    {
        return response()->json([
            'notice' => $notice
        ]);
    }

    /**
     * UPDATE NOTICE (AJAX)
     */
    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'target_role' => 'required|in:backoffice,marketing,all',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive',
        ]);

        $notice->update([
            'title'       => $request->title,
            'message'     => $request->message,
            'target_role' => $request->target_role,
            'status'      => $request->status,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice updated successfully',
        ]);
    }

    /**
     * TOGGLE STATUS (AJAX)
     */
    public function toggleStatus(Notice $notice)
    {
        $notice->update([
            'status' => $notice->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status'  => $notice->status,
        ]);
    }

    /**
     * DELETE NOTICE (AJAX)
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully',
        ]);
    }
}
