<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\ComputationAsset;
use App\Models\ComputationAssetBreakdown;
use App\Models\ComputationInvestment;
use App\Models\ComputationSecurityDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetsReconciliationController extends Controller
{
    /**
     * Display the Assets Reconciliation page.
     */
    public function index($computationId)
    {
        $computation = Computation::with([
            'assets' => function ($query) {
                $query->with('breakdowns')->orderBy('id');
            },
            'investments' => function ($query) {
                $query->orderBy('id');
            },
            'securityDeposits' => function ($query) {
                $query->orderBy('id');
            }
        ])->findOrFail($computationId);

        return view('Backoffice.assets-reconciliation.index', compact('computation'));
    }

    /**
     * Load all data for the given computation (AJAX).
     */
    public function loadData($computationId)
    {
        try {
            $computation = Computation::with([
                'assets' => function ($query) {
                    $query->with('breakdowns')->orderBy('id');
                },
                'investments' => function ($query) {
                    $query->orderBy('id');
                },
                'securityDeposits' => function ($query) {
                    $query->orderBy('id');
                }
            ])->findOrFail($computationId);

            return response()->json([
                'success' => true,
                'assets' => $computation->assets,
                'investments' => $computation->investments,
                'security_deposits' => $computation->securityDeposits,
            ]);
        } catch (\Exception $e) {
            Log::error('Assets Reconciliation Load Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save all data (assets, investments, security deposits) via AJAX.
     */
    public function saveData(Request $request, $computationId)
    {
        try {
            $computation = Computation::findOrFail($computationId);

            $validator = Validator::make($request->all(), [
                // ─── Assets ──────────────────────────────────────
                'assets' => 'nullable|array',
                'assets.*.id' => 'nullable|exists:computation_assets,id',
                'assets.*.asset_name' => 'required|string|max:150',
                'assets.*.block' => 'nullable|string|max:100',   // <-- NEW FIELD
                'assets.*.unit' => 'nullable|integer|min:1',
                'assets.*.opening_wdv' => 'nullable|numeric|min:0',
                'assets.*.purchase_amount' => 'nullable|numeric|min:0',
                'assets.*.sale_amount' => 'nullable|numeric|min:0',
                'assets.*.purchase_date' => 'nullable|date',
                'assets.*.sale_date' => 'nullable|date',
                'assets.*.depreciable_wdv' => 'nullable|numeric|min:0',
                'assets.*.business_use_percentage' => 'nullable|numeric|min:0|max:100',
                'assets.*.depreciation_amount' => 'nullable|numeric|min:0',
                'assets.*.deduction_amount' => 'nullable|numeric|min:0',
                'assets.*.closing_wdv' => 'nullable|numeric|min:0',
                'assets.*.remarks' => 'nullable|string|max:500',

                // ─── Asset Breakdowns ──────────────────────────
                'assets.*.breakdowns' => 'nullable|array',
                'assets.*.breakdowns.*.id' => 'nullable|exists:computation_asset_breakdowns,id',
                'assets.*.breakdowns.*.amount' => 'nullable|numeric|min:0',
                'assets.*.breakdowns.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
                'assets.*.breakdowns.*.depreciation_amount' => 'nullable|numeric|min:0',

                // ─── Investments ───────────────────────────────
                'investments' => 'nullable|array',
                'investments.*.id' => 'nullable|exists:computation_investments,id',
                'investments.*.investment_name' => 'required|string|max:200',
                'investments.*.amount' => 'nullable|numeric|min:0',
                'investments.*.remarks' => 'nullable|string|max:500',

                // ─── Security Deposits ──────────────────────────
                'security_deposits' => 'nullable|array',
                'security_deposits.*.id' => 'nullable|exists:computation_security_deposits,id',
                'security_deposits.*.deposit_name' => 'required|string|max:200',
                'security_deposits.*.amount' => 'nullable|numeric|min:0',
                'security_deposits.*.remarks' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // ─── 1. Process Assets ──────────────────────────────
            $assetIds = [];

            if ($request->has('assets') && is_array($request->assets)) {
                foreach ($request->assets as $assetData) {
                    // Create or update asset
                    if (isset($assetData['id']) && $assetData['id']) {
                        $asset = ComputationAsset::where('id', $assetData['id'])
                            ->where('computation_id', $computationId)
                            ->first();
                        if ($asset) {
                            $asset->update($assetData);
                            $assetIds[] = $asset->id;
                        }
                    } else {
                        // New asset
                        $asset = ComputationAsset::create(array_merge($assetData, [
                            'computation_id' => $computationId
                        ]));
                        $assetIds[] = $asset->id;
                    }

                    // ─── Process Breakdowns for this asset ──────
                    if (isset($assetData['breakdowns']) && is_array($assetData['breakdowns'])) {
                        $breakdownIds = [];
                        foreach ($assetData['breakdowns'] as $breakdownData) {
                            // Ensure the breakdown is linked to the correct asset
                            if (isset($breakdownData['id']) && $breakdownData['id']) {
                                $breakdown = ComputationAssetBreakdown::where('id', $breakdownData['id'])
                                    ->where('computation_asset_id', $asset->id)
                                    ->first();
                                if ($breakdown) {
                                    $breakdown->update($breakdownData);
                                    $breakdownIds[] = $breakdown->id;
                                }
                            } else {
                                // New breakdown
                                $breakdown = ComputationAssetBreakdown::create(array_merge($breakdownData, [
                                    'computation_asset_id' => $asset->id
                                ]));
                                $breakdownIds[] = $breakdown->id;
                            }
                        }

                        // Delete breakdowns that are no longer present
                        $existingBreakdownIds = $asset->breakdowns()->pluck('id')->toArray();
                        $deleteBreakdownIds = array_diff($existingBreakdownIds, $breakdownIds);
                        if (!empty($deleteBreakdownIds)) {
                            ComputationAssetBreakdown::whereIn('id', $deleteBreakdownIds)->delete();
                        }
                    }
                }

                // Delete assets that were not sent
                $existingAssetIds = ComputationAsset::where('computation_id', $computationId)->pluck('id')->toArray();
                $deleteAssetIds = array_diff($existingAssetIds, $assetIds);
                if (!empty($deleteAssetIds)) {
                    ComputationAsset::whereIn('id', $deleteAssetIds)->delete();
                }
            } else {
                // If no assets sent, delete all assets for this computation
                ComputationAsset::where('computation_id', $computationId)->delete();
            }

            // ─── 2. Process Investments ──────────────────────────
            $investmentIds = [];

            if ($request->has('investments') && is_array($request->investments)) {
                foreach ($request->investments as $investmentData) {
                    if (isset($investmentData['id']) && $investmentData['id']) {
                        $investment = ComputationInvestment::where('id', $investmentData['id'])
                            ->where('computation_id', $computationId)
                            ->first();
                        if ($investment) {
                            $investment->update($investmentData);
                            $investmentIds[] = $investment->id;
                        }
                    } else {
                        $investment = ComputationInvestment::create(array_merge($investmentData, [
                            'computation_id' => $computationId
                        ]));
                        $investmentIds[] = $investment->id;
                    }
                }

                $existingInvestmentIds = ComputationInvestment::where('computation_id', $computationId)->pluck('id')->toArray();
                $deleteInvestmentIds = array_diff($existingInvestmentIds, $investmentIds);
                if (!empty($deleteInvestmentIds)) {
                    ComputationInvestment::whereIn('id', $deleteInvestmentIds)->delete();
                }
            } else {
                // No investments sent – delete all
                ComputationInvestment::where('computation_id', $computationId)->delete();
            }

            // ─── 3. Process Security Deposits ────────────────────
            $securityIds = [];

            if ($request->has('security_deposits') && is_array($request->security_deposits)) {
                foreach ($request->security_deposits as $securityData) {
                    if (isset($securityData['id']) && $securityData['id']) {
                        $security = ComputationSecurityDeposit::where('id', $securityData['id'])
                            ->where('computation_id', $computationId)
                            ->first();
                        if ($security) {
                            $security->update($securityData);
                            $securityIds[] = $security->id;
                        }
                    } else {
                        $security = ComputationSecurityDeposit::create(array_merge($securityData, [
                            'computation_id' => $computationId
                        ]));
                        $securityIds[] = $security->id;
                    }
                }

                $existingSecurityIds = ComputationSecurityDeposit::where('computation_id', $computationId)->pluck('id')->toArray();
                $deleteSecurityIds = array_diff($existingSecurityIds, $securityIds);
                if (!empty($deleteSecurityIds)) {
                    ComputationSecurityDeposit::whereIn('id', $deleteSecurityIds)->delete();
                }
            } else {
                // No security deposits sent – delete all
                ComputationSecurityDeposit::where('computation_id', $computationId)->delete();
            }

            DB::commit();

            // ─── Reload fresh data to return ─────────────────────
            $computation->refresh();
            $computation->load(['assets.breakdowns', 'investments', 'securityDeposits']);

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully!',
                'assets' => $computation->assets,
                'investments' => $computation->investments,
                'security_deposits' => $computation->securityDeposits,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assets Reconciliation Save Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single asset (AJAX).
     */
    public function deleteAsset($computationId, $assetId)
    {
        try {
            $asset = ComputationAsset::where('id', $assetId)
                ->where('computation_id', $computationId)
                ->firstOrFail();
            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Asset Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single investment (AJAX).
     */
    public function deleteInvestment($computationId, $investmentId)
    {
        try {
            $investment = ComputationInvestment::where('id', $investmentId)
                ->where('computation_id', $computationId)
                ->firstOrFail();
            $investment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Investment deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Investment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete investment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalize the assets reconciliation report.
     */
    public function finalize($computationId)
    {
        try {
            $computation = Computation::findOrFail($computationId);

            // Update computation status to 'submitted'
            $computation->computation_status = Computation::STATUS_SUBMITTED;
            $computation->submitted_at = now();
            $computation->submitted_by = Auth::id();
            $computation->save();

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Assets Reconciliation Finalize Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to submit report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single security deposit (AJAX).
     */
    public function deleteSecurityDeposit($computationId, $securityId)
    {
        try {
            $security = ComputationSecurityDeposit::where('id', $securityId)
                ->where('computation_id', $computationId)
                ->firstOrFail();
            $security->delete();

            return response()->json([
                'success' => true,
                'message' => 'Security deposit deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Security Deposit Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete security deposit: ' . $e->getMessage()
            ], 500);
        }
    }
}