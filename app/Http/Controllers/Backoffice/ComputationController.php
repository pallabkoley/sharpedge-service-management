<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Computation;
use App\Models\ComputationSection;
use App\Models\ComputationHead;
use App\Models\ComputationValue;
use App\Models\ComputationTdsEntry;
use App\Models\ComputationTcsEntry;
use App\Models\ComputationAdvanceTaxEntry;
use App\Models\ComputationBankAccount;
use App\Models\ComputationSalaryEntry;
use App\Models\ComputationMedia;
use App\Models\ComputationExemptIncome;
use App\Models\ComputationHousePropertyEntry;
use App\Models\ComputationCapitalGainEntry;
use App\Models\ComputationVdaEntry;
use App\Models\ComputationOtherSourceEntry;
use App\Models\ComputationDeductionEntry;
use App\Models\ComputationVerification;
use App\Models\ComputationInterestEntry;
use App\Models\ComputationSelfAssessmentTaxEntry;
// ─── NEW IMPORTS ───
use App\Models\ComputationAsset;
use App\Models\ComputationInvestment;
use App\Models\ComputationSecurityDeposit;
use App\Models\TradingAccount;        // ← NEW: Added
use App\Models\TradingAccountRow;     // ← NEW: Added

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class ComputationController extends Controller
{
    // ================================================================
    // FORMULA ENGINE PROPERTIES
    // ================================================================
    private const DEFAULT_COLUMN = 'value';

    /** @var \Illuminate\Support\Collection All heads (manual + formula) */
    private $heads;

    /** @var array code => full Head model */
    private array $headMap = [];

    /** @var array code => ['numeric_value' => float, 'model' => ComputationValue|null, 'is_overridden' => bool, 'calculated' => bool] */
    private array $valueCache = [];

    /** @var array code => [dependency codes] */
    private array $dependencyGraph = [];

    /** @var array Calculation errors */
    private array $errors = [];

    /** @var Computation Current computation being processed */
    private $computation;

    // ================================================================
    // PUBLIC METHODS – CRUD
    // ================================================================

    /**
     * Display a listing of computations.
     */
    public function index(Request $request)
    {
        $query = Computation::with(['client', 'clientType', 'creator', 'verification']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('assessment_year')) {
            $query->where('assessment_year', $request->assessment_year);
        }
        if ($request->filled('computation_status')) {
            $query->where('computation_status', $request->computation_status);
        }

        $computations = $query->latest()->paginate(20);
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $clientTypes = ClientType::where('status', 1)->orderBy('name')->get();

        return view('Backoffice.computation.index', compact('computations', 'clients', 'clientTypes'));
    }

    /**
     * Show the computation sheet (create or edit).
     * GET  -> show form (if id given, load existing; else show blank form)
     * POST -> create new computation
     */
    public function sheet(Request $request, $id = null)
    {
        // ============================================================
        // ১. GET + no id → blank form
        // ============================================================
        if ($request->isMethod('get') && !$id) {
            $clients = Client::where('status', 'active')->orderBy('name')->get();
            $clientTypes = ClientType::where('status', 1)->orderBy('name')->get();

            $computation = new Computation();
            $computation->tdsEntries = collect();
            $computation->tcsEntries = collect();
            $computation->advanceTaxEntries = collect();
            $computation->bankAccounts = collect();
            $computation->salaryEntries = collect();
            $computation->exemptIncomes = collect();
            $computation->media = collect();
            $computation->housePropertyEntries = collect();
            $computation->capitalGainEntries = collect();
            $computation->vdaEntries = collect();
            $computation->otherSourceEntries = collect();
            $computation->deductionEntries = collect();
            $computation->verification = new ComputationVerification();
            $computation->interestEntry = null;
            $computation->selfAssessmentTaxEntries = collect();

            $sections = ComputationSection::with([
                'heads' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'heads.children' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

            $values = collect();

            return view('Backoffice.computation.sheet', compact(
                'computation', 'sections', 'values', 'clients', 'clientTypes'
            ));
        }

        // ============================================================
        // ২. Existing computation (id given)
        // ============================================================
        if ($id) {
            $computation = Computation::with([
                'client',
                'clientType',
                'bankAccounts',
                'salaryEntries',
                'exemptIncomes',
                'media',
                'housePropertyEntries',
                'capitalGainEntries',
                'vdaEntries',
                'otherSourceEntries',
                'deductionEntries',
                'verification',
                'interestEntry',
                'selfAssessmentTaxEntries',
            ])->findOrFail($id);

            $computation->tdsEntries = ComputationTdsEntry::where('computation_id', $id)
                ->orderBy('sort_order')
                ->get();

            $computation->tcsEntries = ComputationTcsEntry::where('computation_id', $id)
                ->orderBy('sort_order')
                ->get();

            $computation->advanceTaxEntries = ComputationAdvanceTaxEntry::where('computation_id', $id)
                ->orderBy('sort_order')
                ->get();

            if (!$computation->verification) {
                $computation->verification = new ComputationVerification(['computation_id' => $id]);
            }
        }
        // ============================================================
        // ৩. New computation (POST)
        // ============================================================
        else {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'assessment_year' => 'required',
                'financial_year' => 'required',
            ]);

            $client = Client::findOrFail($request->client_id);

            $computation = Computation::create([
                'client_id' => $client->id,
                'created_by' => auth()->id(),
                'client_type_id' => $request->client_type_id,
                'assessment_year' => $request->assessment_year,
                'financial_year' => $request->financial_year,
                'previous_year' => $request->previous_year,
                'return_type' => $request->return_type,
                'tax_regime' => $request->tax_regime ?? 'old',
                'filing_status' => $request->filing_status ?? 'original',
                'computation_status' => 'draft',
                'payment_status' => 'unpaid',
                'computation_date' => now(),
            ]);

            // ─── NEW: Insert default rows for Assets, Investments, Security Deposits ───
            // Default Assets
            $defaultAssets = [
                'Land',
                'Pond',
                'Gold',
                'Work In Progress',
                'Office Building',
                'Furniture & Fixtures',
                'Production Machinery',
                'Electrical Fittings',
                'Computers & Servers',
                'Tools & Equipment',
                'Company Vehicles',
                'furniture',
            ];
            foreach ($defaultAssets as $assetName) {
                ComputationAsset::create([
                    'computation_id' => $computation->id,
                    'asset_name'     => $assetName,
                ]);
            }

            // Default Investments
            $defaultInvestments = [
                'Business Investment',
                'Fixed Deposit',
                'Mutual Fund',
                'Life Insurance',
                'Other Deposits',
            ];
            foreach ($defaultInvestments as $investmentName) {
                ComputationInvestment::create([
                    'computation_id'  => $computation->id,
                    'investment_name' => $investmentName,
                ]);
            }

            // Default Security Deposits
            $defaultSecurityDeposits = [
                'Rent Security',
                'Electricity Deposit',
                'Vendor Security',
                'Customer Security',
                'Other Deposits',
            ];
            foreach ($defaultSecurityDeposits as $depositName) {
                ComputationSecurityDeposit::create([
                    'computation_id' => $computation->id,
                    'deposit_name'   => $depositName,
                ]);
            }

            // ─── NEW: Insert Default Trading Account Rows ───
            $tradingAccount = TradingAccount::create([
                'computation_id'  => $computation->id,
                'adjustment_type' => null,
            ]);

            $defaultDebitRows = [
                'Opening Stock',
                'Register Purchases',
                'Unregister Purchases',
                'Purchase Return',
                'Unloading Charges',
                'Wages',
                'Fuel Expenses',
                'Carriage Inward',
                'Sale Platform',
            ];

            foreach ($defaultDebitRows as $index => $particular) {
                TradingAccountRow::create([
                    'trading_account_id' => $tradingAccount->id,
                    'side'               => 'dr',
                    'row_type'           => 'system',
                    'particular_name'    => $particular,
                    'amount'             => 0,
                    'sort_order'         => $index + 1,
                ]);
            }

            $defaultCreditRows = [
                'Sales as per GST',
                'Sales Without GST',
                'Pre Sale Discount',
                'Post Sale Discount',
                'Credit Note',
                'Closing Stock',
            ];

            foreach ($defaultCreditRows as $index => $particular) {
                TradingAccountRow::create([
                    'trading_account_id' => $tradingAccount->id,
                    'side'               => 'cr',
                    'row_type'           => 'system',
                    'particular_name'    => $particular,
                    'amount'             => 0,
                    'sort_order'         => $index + 1,
                ]);
            }

            $computation->tdsEntries = collect();
            $computation->tcsEntries = collect();
            $computation->advanceTaxEntries = collect();
            $computation->bankAccounts = collect();
            $computation->salaryEntries = collect();
            $computation->exemptIncomes = collect();
            $computation->media = collect();
            $computation->housePropertyEntries = collect();
            $computation->capitalGainEntries = collect();
            $computation->vdaEntries = collect();
            $computation->otherSourceEntries = collect();
            $computation->deductionEntries = collect();
            $computation->verification = new ComputationVerification();
            $computation->interestEntry = null;
            $computation->selfAssessmentTaxEntries = collect();
        }

        // ============================================================
        // ৪. Common data load
        // ============================================================
        $sections = ComputationSection::with([
            'heads' => function ($query) {
                $query->orderBy('sort_order');
            },
            'heads.children' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();

        $values = ComputationValue::where('computation_id', $computation->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->head_id . '_' . $item->column_key;
            });

        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $clientTypes = ClientType::where('status', 1)->orderBy('name')->get();

        $businessIncome = 1090000; // Example

        return view('Backoffice.computation.sheet', compact(
            'computation', 'sections', 'values', 'clients', 'clientTypes', 'businessIncome'
        ));
    }

    /**
     * Save partial values (auto-save) – used for spreadsheet cells.
     */
    public function saveValues(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $computation = Computation::findOrFail($id);

            if ($request->has('values')) {
                foreach ($request->values as $headId => $columns) {
                    foreach ($columns as $columnKey => $value) {
                        ComputationValue::updateOrCreate(
                            [
                                'computation_id' => $computation->id,
                                'head_id' => $headId,
                                'column_key' => $columnKey,
                            ],
                            [
                                'value' => $value,
                                'numeric_value' => is_numeric(str_replace(',', '', $value)) ? str_replace(',', '', $value) : null,
                                'formatted_value' => is_numeric(str_replace(',', '', $value)) ? $value : null,
                            ]
                        );
                    }
                }
            }

            if ($request->has('tds_entries')) {
                ComputationTdsEntry::where('computation_id', $id)->delete();
                foreach ($request->tds_entries as $index => $entry) {
                    ComputationTdsEntry::create([
                        'computation_id' => $id,
                        'tan_number' => $entry['tan_number'] ?? null,
                        'deductor_name' => $entry['deductor_name'] ?? null,
                        'section_code' => $entry['section_code'] ?? null,
                        'amount_paid' => str_replace(',', '', $entry['amount_paid'] ?? 0),
                        'tds_deducted' => str_replace(',', '', $entry['tds_deducted'] ?? 0),
                        'tds_claimed' => str_replace(',', '', $entry['tds_claimed'] ?? 0),
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            if ($request->has('tcs_entries')) {
                ComputationTcsEntry::where('computation_id', $id)->delete();
                foreach ($request->tcs_entries as $index => $entry) {
                    ComputationTcsEntry::create([
                        'computation_id' => $id,
                        'tan_number' => $entry['tan_number'] ?? null,
                        'collector_name' => $entry['collector_name'] ?? null,
                        'section_code' => $entry['section_code'] ?? null,
                        'certificate_no' => $entry['certificate_no'] ?? null,
                        'financial_year' => $entry['financial_year'] ?? null,
                        'amount_collected' => str_replace(',', '', $entry['amount_collected'] ?? 0),
                        'tcs_collected' => str_replace(',', '', $entry['tcs_collected'] ?? 0),
                        'tcs_claimed' => str_replace(',', '', $entry['tcs_claimed'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            if ($request->has('advance_tax_entries')) {
                ComputationAdvanceTaxEntry::where('computation_id', $id)->delete();
                foreach ($request->advance_tax_entries as $index => $entry) {
                    ComputationAdvanceTaxEntry::create([
                        'computation_id' => $id,
                        'payment_date' => $entry['payment_date'] ?? null,
                        'bsr_code' => $entry['bsr_code'] ?? null,
                        'challan_no' => $entry['challan_no'] ?? null,
                        'installment' => $entry['installment'] ?? null,
                        'amount_paid' => str_replace(',', '', $entry['amount_paid'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            if ($request->has('bank_accounts')) {
                ComputationBankAccount::where('computation_id', $id)->delete();
                foreach ($request->bank_accounts as $index => $bank) {
                    if (!empty($bank['bank_name']) || !empty($bank['account_number'])) {
                        ComputationBankAccount::create([
                            'computation_id' => $id,
                            'bank_name' => $bank['bank_name'] ?? null,
                            'account_number' => $bank['account_number'] ?? null,
                            'ifsc' => $bank['ifsc'] ?? null,
                            'is_refund_account' => $bank['is_refund_account'] ?? false,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('salary_entries')) {
                ComputationSalaryEntry::where('computation_id', $id)->delete();
                foreach ($request->salary_entries as $index => $salary) {
                    if (is_array($salary) && (!empty($salary['particular']) || !empty($salary['section']) || ($salary['amount'] ?? 0) > 0)) {
                        ComputationSalaryEntry::create([
                            'computation_id' => $id,
                            'particular' => $salary['particular'] ?? null,
                            'section' => $salary['section'] ?? null,
                            'amount' => str_replace(',', '', $salary['amount'] ?? 0),
                            'remarks' => $salary['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('exempt_incomes')) {
                ComputationExemptIncome::where('computation_id', $id)->delete();
                foreach ($request->exempt_incomes as $index => $exempt) {
                    if (is_array($exempt) && (!empty($exempt['particular']) || !empty($exempt['section']) || ($exempt['amount'] ?? 0) > 0)) {
                        ComputationExemptIncome::create([
                            'computation_id' => $id,
                            'particular' => $exempt['particular'] ?? null,
                            'section' => $exempt['section'] ?? null,
                            'amount' => str_replace(',', '', $exempt['amount'] ?? 0),
                            'remarks' => $exempt['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('hp_entries')) {
                ComputationHousePropertyEntry::where('computation_id', $id)->delete();
                foreach ($request->hp_entries as $index => $entry) {
                    if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                        ComputationHousePropertyEntry::create([
                            'computation_id' => $id,
                            'particular' => $entry['particular'] ?? null,
                            'section' => $entry['section'] ?? null,
                            'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                            'remarks' => $entry['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('cg_entries')) {
                ComputationCapitalGainEntry::where('computation_id', $id)->delete();
                foreach ($request->cg_entries as $index => $entry) {
                    if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                        ComputationCapitalGainEntry::create([
                            'computation_id' => $id,
                            'particular' => $entry['particular'] ?? null,
                            'section' => $entry['section'] ?? null,
                            'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                            'remarks' => $entry['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('vda_entries')) {
                ComputationVdaEntry::where('computation_id', $id)->delete();
                foreach ($request->vda_entries as $index => $entry) {
                    if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                        ComputationVdaEntry::create([
                            'computation_id' => $id,
                            'particular' => $entry['particular'] ?? null,
                            'section' => $entry['section'] ?? null,
                            'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                            'remarks' => $entry['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('os_entries')) {
                ComputationOtherSourceEntry::where('computation_id', $id)->delete();
                foreach ($request->os_entries as $index => $entry) {
                    if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                        ComputationOtherSourceEntry::create([
                            'computation_id' => $id,
                            'particular' => $entry['particular'] ?? null,
                            'section' => $entry['section'] ?? null,
                            'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                            'remarks' => $entry['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('ded_entries')) {
                ComputationDeductionEntry::where('computation_id', $id)->delete();
                foreach ($request->ded_entries as $index => $entry) {
                    if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                        ComputationDeductionEntry::create([
                            'computation_id' => $id,
                            'particular' => $entry['particular'] ?? null,
                            'section' => $entry['section'] ?? null,
                            'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                            'remarks' => $entry['remarks'] ?? null,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($request->has('verification')) {
                $verificationData = $request->verification;
                $computation->verification()->updateOrCreate(
                    ['computation_id' => $id],
                    [
                        'prepared_by_name' => $verificationData['prepared_by_name'] ?? null,
                        'prepared_membership_no' => $verificationData['prepared_membership_no'] ?? null,
                        'prepared_firm_name' => $verificationData['prepared_firm_name'] ?? null,
                        'prepared_udin' => $verificationData['prepared_udin'] ?? null,
                        'verified_by_name' => $verificationData['verified_by_name'] ?? null,
                        'verified_place' => $verificationData['verified_place'] ?? null,
                        'verified_date' => $verificationData['verified_date'] ?? null,
                        'verified_seal' => $verificationData['verified_seal'] ?? null,
                        'assessee_name' => $verificationData['assessee_name'] ?? null,
                        'assessee_pan' => $verificationData['assessee_pan'] ?? null,
                        'assessee_place' => $verificationData['assessee_place'] ?? null,
                        'assessee_date' => $verificationData['assessee_date'] ?? null,
                    ]
                );
            }

            if ($request->has('media')) {
                ComputationMedia::where('computation_id', $id)->delete();
                foreach ($request->media as $media) {
                    if (!empty($media['file_path'])) {
                        ComputationMedia::create([
                            'computation_id' => $id,
                            'type' => $media['type'] ?? 'barcode',
                            'file_path' => $media['file_path'],
                        ]);
                    }
                }
            }

            // ── NEW: Interest & Fees and SAT ──
            if ($request->has('tax_paid_data')) {
                $taxData = $request->tax_paid_data;

                if (isset($taxData['interest_234a']) || isset($taxData['interest_234b']) || 
                    isset($taxData['interest_234c']) || isset($taxData['fee_234f'])) {
                    
                    ComputationInterestEntry::updateOrCreate(
                        ['computation_id' => $computation->id],
                        [
                            'interest_234a' => str_replace(',', '', $taxData['interest_234a'] ?? 0),
                            'interest_234b' => str_replace(',', '', $taxData['interest_234b'] ?? 0),
                            'interest_234c' => str_replace(',', '', $taxData['interest_234c'] ?? 0),
                            'fee_234f'      => str_replace(',', '', $taxData['fee_234f'] ?? 0),
                        ]
                    );
                }

                if (isset($taxData['sat_entries']) && is_array($taxData['sat_entries'])) {
                    ComputationSelfAssessmentTaxEntry::where('computation_id', $computation->id)->delete();
                    foreach ($taxData['sat_entries'] as $index => $sat) {
                        if (isset($sat['amount_paid']) && floatval(str_replace(',', '', $sat['amount_paid'])) > 0) {
                            ComputationSelfAssessmentTaxEntry::create([
                                'computation_id' => $computation->id,
                                'payment_date'   => $sat['payment_date'] ?? null,
                                'bsr_code'       => $sat['bsr_code'] ?? null,
                                'challan_no'     => $sat['challan_no'] ?? null,
                                'amount_paid'    => str_replace(',', '', $sat['amount_paid'] ?? 0),
                                'remarks'        => $sat['remarks'] ?? null,
                                'sort_order'     => $index + 1,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Values saved successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SaveValues error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving values: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save full computation (main fields + all entries) with formula validation.
     */
    public function saveComputation(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $input = $request->isJson() ? $request->json()->all() : $request->all();

            Log::info('🔥 saveComputation: Input keys', ['keys' => array_keys($input)]);

            $computation = Computation::findOrFail($id);
            $client = $computation->client;

            $this->updateClientFields($input, $client);
            $this->updateComputationFields($input, $computation);
            $this->saveRelatedData($input, $computation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Computation saved successfully!',
                'computation_id' => $computation->id
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('=== SAVE ERROR ===', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function updateClientFields($input, Client $client)
    {
        $clientFields = [
            'name', 'father_name', 'dob', 'pan', 'aadhaar',
            'residential_status', 'address', 'city', 'state',
            'pincode', 'phone', 'alternate_mobile', 'email',
            'company_name', 'gst_number',
        ];

        $updateData = [];
        foreach ($clientFields as $field) {
            if (array_key_exists($field, $input)) {
                $updateData[$field] = $input[$field];
            }
        }

        if (!empty($updateData)) {
            $client->update($updateData);
        }
    }

    /**
     * Submit computation (finalize) with formula validation.
     */
    public function submitComputation(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $input = $request->isJson() ? $request->json()->all() : $request->all();
            $computation = Computation::findOrFail($id);

            $this->validateFormulasBeforeSave($input, $computation);
            $this->saveRelatedData($input, $computation);

            $computation->update([
                'computation_status' => 'submitted',
                'submitted_at' => now(),
                'submitted_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Computation submitted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SubmitComputation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting computation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete computation and all related entries.
     */
    public function destroy($id)
    {
        $computation = Computation::findOrFail($id);

        ComputationValue::where('computation_id', $id)->delete();
        ComputationTdsEntry::where('computation_id', $id)->delete();
        ComputationTcsEntry::where('computation_id', $id)->delete();
        ComputationAdvanceTaxEntry::where('computation_id', $id)->delete();
        ComputationBankAccount::where('computation_id', $id)->delete();
        ComputationSalaryEntry::where('computation_id', $id)->delete();
        ComputationExemptIncome::where('computation_id', $id)->delete();
        ComputationHousePropertyEntry::where('computation_id', $id)->delete();
        ComputationCapitalGainEntry::where('computation_id', $id)->delete();
        ComputationVdaEntry::where('computation_id', $id)->delete();
        ComputationOtherSourceEntry::where('computation_id', $id)->delete();
        ComputationDeductionEntry::where('computation_id', $id)->delete();
        ComputationVerification::where('computation_id', $id)->delete();
        ComputationMedia::where('computation_id', $id)->delete();
        ComputationInterestEntry::where('computation_id', $id)->delete();
        ComputationSelfAssessmentTaxEntry::where('computation_id', $id)->delete();
        // ─── NEW: Delete the default rows ───
        ComputationAsset::where('computation_id', $id)->delete();
        ComputationInvestment::where('computation_id', $id)->delete();
        ComputationSecurityDeposit::where('computation_id', $id)->delete();
        // ─── NEW: Delete Trading Account rows ───
        $tradingAccount = TradingAccount::where('computation_id', $id)->first();
        if ($tradingAccount) {
            TradingAccountRow::where('trading_account_id', $tradingAccount->id)->delete();
            $tradingAccount->delete();
        }

        $computation->delete();

        return redirect()
            ->route('backoffice.computations.index')
            ->with('success', 'Computation deleted successfully.');
    }

    // ================================================================
    // PRIVATE HELPERS – SAVE LOGIC
    // ================================================================

    private function updateComputationFields($input, $computation)
    {
        $fields = [
            'trade_name',
            'trade_code',
            'income_tax_id',
            'income_tax_password',
            'gmail_password',
            'constitution',
            'assessment_year',
            'financial_year',
            'previous_year',
            'return_type',
            'tax_regime',
            'residential_section',
            'residential_year',
            'filing_status',
            'computation_status',
            'payment_status',
            'payment_mode',
            'payment_date',
            'paid_amount',
            'bsr_no',
            'verified_by',
            'verify_status',
            'verify_success',
            'auditor_name',
            'membership_number',
            'audit_place',
            'audit_date',
            'liable_under_44ab',
            'udin',
            'din_number',
            'itr_acknowledgement_number',
            'audit_acknowledgement_number',
            'ie10_acknowledgement_number',
            'form_under_section',
            'form_no',
            'alternate_mobile',
            'barcode_path',
            'notes',
            'status',
        ];

        $updateData = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $input)) {
                $updateData[$field] = $input[$field];
            }
        }

        if (!empty($updateData)) {
            $computation->update($updateData);
        }
    }

    /**
     * Save all related data: values, TDS, TCS, Advance Tax, Bank Accounts, Salary, Exempt Income, Media.
     * Then run the Formula Engine.
     */
    private function saveRelatedData($input, $computation)
    {
        // ── Head Values ──
        if (isset($input['values']) && is_array($input['values'])) {
            foreach ($input['values'] as $headId => $columns) {
                if (is_array($columns)) {
                    foreach ($columns as $columnKey => $value) {
                        ComputationValue::updateOrCreate(
                            [
                                'computation_id' => $computation->id,
                                'head_id' => $headId,
                                'column_key' => $columnKey,
                            ],
                            [
                                'value' => $value,
                                'numeric_value' => is_numeric(str_replace(',', '', $value)) ? str_replace(',', '', $value) : null,
                                'formatted_value' => $value,
                                'is_overridden' => true,
                            ]
                        );
                    }
                }
            }
        }

        // ── TDS Entries ──
        if (isset($input['tds_entries']) && is_array($input['tds_entries'])) {
            ComputationTdsEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['tds_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['tan_number']) || !empty($entry['deductor_name']) ||
                    ($entry['amount_paid'] ?? 0) > 0 || ($entry['tds_deducted'] ?? 0) > 0)) {
                    ComputationTdsEntry::create([
                        'computation_id' => $computation->id,
                        'tan_number' => $entry['tan_number'] ?? null,
                        'deductor_name' => $entry['deductor_name'] ?? null,
                        'section_code' => $entry['section_code'] ?? null,
                        'amount_paid' => str_replace(',', '', $entry['amount_paid'] ?? 0),
                        'tds_deducted' => str_replace(',', '', $entry['tds_deducted'] ?? 0),
                        'tds_claimed' => str_replace(',', '', $entry['tds_claimed'] ?? 0),
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── TCS Entries ──
        if (isset($input['tcs_entries']) && is_array($input['tcs_entries'])) {
            ComputationTcsEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['tcs_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['tan_number']) || !empty($entry['collector_name']) ||
                    ($entry['amount_collected'] ?? 0) > 0 || ($entry['tcs_collected'] ?? 0) > 0)) {
                    ComputationTcsEntry::create([
                        'computation_id' => $computation->id,
                        'tan_number' => $entry['tan_number'] ?? null,
                        'collector_name' => $entry['collector_name'] ?? null,
                        'section_code' => $entry['section_code'] ?? null,
                        'certificate_no' => $entry['certificate_no'] ?? null,
                        'financial_year' => $entry['financial_year'] ?? null,
                        'amount_collected' => str_replace(',', '', $entry['amount_collected'] ?? 0),
                        'tcs_collected' => str_replace(',', '', $entry['tcs_collected'] ?? 0),
                        'tcs_claimed' => str_replace(',', '', $entry['tcs_claimed'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Advance Tax Entries ──
        if (isset($input['advance_tax_entries']) && is_array($input['advance_tax_entries'])) {
            ComputationAdvanceTaxEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['advance_tax_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['payment_date']) || !empty($entry['bsr_code']) ||
                    !empty($entry['challan_no']) || ($entry['amount_paid'] ?? 0) > 0)) {
                    ComputationAdvanceTaxEntry::create([
                        'computation_id' => $computation->id,
                        'payment_date' => $entry['payment_date'] ?? null,
                        'bsr_code' => $entry['bsr_code'] ?? null,
                        'challan_no' => $entry['challan_no'] ?? null,
                        'installment' => $entry['installment'] ?? null,
                        'amount_paid' => str_replace(',', '', $entry['amount_paid'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Bank Accounts ──
        if (isset($input['bank_accounts']) && is_array($input['bank_accounts'])) {
            ComputationBankAccount::where('computation_id', $computation->id)->delete();
            foreach ($input['bank_accounts'] as $index => $bank) {
                if (is_array($bank) && (!empty($bank['bank_name']) || !empty($bank['account_number']))) {
                    ComputationBankAccount::create([
                        'computation_id' => $computation->id,
                        'bank_name' => $bank['bank_name'] ?? null,
                        'account_number' => $bank['account_number'] ?? null,
                        'ifsc' => $bank['ifsc'] ?? null,
                        'is_refund_account' => $bank['is_refund_account'] ?? false,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Salary Entries ──
        if (isset($input['salary_entries']) && is_array($input['salary_entries'])) {
            ComputationSalaryEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['salary_entries'] as $index => $salary) {
                if (is_array($salary) && (!empty($salary['particular']) || !empty($salary['section']) || ($salary['amount'] ?? 0) > 0)) {
                    ComputationSalaryEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $salary['particular'] ?? null,
                        'section' => $salary['section'] ?? null,
                        'amount' => str_replace(',', '', $salary['amount'] ?? 0),
                        'remarks' => $salary['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Exempt Income Entries ──
        if (isset($input['exempt_incomes']) && is_array($input['exempt_incomes'])) {
            ComputationExemptIncome::where('computation_id', $computation->id)->delete();
            foreach ($input['exempt_incomes'] as $index => $exempt) {
                if (is_array($exempt) && (!empty($exempt['particular']) || !empty($exempt['section']) || ($exempt['amount'] ?? 0) > 0)) {
                    ComputationExemptIncome::create([
                        'computation_id' => $computation->id,
                        'particular' => $exempt['particular'] ?? null,
                        'section' => $exempt['section'] ?? null,
                        'amount' => str_replace(',', '', $exempt['amount'] ?? 0),
                        'remarks' => $exempt['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── House Property Entries ──
        if (isset($input['hp_entries']) && is_array($input['hp_entries'])) {
            ComputationHousePropertyEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['hp_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                    ComputationHousePropertyEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $entry['particular'] ?? null,
                        'section' => $entry['section'] ?? null,
                        'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Capital Gain Entries ──
        if (isset($input['cg_entries']) && is_array($input['cg_entries'])) {
            ComputationCapitalGainEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['cg_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                    ComputationCapitalGainEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $entry['particular'] ?? null,
                        'section' => $entry['section'] ?? null,
                        'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── VDA Entries ──
        if (isset($input['vda_entries']) && is_array($input['vda_entries'])) {
            ComputationVdaEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['vda_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                    ComputationVdaEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $entry['particular'] ?? null,
                        'section' => $entry['section'] ?? null,
                        'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Other Source Entries ──
        if (isset($input['os_entries']) && is_array($input['os_entries'])) {
            ComputationOtherSourceEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['os_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                    ComputationOtherSourceEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $entry['particular'] ?? null,
                        'section' => $entry['section'] ?? null,
                        'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ── Deduction Entries ──
        if (isset($input['ded_entries']) && is_array($input['ded_entries'])) {
            ComputationDeductionEntry::where('computation_id', $computation->id)->delete();
            foreach ($input['ded_entries'] as $index => $entry) {
                if (is_array($entry) && (!empty($entry['particular']) || !empty($entry['section']) || ($entry['amount'] ?? 0) > 0)) {
                    ComputationDeductionEntry::create([
                        'computation_id' => $computation->id,
                        'particular' => $entry['particular'] ?? null,
                        'section' => $entry['section'] ?? null,
                        'amount' => str_replace(',', '', $entry['amount'] ?? 0),
                        'remarks' => $entry['remarks'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // ====== Save Verification Data ======
        if (isset($input['verification']) && is_array($input['verification'])) {
            $v = $input['verification'];
            $computation->verification()->updateOrCreate(
                ['computation_id' => $computation->id],
                [
                    'prepared_by_name' => $v['prepared_by_name'] ?? null,
                    'prepared_membership_no' => $v['prepared_membership_no'] ?? null,
                    'prepared_firm_name' => $v['prepared_firm_name'] ?? null,
                    'prepared_udin' => $v['prepared_udin'] ?? null,
                    'verified_by_name' => $v['verified_by_name'] ?? null,
                    'verified_place' => $v['verified_place'] ?? null,
                    'verified_date' => $v['verified_date'] ?? null,
                    'verified_seal' => $v['verified_seal'] ?? null,
                    'assessee_name' => $v['assessee_name'] ?? null,
                    'assessee_pan' => $v['assessee_pan'] ?? null,
                    'assessee_place' => $v['assessee_place'] ?? null,
                    'assessee_date' => $v['assessee_date'] ?? null,
                ]
            );
        }

        // ── Media ── (no sort_order)
        if (isset($input['media']) && is_array($input['media'])) {
            ComputationMedia::where('computation_id', $computation->id)->delete();
            foreach ($input['media'] as $media) {
                if (is_array($media) && !empty($media['file_path'])) {
                    ComputationMedia::create([
                        'computation_id' => $computation->id,
                        'type' => $media['type'] ?? 'barcode',
                        'file_path' => $media['file_path'],
                    ]);
                }
            }
        }

        // ── NEW: Interest & Fees and SAT from tax_paid_data ──
        if (isset($input['tax_paid_data']) && is_array($input['tax_paid_data'])) {
            $taxData = $input['tax_paid_data'];

            Log::info('🔥 tax_paid_data received', ['taxData' => $taxData]);

            // Save Interest & Fees (single record)
            if (isset($taxData['interest_234a']) || isset($taxData['interest_234b']) || 
                isset($taxData['interest_234c']) || isset($taxData['fee_234f'])) {
                
                ComputationInterestEntry::updateOrCreate(
                    ['computation_id' => $computation->id],
                    [
                        'interest_234a' => str_replace(',', '', $taxData['interest_234a'] ?? 0),
                        'interest_234b' => str_replace(',', '', $taxData['interest_234b'] ?? 0),
                        'interest_234c' => str_replace(',', '', $taxData['interest_234c'] ?? 0),
                        'fee_234f'      => str_replace(',', '', $taxData['fee_234f'] ?? 0),
                    ]
                );
            }

            // Save Self Assessment Tax (SAT) entries
            if (isset($taxData['sat_entries']) && is_array($taxData['sat_entries'])) {
                ComputationSelfAssessmentTaxEntry::where('computation_id', $computation->id)->delete();
                foreach ($taxData['sat_entries'] as $index => $sat) {
                    if (isset($sat['amount_paid']) && floatval(str_replace(',', '', $sat['amount_paid'])) > 0) {
                        ComputationSelfAssessmentTaxEntry::create([
                            'computation_id' => $computation->id,
                            'payment_date'   => $sat['payment_date'] ?? null,
                            'bsr_code'       => $sat['bsr_code'] ?? null,
                            'challan_no'     => $sat['challan_no'] ?? null,
                            'amount_paid'    => str_replace(',', '', $sat['amount_paid'] ?? 0),
                            'remarks'        => $sat['remarks'] ?? null,
                            'sort_order'     => $index + 1,
                        ]);
                    }
                }
            }

            // ── 🔥 NEW: Update ComputationValue heads with Tax Paid totals ──
            $interestTotal = ($taxData['interest_234a'] ?? 0) + ($taxData['interest_234b'] ?? 0)
                           + ($taxData['interest_234c'] ?? 0) + ($taxData['fee_234f'] ?? 0);

            $tdsTotal = 0;
            foreach ($taxData['tds_entries'] ?? [] as $entry) {
                $tdsTotal += (float) str_replace(',', '', $entry['tds_claimed'] ?? 0);
            }

            $tcsTotal = 0;
            foreach ($taxData['tcs_entries'] ?? [] as $entry) {
                $tcsTotal += (float) str_replace(',', '', $entry['tcs_claimed'] ?? 0);
            }

            $advanceTotal = 0;
            foreach ($taxData['advance_tax_entries'] ?? [] as $entry) {
                $advanceTotal += (float) str_replace(',', '', $entry['amount_paid'] ?? 0);
            }

            $satTotal = 0;
            foreach ($taxData['sat_entries'] ?? [] as $entry) {
                $satTotal += (float) str_replace(',', '', $entry['amount_paid'] ?? 0);
            }

            $grandTotal = $interestTotal + $tdsTotal + $tcsTotal + $advanceTotal + $satTotal;

            $headValues = [
                'interest_fees_total' => $interestTotal,
                'tds_claimed_total'   => $tdsTotal,
                'tcs_claimed_total'   => $tcsTotal,
                'advance_tax_total'   => $advanceTotal,
                'sat_total'           => $satTotal,
                'total_tax_paid'      => $grandTotal,
            ];

            Log::info('🔥 Head values to update', ['headValues' => $headValues]);

            $heads = ComputationHead::whereIn('code', array_keys($headValues))->get()->keyBy('code');

            foreach ($headValues as $code => $value) {
                if ($head = $heads->get($code)) {
                    ComputationValue::updateOrCreate(
                        [
                            'computation_id' => $computation->id,
                            'head_id'        => $head->id,
                            'column_key'     => self::DEFAULT_COLUMN,
                        ],
                        [
                            'numeric_value'   => $value,
                            'formatted_value' => number_format($value, 2),
                            'value'           => (string) $value,
                            'is_overridden'   => false,
                        ]
                    );
                    Log::info("✅ Updated head: {$code} = {$value}");
                } else {
                    Log::warning("⚠️ Head not found: {$code}");
                }
            }
        } else {
            Log::warning('⚠️ tax_paid_data not found in input or not an array');
        }

        // ── 🔥 FORMULA ENGINE: Auto‑calculate formula heads ──
        $this->initializeCalculationEngine($computation);
        $this->buildDependencyGraph();

        if (!$this->ensureOverrideColumnExists()) {
            Log::warning('is_overridden column missing in computation_values table. Auto-calculation may not persist.');
        }

        if (!$this->hasCalculationErrors()) {
            $this->calculateAll();
            $this->persistCalculatedValues();
        } else {
            Log::warning('Formula engine errors: ' . implode(', ', $this->errors));
        }
    }

    // ================================================================
    // 🔥 FORMULA VALIDATION (Pre-save)
    // ================================================================

    private function validateFormulasBeforeSave($input, $computation)
    {
        $headIdsInInput = [];
        if (isset($input['values']) && is_array($input['values'])) {
            foreach (array_keys($input['values']) as $headId) {
                $headIdsInInput[] = (int) $headId;
            }
        }

        if (empty($headIdsInInput)) {
            return;
        }

        $heads = ComputationHead::whereIn('id', $headIdsInInput)
            ->where('status', 1)
            ->get()
            ->keyBy('id');

        $allHeads = ComputationHead::where('status', 1)->get()->keyBy('code');

        foreach ($heads as $head) {
            if (empty($head->formula)) {
                continue;
            }

            $deps = $this->extractDependencies($head->formula);
            foreach ($deps as $depCode) {
                if (!isset($allHeads[$depCode])) {
                    throw new \Exception("Unknown head '{$depCode}' in formula for '{$head->code}'");
                }
            }

            $graph = [];
            $graph[$head->code] = $deps;
            foreach ($deps as $depCode) {
                if (isset($allHeads[$depCode]) && !empty($allHeads[$depCode]->formula)) {
                    $graph[$depCode] = $this->extractDependencies($allHeads[$depCode]->formula);
                }
            }
            if ($this->detectCircularReferenceInGraph($head->code, $graph)) {
                throw new \Exception("Circular reference detected in formula for '{$head->code}'");
            }

            try {
                $this->parseExpression($head->formula);
            } catch (\Throwable $e) {
                throw new \Exception("Syntax error in formula for '{$head->code}': " . $e->getMessage());
            }
        }
    }

    private function detectCircularReferenceInGraph(string $startCode, array $graph, array $stack = []): bool
    {
        if (in_array($startCode, $stack)) {
            return true;
        }
        $stack[] = $startCode;
        foreach ($graph[$startCode] ?? [] as $depCode) {
            if ($this->detectCircularReferenceInGraph($depCode, $graph, $stack)) {
                return true;
            }
        }
        return false;
    }

    // ================================================================
    // 🔥 FORMULA ENGINE – PRIVATE METHODS
    // ================================================================

    private function initializeCalculationEngine(Computation $computation): void
    {
        $this->computation = $computation;
        $this->errors = [];
        $this->headMap = [];
        $this->valueCache = [];
        $this->dependencyGraph = [];

        $this->heads = ComputationHead::where('status', 1)
            ->orderBy('sort_order')
            ->get();

        if ($this->heads->isEmpty()) {
            return;
        }

        foreach ($this->heads as $head) {
            $this->headMap[$head->code] = $head;
        }

        $existingValues = ComputationValue::where('computation_id', $this->computation->id)
            ->where('column_key', self::DEFAULT_COLUMN)
            ->get()
            ->keyBy('head_id');

        foreach ($this->heads as $head) {
            $model = $existingValues->get($head->id);
            $this->valueCache[$head->code] = [
                'numeric_value' => $model ? (float) $model->numeric_value : 0.0,
                'model' => $model,
                'is_overridden' => $model ? (bool) $model->is_overridden : false,
                'calculated' => false,
            ];
        }
    }

    private function buildDependencyGraph(): void
    {
        foreach ($this->heads as $head) {
            if (empty($head->formula)) {
                $this->dependencyGraph[$head->code] = [];
                continue;
            }
            $this->dependencyGraph[$head->code] = $this->extractDependencies($head->formula);
        }
    }

    private function extractDependencies(string $formula): array
    {
        preg_match_all('/[A-Z][A-Z0-9_]*/', $formula, $matches);
        $codes = array_unique($matches[0] ?? []);
        $functions = ['SUM', 'MIN', 'MAX', 'ABS', 'ROUND', 'IF', 'AVG', 'PERCENT', 'CEIL', 'FLOOR'];
        return array_filter($codes, function ($code) use ($functions) {
            return !in_array($code, $functions);
        });
    }

    private function ensureOverrideColumnExists(): bool
    {
        return Schema::hasColumn('computation_values', 'is_overridden');
    }

    private function calculateAll(): void
    {
        if ($this->hasCalculationErrors()) {
            return;
        }

        foreach (array_keys($this->valueCache) as $code) {
            $this->valueCache[$code]['calculated'] = false;
        }

        foreach ($this->heads as $head) {
            if (!empty($head->formula) && !$this->valueCache[$head->code]['calculated']) {
                $this->calculateHead($head->code);
            }
        }
    }

    private function calculateHead(string $code): void
    {
        if ($this->valueCache[$code]['calculated'] ?? false) {
            return;
        }

        if ($this->valueCache[$code]['is_overridden'] ?? false) {
            $this->valueCache[$code]['calculated'] = true;
            return;
        }

        $head = $this->headMap[$code] ?? null;
        if (!$head || empty($head->formula)) {
            $this->valueCache[$code]['calculated'] = true;
            return;
        }

        foreach ($this->dependencyGraph[$code] ?? [] as $depCode) {
            if (!$this->valueCache[$depCode]['calculated'] ?? false) {
                $this->calculateHead($depCode);
            }
        }

        $result = $this->evaluateFormula($head->formula, $code);

        if ($result !== null) {
            $this->valueCache[$code]['numeric_value'] = $result;
        }

        $this->valueCache[$code]['calculated'] = true;
    }

    private function evaluateFormula(string $formula, string $headCode): ?float
    {
        try {
            $expression = $formula;
            $dependencies = $this->extractDependencies($expression);

            foreach ($dependencies as $depCode) {
                if (isset($this->valueCache[$depCode])) {
                    $value = $this->valueCache[$depCode]['numeric_value'];
                    $expression = preg_replace('/\b' . preg_quote($depCode, '/') . '\b/', (string) $value, $expression);
                }
            }

            $result = $this->parseExpression($expression);

            if (is_numeric($result)) {
                return (float) $result;
            }

            $this->addCalculationError("Formula for '{$headCode}' returned non-numeric: {$result}");
            return null;
        } catch (\Throwable $e) {
            $this->addCalculationError("Error evaluating '{$headCode}': " . $e->getMessage());
            return null;
        }
    }

    // ================================================================
    // 🔥 SHUNTING-YARD PARSER (No eval!)
    // ================================================================

    private function parseExpression(string $expr)
    {
        $expr = trim($expr);

        if (preg_match('/^([A-Z]+)\((.*)\)$/', $expr, $matches)) {
            $funcName = strtolower($matches[1]);
            $argsString = $matches[2];
            $args = $this->parseArguments($argsString);
            $evaluatedArgs = array_map([$this, 'parseExpression'], $args);
            return $this->callFunction($funcName, $evaluatedArgs);
        }

        if (str_starts_with($expr, '(') && str_ends_with($expr, ')')) {
            return $this->parseExpression(substr($expr, 1, -1));
        }

        $tokens = $this->tokenize($expr);
        return $this->evaluateTokens($tokens);
    }

    private function tokenize(string $expr): array
    {
        $tokens = [];
        $current = '';
        $len = strlen($expr);

        for ($i = 0; $i < $len; $i++) {
            $char = $expr[$i];

            if ($char === ' ') {
                continue;
            }

            if (is_numeric($char) || $char === '.') {
                $current .= $char;
            } elseif (in_array($char, ['+', '-', '*', '/', '(', ')', ','])) {
                if ($current !== '') {
                    $tokens[] = $current;
                    $current = '';
                }
                $tokens[] = $char;
            } else {
                if ($current !== '') {
                    $tokens[] = $current;
                    $current = '';
                }
                $current .= $char;
            }
        }

        if ($current !== '') {
            $tokens[] = $current;
        }

        return $tokens;
    }

    private function evaluateTokens(array $tokens)
    {
        $outputQueue = [];
        $operatorStack = [];

        $precedence = [
            '+' => 1,
            '-' => 1,
            '*' => 2,
            '/' => 2,
        ];

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $outputQueue[] = (float) $token;
            } elseif (in_array($token, ['+', '-', '*', '/'])) {
                while (!empty($operatorStack) && in_array(end($operatorStack), ['+', '-', '*', '/']) &&
                    $precedence[end($operatorStack)] >= $precedence[$token]) {
                    $outputQueue[] = array_pop($operatorStack);
                }
                $operatorStack[] = $token;
            } elseif ($token === '(') {
                $operatorStack[] = $token;
            } elseif ($token === ')') {
                while (!empty($operatorStack) && end($operatorStack) !== '(') {
                    $outputQueue[] = array_pop($operatorStack);
                }
                array_pop($operatorStack);
            } elseif ($token === ',') {
                while (!empty($operatorStack) && end($operatorStack) !== '(') {
                    $outputQueue[] = array_pop($operatorStack);
                }
            } else {
                if (isset($this->valueCache[$token])) {
                    $outputQueue[] = $this->valueCache[$token]['numeric_value'];
                } else {
                    throw new \Exception("Unknown variable: {$token}");
                }
            }
        }

        while (!empty($operatorStack)) {
            $outputQueue[] = array_pop($operatorStack);
        }

        return $this->evaluateRPN($outputQueue);
    }

    private function evaluateRPN(array $tokens)
    {
        $stack = [];

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $stack[] = $token;
            } elseif (in_array($token, ['+', '-', '*', '/'])) {
                if (count($stack) < 2) {
                    throw new \Exception("Not enough operands for operator: {$token}");
                }
                $b = array_pop($stack);
                $a = array_pop($stack);
                switch ($token) {
                    case '+': $stack[] = $a + $b; break;
                    case '-': $stack[] = $a - $b; break;
                    case '*': $stack[] = $a * $b; break;
                    case '/':
                        if ($b == 0) {
                            throw new \Exception("Division by zero");
                        }
                        $stack[] = $a / $b;
                        break;
                }
            } else {
                throw new \Exception("Unexpected token in RPN: {$token}");
            }
        }

        if (count($stack) !== 1) {
            throw new \Exception("Invalid expression: stack has " . count($stack) . " elements");
        }

        return $stack[0];
    }

    private function parseArguments(string $argsString): array
    {
        $args = [];
        $current = '';
        $depth = 0;
        $len = strlen($argsString);

        for ($i = 0; $i < $len; $i++) {
            $char = $argsString[$i];
            if ($char === '(') {
                $depth++;
                $current .= $char;
            } elseif ($char === ')') {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                $args[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $args[] = trim($current);
        }

        return $args;
    }

    private function callFunction(string $name, array $args)
    {
        switch ($name) {
            case 'sum': return array_sum($args);
            case 'min': return min($args);
            case 'max': return max($args);
            case 'abs': return abs($args[0] ?? 0);
            case 'round': $precision = (int) ($args[1] ?? 0); return round($args[0] ?? 0, $precision);
            case 'if': return (bool) ($args[0] ?? false) ? ($args[1] ?? 0) : ($args[2] ?? 0);
            case 'avg': return count($args) > 0 ? array_sum($args) / count($args) : 0;
            case 'percent': $value = $args[0] ?? 0; $total = $args[1] ?? 1; return ($total != 0) ? ($value / $total * 100) : 0;
            case 'ceil': return ceil($args[0] ?? 0);
            case 'floor': return floor($args[0] ?? 0);
            default: throw new \Exception("Unsupported function: {$name}");
        }
    }

    private function persistCalculatedValues(): void
    {
        if ($this->hasCalculationErrors()) {
            return;
        }

        foreach ($this->heads as $head) {
            if (empty($head->formula)) {
                continue;
            }

            $code = $head->code;
            $cache = $this->valueCache[$code] ?? null;

            if (!$cache || ($cache['is_overridden'] ?? false)) {
                continue;
            }

            $value = $cache['numeric_value'] ?? null;
            if ($value === null) {
                continue;
            }

            $decimalPlaces = (int) ($head->decimal_places ?? 2);
            $roundedValue = round($value, $decimalPlaces);

            $model = $cache['model'] ?? null;
            if ($model) {
                $model->numeric_value = $roundedValue;
                $model->formatted_value = number_format($roundedValue, $decimalPlaces);
                $model->value = (string) $roundedValue;
                $model->is_overridden = false;
                $model->save();
            } else {
                ComputationValue::create([
                    'computation_id' => $this->computation->id,
                    'head_id' => $head->id,
                    'column_key' => self::DEFAULT_COLUMN,
                    'numeric_value' => $roundedValue,
                    'formatted_value' => number_format($roundedValue, $decimalPlaces),
                    'value' => (string) $roundedValue,
                    'is_overridden' => false,
                ]);
            }
        }
    }

    // ================================================================
    // MEDIA UPLOAD / REMOVE
    // ================================================================

    public function uploadMedia(Request $request, $id)
    {
        $request->validate([
            'media' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'type'  => 'required|in:barcode,qrcode',
        ]);

        $computation = Computation::findOrFail($id);

        $media = ComputationMedia::where('computation_id', $computation->id)
            ->where('type', $request->type)
            ->first();

        $folder = $request->type == 'barcode'
            ? 'Computation_Barcode'
            : 'Computation_QRCode';

        $path = $request->file('media')->store($folder, 'public');

        if ($media) {
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }
            $media->update(['file_path' => $path]);
        } else {
            ComputationMedia::create([
                'computation_id' => $computation->id,
                'type'           => $request->type,
                'file_path'      => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type).' uploaded successfully.',
            'url'     => asset('storage/'.$path),
        ]);
    }

    public function removeMedia(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:barcode,qrcode',
        ]);

        $media = ComputationMedia::where('computation_id', $id)
            ->where('type', $request->type)
            ->first();

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found.'
            ]);
        }

        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media removed successfully.'
        ]);
    }

    // ================================================================
    // PDF GENERATION
    // ================================================================

    /**
     * Generate and download PDF of the computation.
     */
    public function downloadPdf($id)
    {
        // Load computation with all required relations
        $computation = Computation::with([
            'client',
            'tdsEntries',
            'tcsEntries',
            'advanceTaxEntries',
            'salaryEntries',
            'housePropertyEntries',
            'capitalGainEntries',
            'vdaEntries',
            'otherSourceEntries',
            'deductionEntries',
            'verification',
            'interestEntry',
            'selfAssessmentTaxEntries',
        ])->findOrFail($id);

        // Compute income summary totals (reuse your JavaScript logic in PHP if needed)
        // For simplicity, we'll compute the same values that appear in the sheet.
        // You can also pass data from the sheet's hidden inputs, but it's safer to compute here.

        $gti = $computation->salaryEntries->sum('amount')
            + $computation->housePropertyEntries->sum('amount')
            + 1090000 // Business income placeholder (you can fetch from actual model)
            + $computation->capitalGainEntries->sum('amount')
            + $computation->vdaEntries->sum('amount')
            + $computation->otherSourceEntries->sum('amount');

        $deductions = $computation->deductionEntries->sum('amount');
        $tti = max(0, $gti - $deductions);

        // Tax computation (simplified – you can expand as per your actual JS logic)
        $tax_stcg = 0;
        $tax_ltcg = 0;
        $tax_vda = 0;
        $tax_normal = 0;
        $tax_before_rebate = 0;
        $tax_87a = 0;
        $tax_after_rebate = 0;
        $tax_cess = 0;
        $tax_gross = 0;
        $int_total = 0;
        $tax_total_payable = 0;
        $rounding_off = 0;
        $tax_net = 0;

        // You can compute these fields using the same formulas as your JS.
        // For brevity, I'm leaving them as 0 – you should implement the tax calculation here.

        // Get interest total from interestEntry
        $interestEntry = $computation->interestEntry;
        $int_total = ($interestEntry->interest_234a ?? 0)
                   + ($interestEntry->interest_234b ?? 0)
                   + ($interestEntry->interest_234c ?? 0)
                   + ($interestEntry->fee_234f ?? 0);

        // Tax paid components
        $tds_claimed = $computation->tdsEntries->sum('tds_claimed');
        $tcs_claimed = $computation->tcsEntries->sum('tcs_claimed');
        $advance_tax = $computation->advanceTaxEntries->sum('amount_paid');
        $sat_paid = $computation->selfAssessmentTaxEntries->sum('amount_paid');
        $total_tax_paid = $tds_claimed + $tcs_claimed + $advance_tax + $sat_paid;

        // Pass data to PDF view
        $data = [
            'computation'    => $computation,
            'gti'            => $gti,
            'tti'            => $tti,
            'deductions'     => $deductions,
            'tax_stcg'       => $tax_stcg,
            'tax_ltcg'       => $tax_ltcg,
            'tax_vda'        => $tax_vda,
            'tax_normal'     => $tax_normal,
            'tax_before_rebate' => $tax_before_rebate,
            'tax_87a'        => $tax_87a,
            'tax_after_rebate' => $tax_after_rebate,
            'tax_cess'       => $tax_cess,
            'tax_gross'      => $tax_gross,
            'int_total'      => $int_total,
            'tax_total_payable' => $tax_total_payable,
            'rounding_off'   => $rounding_off,
            'tax_net'        => $tax_net,
            'tds_claimed'    => $tds_claimed,
            'tcs_claimed'    => $tcs_claimed,
            'advance_tax'    => $advance_tax,
            'sat_paid'       => $sat_paid,
            'total_tax_paid' => $total_tax_paid,
        ];

        $pdf = Pdf::loadView('Backoffice.computation.pdf.summary.summary-pdf', $data);

        return $pdf->download('computation-' . $computation->id . '.pdf');
    }

    // ─── Error Helpers ───────────────────────────────────────────────

    private function addCalculationError(string $message): void
    {
        $this->errors[] = $message;
    }

    private function hasCalculationErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getCalculationErrors(): array
    {
        return $this->errors;
    }
}