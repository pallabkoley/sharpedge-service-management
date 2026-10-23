<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\ClientBankAccount;
use App\Models\ClientCase;
use App\Models\ClientCaseFollowUp;
use App\Models\ClientCompliance;
use App\Models\ClientComplianceFollowUp;
use App\Models\ClientTurnover;
use App\Models\ClientPortalCredential;
use App\Models\ClientCustomField;
use App\Models\ClientCustomFieldValue;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf;

class ClientMasterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CLIENT MASTER LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('client_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('gst_number', 'like', "%{$search}%")
                    ->orWhere('pan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clients = $query->latest('id')->get();

        return view('admin.client-master.index', compact('clients'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE CLIENT PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.client-master.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE CLIENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'father_name'           => 'nullable|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'phone'                 => 'nullable|string|max:50',
            'alternate_mobile'      => 'nullable|string|max:50',

            'dob'                   => 'nullable|date',
            'pan'                   => 'nullable|string|max:20',
            'aadhaar'               => 'nullable|string|max:30',
            'gender'                => 'nullable|string|max:50',
            'client_type'           => 'nullable|string|max:100',

            'address'               => 'nullable|string',
            'city'                  => 'nullable|string|max:100',
            'state'                 => 'nullable|string|max:100',
            'pincode'               => 'nullable|string|max:20',

            'company_name'          => 'nullable|string|max:255',
            'gst_number'            => 'nullable|string|max:100',
            'tan'                   => 'nullable|string|max:50',
            'cin_llpin'             => 'nullable|string|max:100',
            'nature_of_business'    => 'nullable|string',
            'constitution'          => 'nullable|string|max:150',
            'approx_annual_turnover'=> 'nullable|numeric',

            'residential_status'    => 'nullable|string|max:100',
            'last_udin_used'        => 'nullable|string|max:100',

            'preferred_contact'     => 'nullable|string|max:100',
            'source'                => 'nullable|string|max:100',
            'notes'                 => 'nullable|string',
            'compliance_notes'      => 'nullable|string',

            'status'                => 'nullable|string|max:50',
            'is_credit_allowed'     => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['user_id'] = null;

        $client = Client::create($validated);

        return redirect()
            ->route('admin.client-master.show', $client)
            ->with('success', 'Client created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | CLIENT MASTER SUMMARY (SHOW)
    |--------------------------------------------------------------------------
    */

    public function show(Client $client)
    {
        $client->load([
            'user',
            'creator',
            'bankAccounts',
            'cases.followUps.createdBy',
            'compliances.parentCompliance',
            'compliances.followUps.createdBy',
            'turnovers',
            'portalCredentials',
            'customFieldValues.customField',
            'documents',
            'orders',
            'payments',
            'meetings',
        ]);

        $customFields = ClientCustomField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.client-master.show', compact('client', 'customFields'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT CLIENT
    |--------------------------------------------------------------------------
    */

    public function edit(Client $client)
    {
        return view('admin.client-master.edit', compact('client'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE CLIENT
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'father_name'            => 'nullable|string|max:255',
            'email'                  => 'nullable|email|max:255',
            'phone'                  => 'nullable|string|max:50',
            'alternate_mobile'       => 'nullable|string|max:50',

            'dob'                    => 'nullable|date',
            'pan'                    => 'nullable|string|max:20',
            'aadhaar'                => 'nullable|string|max:30',
            'gender'                 => 'nullable|string|max:50',
            'client_type'            => 'nullable|string|max:100',

            'address'                => 'nullable|string',
            'city'                   => 'nullable|string|max:100',
            'state'                  => 'nullable|string|max:100',
            'pincode'                => 'nullable|string|max:20',

            'company_name'           => 'nullable|string|max:255',
            'gst_number'             => 'nullable|string|max:100',
            'tan'                    => 'nullable|string|max:50',
            'cin_llpin'              => 'nullable|string|max:100',
            'nature_of_business'     => 'nullable|string',
            'constitution'           => 'nullable|string|max:150',
            'approx_annual_turnover' => 'nullable|numeric',

            'residential_status'     => 'nullable|string|max:100',
            'last_udin_used'         => 'nullable|string|max:100',

            'preferred_contact'      => 'nullable|string|max:100',
            'source'                 => 'nullable|string|max:100',
            'notes'                  => 'nullable|string',
            'compliance_notes'       => 'nullable|string',

            'status'                 => 'nullable|string|max:50',
            'is_credit_allowed'      => 'nullable|boolean',
        ]);

        $client->update($validated);

        return redirect()
            ->route('admin.client-master.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE CLIENT
    |--------------------------------------------------------------------------
    */

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('admin.client-master.index')
            ->with('success', 'Client deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | BANK ACCOUNTS
    |--------------------------------------------------------------------------
    */

    public function storeBankAccount(Request $request, Client $client)
    {
        $validated = $request->validate([
            'bank_name'      => 'required|string|max:150',
            'account_number' => 'required|string|max:100',
            'ifsc_code'      => 'nullable|string|max:20',
            'account_type'   => 'nullable|string|max:50',
            'is_primary'     => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($client, $validated) {
            if (!empty($validated['is_primary'])) {
                $client->bankAccounts()->update(['is_primary' => false]);
            }
            $client->bankAccounts()->create($validated);
        });

        return back()->with('success', 'Bank account added successfully.');
    }

    public function updateBankAccount(Request $request, Client $client, ClientBankAccount $bankAccount)
    {
        abort_unless($bankAccount->client_id == $client->id, 404);

        $validated = $request->validate([
            'bank_name'      => 'required|string|max:150',
            'account_number' => 'required|string|max:100',
            'ifsc_code'      => 'nullable|string|max:20',
            'account_type'   => 'nullable|string|max:50',
            'is_primary'     => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($client, $bankAccount, $validated) {
            if (!empty($validated['is_primary'])) {
                $client->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_primary' => false]);
            }
            $bankAccount->update($validated);
        });

        return back()->with('success', 'Bank account updated successfully.');
    }

    public function destroyBankAccount(Client $client, ClientBankAccount $bankAccount)
    {
        abort_unless($bankAccount->client_id == $client->id, 404);
        $bankAccount->delete();
        return back()->with('success', 'Bank account deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | GST & IT CASES
    |--------------------------------------------------------------------------
    */

    public function storeCase(Request $request, Client $client)
    {
        $validated = $request->validate([
            'case_type'                 => 'required|string|max:100',
            'other_case_type'           => 'nullable|string|max:150',
            'financial_year'            => 'nullable|string|max:20',
            'reference_notice_din'      => 'nullable|string|max:150',
            'issuing_authority_office'  => 'nullable|string|max:255',
            'status'                    => 'nullable|string|max:50',
            'next_follow_up_date'       => 'nullable|date',
            'amount_involved'           => 'nullable|numeric',
            'case_description'          => 'nullable|string',
        ]);

        $client->cases()->create($validated);
        return back()->with('success', 'Case added successfully.');
    }

    public function updateCase(Request $request, Client $client, ClientCase $case)
    {
        abort_unless($case->client_id == $client->id, 404);

        $validated = $request->validate([
            'case_type'                 => 'required|string|max:100',
            'other_case_type'           => 'nullable|string|max:150',
            'financial_year'            => 'nullable|string|max:20',
            'reference_notice_din'      => 'nullable|string|max:150',
            'issuing_authority_office'  => 'nullable|string|max:255',
            'status'                    => 'nullable|string|max:50',
            'next_follow_up_date'       => 'nullable|date',
            'amount_involved'           => 'nullable|numeric',
            'case_description'          => 'nullable|string',
        ]);

        $case->update($validated);
        return back()->with('success', 'Case updated successfully.');
    }

    public function destroyCase(Client $client, ClientCase $case)
    {
        abort_unless($case->client_id == $client->id, 404);
        $case->delete();
        return back()->with('success', 'Case deleted successfully.');
    }

    public function storeCaseFollowUp(Request $request, Client $client, ClientCase $case)
    {
        abort_unless($case->client_id == $client->id, 404);

        $validated = $request->validate([
            'follow_up_date' => 'required|date',
            'note'           => 'required|string|max:5000',
        ]);

        $case->followUps()->create([
            'follow_up_date' => $validated['follow_up_date'],
            'note'           => $validated['note'],
            'created_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Follow-up logged successfully.');
    }

    public function destroyCaseFollowUp(Client $client, ClientCase $case, ClientCaseFollowUp $followUp)
    {
        abort_unless($case->client_id == $client->id, 404);
        abort_unless($followUp->client_case_id == $case->id, 404);

        $followUp->delete();

        return back()->with('success', 'Follow-up deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLIANCES
    |--------------------------------------------------------------------------
    */

    public function storeCompliance(Request $request, Client $client)
    {
        $validated = $request->validate([
            'compliance_type'      => 'required|string|max:100',
            'period'               => 'nullable|string|max:50',
            'due_date'             => 'nullable|date',
            'status'               => 'nullable|string|max:50',
            'notes'                => 'nullable|string',
            'repeat_automatically' => 'nullable|boolean',
            'parent_compliance_id' => [
                'nullable',
                'integer',
                Rule::exists('client_compliances', 'id')->where('client_id', $client->id),
            ],
        ]);

        $client->compliances()->create($validated);
        return back()->with('success', 'Compliance added successfully.');
    }

    public function updateCompliance(Request $request, Client $client, ClientCompliance $compliance)
    {
        abort_unless($compliance->client_id == $client->id, 404);

        $validated = $request->validate([
            'compliance_type'      => 'required|string|max:100',
            'period'               => 'nullable|string|max:50',
            'due_date'             => 'nullable|date',
            'status'               => 'nullable|string|max:50',
            'notes'                => 'nullable|string',
            'repeat_automatically' => 'nullable|boolean',
            'parent_compliance_id' => [
                'nullable',
                'integer',
                Rule::exists('client_compliances', 'id')->where('client_id', $client->id),
            ],
        ]);

        $compliance->update($validated);
        return back()->with('success', 'Compliance updated successfully.');
    }

    public function destroyCompliance(Client $client, ClientCompliance $compliance)
    {
        abort_unless($compliance->client_id == $client->id, 404);
        $compliance->delete();
        return back()->with('success', 'Compliance deleted successfully.');
    }

    public function storeComplianceFollowUp(Request $request, Client $client, ClientCompliance $compliance)
    {
        abort_unless($compliance->client_id == $client->id, 404);

        $validated = $request->validate([
            'follow_up_date' => 'required|date',
            'note'           => 'required|string|max:5000',
        ]);

        $compliance->followUps()->create([
            'follow_up_date' => $validated['follow_up_date'],
            'note'           => $validated['note'],
            'created_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Compliance follow-up logged successfully.');
    }

    public function destroyComplianceFollowUp(Client $client, ClientCompliance $compliance, ClientComplianceFollowUp $followUp)
    {
        abort_unless($compliance->client_id == $client->id, 404);
        abort_unless($followUp->client_compliance_id == $compliance->id, 404);

        $followUp->delete();

        return back()->with('success', 'Compliance follow-up deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | TURNOVER
    |--------------------------------------------------------------------------
    */

    public function saveTurnover(Request $request, Client $client)
    {
        $validated = $request->validate([
            'financial_year' => 'required|string|max:20',
            'month'          => 'required|integer|min:1|max:12',
            'amount'         => 'required|numeric|min:0',
        ]);

        ClientTurnover::updateOrCreate(
            [
                'client_id'      => $client->id,
                'financial_year' => $validated['financial_year'],
                'month'          => $validated['month'],
            ],
            [
                'amount' => $validated['amount'],
            ]
        );

        return back()->with('success', 'Turnover saved successfully.');
    }

    public function destroyTurnover(Client $client, ClientTurnover $turnover)
    {
        abort_unless($turnover->client_id == $client->id, 404);
        $turnover->delete();
        return back()->with('success', 'Turnover deleted successfully.');
    }

    public function getTurnoverData(Client $client, $fy)
    {
        $months = ClientTurnover::where('client_id', $client->id)
            ->where('financial_year', $fy)
            ->pluck('amount', 'month')
            ->toArray();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[$m] = $months[$m] ?? 0;
        }

        return response()->json($data);
    }

    public function saveBulkTurnover(Request $request, Client $client)
    {
        $validated = $request->validate([
            'financial_year' => 'required|string|max:20',
            'months' => 'required|array|size:12',
            'months.*' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($client, $validated) {
            foreach ($validated['months'] as $month => $amount) {
                if ($amount !== null && $amount !== '') {
                    ClientTurnover::updateOrCreate(
                        [
                            'client_id' => $client->id,
                            'financial_year' => $validated['financial_year'],
                            'month' => (int) $month,
                        ],
                        ['amount' => (float) $amount]
                    );
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Turnover saved successfully.']);
    }

    public function getYearlySummary(Client $client)
    {
        $turnovers = ClientTurnover::where('client_id', $client->id)
            ->select('financial_year', DB::raw('SUM(amount) as total'))
            ->groupBy('financial_year')
            ->orderBy('financial_year', 'desc')
            ->get();

        $maxTotal = $turnovers->max('total') ?: 1;

        return response()->json([
            'years' => $turnovers->map(function ($item) use ($maxTotal) {
                return [
                    'financial_year' => $item->financial_year,
                    'total' => (float) $item->total,
                    'percentage' => $maxTotal > 0 ? round(($item->total / $maxTotal) * 100, 2) : 0,
                ];
            })
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PORTAL CREDENTIALS
    |--------------------------------------------------------------------------
    */

    public function storePortalCredential(Request $request, Client $client)
    {
        $validated = $request->validate([
            'portal'            => 'required|string|max:100',
            'other_portal_name' => 'nullable|string|max:150',
            'login_username'    => 'nullable|string|max:255',
            'password'          => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $client->portalCredentials()->create($validated);
        return back()->with('success', 'Portal credential added successfully.');
    }

    public function updatePortalCredential(Request $request, Client $client, ClientPortalCredential $credential)
    {
        abort_unless($credential->client_id == $client->id, 404);

        $validated = $request->validate([
            'portal'            => 'required|string|max:100',
            'other_portal_name' => 'nullable|string|max:150',
            'login_username'    => 'nullable|string|max:255',
            'password'          => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $credential->update($validated);
        return back()->with('success', 'Portal credential updated successfully.');
    }

    public function destroyPortalCredential(Client $client, ClientPortalCredential $credential)
    {
        abort_unless($credential->client_id == $client->id, 404);
        $credential->delete();
        return back()->with('success', 'Portal credential deleted successfully.');
    }
public function getPortalCredential($client, $credential)
{
    $credential = \App\Models\ClientPortalCredential::where('id', $credential)
        ->where('client_id', $client)
        ->firstOrFail();

    return response()->json([
        'id' => $credential->id,
        'portal' => $credential->portal,
        'other_portal_name' => $credential->other_portal_name,
        'login_username' => $credential->login_username,
        'notes' => $credential->notes,
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | CLIENT CUSTOM FIELD VALUES
    |--------------------------------------------------------------------------
    */

    public function saveCustomFieldValues(Request $request, Client $client)
    {
        $fields = $request->input('fields', []);

        if (!is_array($fields)) {
            return back()->withErrors(['fields' => 'Invalid custom field data.']);
        }

        DB::transaction(function () use ($client, $fields) {
            foreach ($fields as $customFieldId => $value) {
                $customField = ClientCustomField::find($customFieldId);
                if (!$customField || !$customField->is_active) {
                    continue;
                }

                ClientCustomFieldValue::updateOrCreate(
                    [
                        'client_id'       => $client->id,
                        'custom_field_id' => $customField->id,
                    ],
                    [
                        'field_value' => is_array($value) ? json_encode($value) : $value,
                    ]
                );
            }
        });

        return back()->with('success', 'Custom fields saved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL CUSTOM FIELD MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function storeCustomField(Request $request)
    {
        $validated = $request->validate([
            'field_name'   => 'required|string|max:150|unique:client_custom_fields,field_name',
            'field_label'  => 'required|string|max:255',
            'field_type'   => 'required|string|max:50',
            'options'      => 'nullable|string',
            'is_required'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        ClientCustomField::create($validated);
        return back()->with('success', 'Custom field created successfully.');
    }

    public function updateCustomField(Request $request, ClientCustomField $customField)
    {
        $validated = $request->validate([
            'field_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('client_custom_fields', 'field_name')->ignore($customField->id),
            ],
            'field_label' => 'required|string|max:255',
            'field_type'  => 'required|string|max:50',
            'options'     => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $customField->update($validated);
        return back()->with('success', 'Custom field updated successfully.');
    }

    public function destroyCustomField(ClientCustomField $customField)
    {
        DB::transaction(function () use ($customField) {
            ClientCustomFieldValue::where('custom_field_id', $customField->id)->delete();
            $customField->delete();
        });

        return back()->with('success', 'Custom field deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ðŸ“„ PDF EXPORT â€“ Client Summary
    |--------------------------------------------------------------------------
    */

    public function exportPdf(Client $client)
    {
        $client->load([
            'user',
            'creator',
            'bankAccounts',
            'cases.followUps.createdBy',
            'compliances.parentCompliance',
            'compliances.followUps.createdBy',
            'turnovers',
            'portalCredentials',
            'customFieldValues.customField',
        ]);

        $customFields = ClientCustomField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $data = compact('client', 'customFields');

        $pdf = Pdf::loadView('admin.client-master.pdf.summary', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Client_Summary_' . ($client->client_code ?? $client->id) . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | ðŸ”” REMINDERS â€“ Active Case Follow-ups & Compliance Due Dates
    |--------------------------------------------------------------------------
    */
    /*
|--------------------------------------------------------------------------
| 🔔 REMINDERS – Active Case Follow-ups & Compliance Due Dates
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| 🔔 REMINDERS – Active Case Follow-ups & Compliance Due Dates
|--------------------------------------------------------------------------
*/
public function getReminders()
{
    $today = now()->startOfDay();

    $reminders = [];

    // ==========================================================
    // CASE FOLLOW-UP REMINDERS
    // ==========================================================

    $cases = ClientCase::with('client')
        ->where('status', '!=', 'Closed')
        ->whereNotNull('next_follow_up_date')
        ->get();

    foreach ($cases as $case) {

        $followUpDate = \Carbon\Carbon::parse(
            $case->next_follow_up_date
        )->startOfDay();

        $reminderStartDate = $followUpDate->copy()->subDays(10);

        // Reminder starts 10 days before follow-up date
        if ($today->gte($reminderStartDate)) {

            $daysRemaining = $today->diffInDays(
                $followUpDate,
                false
            );

            if ($daysRemaining < 0) {
                $status = 'overdue';
            } elseif ($daysRemaining == 0) {
                $status = 'today';
            } else {
                $status = 'upcoming';
            }

            $reminders[] = [
                'type'        => 'Case Follow-up',
                'client_name' => $case->client->name ?? 'N/A',
                'case_type'   => $case->case_type ?? 'Case',
                'target_date' => $followUpDate->format('d-m-Y'),
                'days'        => $daysRemaining,
                'status'      => $status,
                'url'         => route(
                    'admin.client-master.show',
                    $case->client_id
                ) . '?tab=cases',
            ];
        }
    }

    // ==========================================================
    // COMPLIANCE REMINDERS
    // ==========================================================

    $compliances = ClientCompliance::with('client')
        ->where('status', '!=', 'completed')
        ->whereNotNull('due_date')
        ->get();

    foreach ($compliances as $compliance) {

        $dueDate = \Carbon\Carbon::parse(
            $compliance->due_date
        )->startOfDay();

        $reminderStartDate = $dueDate->copy()->subDays(10);

        // Reminder starts 10 days before due date
        if ($today->gte($reminderStartDate)) {

            $daysRemaining = $today->diffInDays(
                $dueDate,
                false
            );

            if ($daysRemaining < 0) {
                $status = 'overdue';
            } elseif ($daysRemaining == 0) {
                $status = 'today';
            } else {
                $status = 'upcoming';
            }

            $reminders[] = [
                'type'            => 'Compliance Due',
                'client_name'     => $compliance->client->name ?? 'N/A',
                'compliance_type' => $compliance->compliance_type ?? 'Compliance',
                'period'          => $compliance->period ?? null,
                'target_date'     => $dueDate->format('d-m-Y'),
                'days'            => $daysRemaining,
                'status'          => $status,
                'url'             => route(
                    'admin.client-master.show',
                    $compliance->client_id
                ) . '?tab=compliances',
            ];
        }
    }

    // ==========================================================
    // SORT
    // Overdue first, then nearest upcoming
    // ==========================================================

    usort($reminders, function ($a, $b) {
        return $a['days'] <=> $b['days'];
    });

    return response()->json([
        'reminders' => $reminders,
        'count'     => count($reminders),
    ]);
}
}