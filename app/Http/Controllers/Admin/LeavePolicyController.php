<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeavePolicy;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeavePolicyController extends Controller
{
    /**
     * 1) Show Leave Policy Setup Page
     * - Dropdown leave types
     * - Existing policies list (with leave type details using relationship)
     */
    public function index()
    {
        // dropdown list (all leave types)
        $leaveTypes = LeaveType::query()
            ->orderBy('name')
            ->get();

        // policies with leave type (relationship)
        $policies = LeavePolicy::query()
            ->with('leaveType') // relation
            ->orderByDesc('id')
            ->get();

        return view('admin.leave_policies.index', compact('leaveTypes', 'policies'));
    }

    /**
     * 2) Save Policies (Bulk Insert / Update)
     * - Save multiple policy rows at once
     */
    public function store(Request $request)
    {
        $request->validate([
            'policies' => 'required|array|min:1',
            'policies.*.leave_type_id' => 'required|exists:leave_types,id',
            'policies.*.max_per_year' => 'required|integer|min:0|max:365',
            'policies.*.max_per_month' => 'nullable|integer|min:0|max:31',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->policies as $row) {
                LeavePolicy::updateOrCreate(
                    ['leave_type_id' => $row['leave_type_id']],
                    [
                        'max_per_year' => $row['max_per_year'],
                        'max_per_month' => $row['max_per_month'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Leave policies saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * 3) + Button Feature
     * Add new Leave Type from the same page using AJAX
     */
    public function storeLeaveType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:leave_types,code',
        ]);

        $leaveType = LeaveType::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'status' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave type added successfully!',
            'data' => [
                'id' => $leaveType->id,
                'name' => $leaveType->name,
                'code' => $leaveType->code,
            ]
        ]);
    }
}
