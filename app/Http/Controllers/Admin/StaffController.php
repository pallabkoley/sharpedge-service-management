<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = Staff::with(['user', 'role'])
            ->when($request->role, function ($q) use ($request) {
                $q->whereHas('role', fn ($r) => $r->where('name', $request->role));
            })
            ->latest()
            ->get();

        $roles = Role::all();

        return view('admin.staff.index', compact('staff', 'roles'));
    }

    /* ===============================
       STORE
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role_id'  => 'required|exists:roles,id',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')
                ->store('Staff/profile_images', 'public');
        }

        $staff = Staff::create([
            'user_id'       => $user->id,
            'role_id'       => $request->role_id,
            'staff_code'    => 'STAFF-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'profile_image' => $profileImage,
            'department'    => $request->department,
            'designation'   => $request->designation,
            'date_joined'   => now(),
            'status'        => 'active',
        ]);

        return back()->with('success', 'Staff created successfully');
    }

    /* ===============================
       UPDATE
    =============================== */
    public function update(Request $request, $id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $staff->user_id,
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required|in:active,inactive,suspended',
        ]);

        // Image update
        if ($request->hasFile('profile_image')) {
            if ($staff->profile_image) {
                Storage::disk('public')->delete($staff->profile_image);
            }

            $staff->profile_image = $request->file('profile_image')
                ->store('Staff/profile_images', 'public');
        }

        // Staff update
        $staff->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'role_id'     => $request->role_id,
            'status'      => $request->status,
            'department'  => $request->department,
            'designation' => $request->designation,
        ]);

        // User sync
        $staff->user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'role_id' => $request->role_id,
            'phone'   => $request->phone,
            'status'  => $request->status,
        ]);

        return back()->with('success', 'Staff updated successfully');
    }

    /* ===============================
       DELETE
    =============================== */
    public function destroy($id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        if ($staff->profile_image) {
            Storage::disk('public')->delete($staff->profile_image);
        }

        $staff->user->delete();
        $staff->delete();

        return back()->with('success', 'Staff deleted successfully');
    }

    /* ===============================
       TOGGLE STATUS
    =============================== */
    public function toggleStatus($id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';

        $staff->update(['status' => $newStatus]);
        $staff->user->update(['status' => $newStatus]);

        return back()->with('success', 'Status updated');
    }

    /* ===============================
       EDIT (AJAX)
    =============================== */
    public function edit($id)
    {
        $staff = Staff::with(['user', 'role'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'staff' => $staff
        ]);
    }
    
    public function show($id)
{
    $staff = Staff::with(['user', 'role'])->findOrFail($id);

    /* =========================
       ðŸ”¹ MARKETING DATA
    ========================== */

    $orders = \App\Models\Order::where('marketing_staff_id', $id)
        ->with(['client', 'service', 'payments'])
        ->latest()
        ->get();

    $totalOrders     = $orders->count();
    $totalRevenue    = $orders->sum('final_amount');
    $totalCollected  = $orders->sum('paid_amount');
    $totalClients    = $orders->pluck('client_id')->unique()->count();

    /* =========================
       ðŸ”¹ TASK DATA (BACK OFFICE)
    ========================== */

    $tasks = \App\Models\Task::where('assigned_to', $id)->latest()->get();

    $totalTasks      = $tasks->count();
    $completedTasks  = $tasks->where('status', 'verified')->count();
    $pendingTasks    = $tasks->whereNotIn('status', ['verified'])->count();

    $overdueTasks = \App\Models\Task::assignedTo($id)
        ->overdue()
        ->count();

    /* =========================
       ðŸ”¹ ATTENDANCE DATA
    ========================== */

    $attendanceLogs = \App\Models\AttendanceLog::where('staff_id', $id)
        ->latest()
        ->get();

    $totalSeconds = $attendanceLogs->sum('total_work_seconds');
    $totalHours   = round($totalSeconds / 3600, 2);

    $totalDays = $attendanceLogs->count();

    $avgHours = $totalDays > 0 
        ? round($totalHours / $totalDays, 2) 
        : 0;

    $todayAttendance = $attendanceLogs
        ->where('date', now()->toDateString())
        ->first();

    /* =========================
       ðŸ”¹ RETURN VIEW
    ========================== */

    return view('admin.staff.show', compact(
        'staff',

        // marketing
        'orders',
        'totalOrders',
        'totalRevenue',
        'totalCollected',
        'totalClients',

        // tasks
        'tasks',
        'totalTasks',
        'completedTasks',
        'pendingTasks',
        'overdueTasks',

        // attendance
        'attendanceLogs',
        'totalHours',
        'totalDays',
        'avgHours',
        'todayAttendance'
    ));
}
}