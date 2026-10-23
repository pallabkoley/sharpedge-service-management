<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkProcess;
use App\Models\WorkProcessStep;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WorkProcessController extends Controller
{
    /**
     * List all work processes
     */
    public function index()
    {
        $processes = WorkProcess::with([
                'service',
                'creator',
                'steps' => function ($q) {
                    $q->orderBy('step_order');
                }
            ])
            ->latest()
            ->get();

        $services = Service::where('status', 'active')->get();

        return view('admin.work-process.index', compact('processes', 'services'));
    }

    /**
     * Store new work process with steps
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id'                   => 'required|exists:services,id',
            'name'                         => 'required|string|max:255',
            'steps'                        => 'required|array|min:1',
            'steps.*.step_name'            => 'required|string|max:255',
            'steps.*.estimated_days'       => 'nullable|integer|min:0',
            'steps.*.client_instruction'   => 'nullable|string', // ✅ added only
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $process = WorkProcess::create([
                'service_id' => $request->service_id,
                'name'       => $request->name,
                'status'     => 'active',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->steps as $index => $step) {
                WorkProcessStep::create([
                    'work_process_id'   => $process->id,
                    'step_name'         => $step['step_name'],
                    'step_order'        => $index + 1,
                    'estimated_days'    => $step['estimated_days'] ?? null,
                    'client_instruction'=> $step['client_instruction'] ?? null, // ✅ added only
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work process created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Fetch process for EDIT modal
     */
    public function edit($id)
    {
        Log::info('WorkProcessController@edit called for ID: ' . $id);

        try {
            $process = WorkProcess::with([
                    'steps' => function ($q) {
                        $q->orderBy('step_order');
                    }
                ])
                ->find($id);

            if (!$process) {
                Log::warning('Process not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Process not found'
                ], 404);
            }

            Log::info('Process found: ' . $process->name . ' with ' . $process->steps->count() . ' steps');

            return response()->json([
                'success' => true,
                'data'    => $process
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load process data'
            ], 500);
        }
    }

    /**
     * Fetch steps for VIEW modal
     */
    public function steps($id)
    {
        try {
            $process = WorkProcess::with([
                    'steps' => function ($q) {
                        $q->orderBy('step_order');
                    }
                ])
                ->find($id);

            if (!$process) {
                Log::warning('Process not found in steps method: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Process not found'
                ], 404);
            }

            Log::info('Process found in steps method: ' . $process->name . ' with ' . $process->steps->count() . ' steps');

            return response()->json([
                'success'       => true,
                'process_name' => $process->name,
                'steps'        => $process->steps
            ]);
        } catch (\Exception $e) {
            Log::error('Error in steps method: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load steps: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update work process and steps
     */
    public function update(Request $request, $id)
    {
        $process = WorkProcess::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'service_id'                   => 'required|exists:services,id',
            'name'                         => 'required|string|max:255',
            'status'                       => 'required|in:active,inactive',
            'steps'                        => 'required|array|min:1',
            'steps.*.step_name'            => 'required|string|max:255',
            'steps.*.estimated_days'       => 'nullable|integer|min:0',
            'steps.*.client_instruction'   => 'nullable|string', // ✅ added only
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $process->update([
                'service_id' => $request->service_id,
                'name'       => $request->name,
                'status'     => $request->status,
            ]);

            $process->steps()->delete();

            foreach ($request->steps as $index => $step) {
                WorkProcessStep::create([
                    'work_process_id'   => $process->id,
                    'step_name'         => $step['step_name'],
                    'step_order'        => $index + 1,
                    'estimated_days'    => $step['estimated_days'] ?? null,
                    'client_instruction'=> $step['client_instruction'] ?? null, // ✅ added only
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work process updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update process'
            ], 500);
        }
    }

    /**
     * Change process status
     */
    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:work_processes,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $process = WorkProcess::findOrFail($request->id);

            $process->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Delete work process (with steps)
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $process = WorkProcess::findOrFail($id);
                $process->steps()->delete();
                $process->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Work process deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete process'
            ], 500);
        }
    }
}
