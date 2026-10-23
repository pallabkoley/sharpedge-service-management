<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\ServiceMasterController;
use App\Http\Controllers\Admin\DocumentRequirementController;
use App\Http\Controllers\Admin\RequestDocumentController;
use App\Http\Controllers\Admin\ServiceRequiredDocumentController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\WorkProcessController;
use App\Http\Controllers\Admin\AdminDocumentUploadController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Marketing\DashboardController as MarketingDashboardController;
use App\Http\Controllers\Marketing\MarketingClientController;
use App\Http\Controllers\Marketing\ClientDocumentController;
use App\Http\Controllers\Marketing\DocumentUploadController;
use App\Http\Controllers\Admin\UPIController;
use App\Http\Controllers\Marketing\MarketingOrderController;
use App\Http\Controllers\Marketing\MarketingPaymentController;
use App\Http\Controllers\Admin\AdminTaskAssignController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminOrderApprovalController;
use App\Http\Controllers\Backoffice\BackofficeHomeController;
use App\Http\Controllers\Backoffice\BackofficeNoticeController;
use App\Http\Controllers\Admin\AdminNoticeController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\AdminTaskVerificationController;
use App\Http\Controllers\Marketing\MarketingOrderDocumentController;
use App\Http\Controllers\Marketing\HolidayCalendarController;
use App\Http\Controllers\Marketing\PaymentFollowUpController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Backoffice\HolidayCalendaController;
use App\Http\Controllers\Backoffice\BackofficeTaskController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminOrderDocumentController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientOrderController;
use App\Http\Controllers\Client\ClientInvoiceController;
use App\Http\Controllers\Client\ClientSupportTicketController;
use App\Http\Controllers\Client\ClientMeetingController;
use App\Http\controllers\Backoffcie\BackofficeMeetingController;
use App\Http\Controllers\Admin\ServiceWorkflowHealthController;
use App\Http\Controllers\Admin\OrderSupportDocumentController;
use App\Http\Controllers\Marketing\PaymentController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminServiceSellingAnalysisController;
use App\Http\Controllers\Backoffice\ComputationController;
use App\Http\Controllers\Admin\PartnerWebsiteController;
use App\Http\Controllers\Backoffice\ReportController;
use App\Http\Controllers\Backoffice\ClientDocsController;
use App\Http\Controllers\Backoffice\TradingAccountController;
use App\Http\Controllers\Backoffice\ProfitLossController;
use App\Http\Controllers\Backoffice\AppropriationController;
use App\Http\Controllers\Backoffice\SalesReconciliationController;
use App\Http\Controllers\Backoffice\AssetsReconciliationController;
use App\Http\Controllers\Backoffice\DebtorReconciliationController;
use App\Http\Controllers\Backoffice\CreditorReconciliationController;
use App\Http\Controllers\Backoffice\BankReconciliationController;
use App\Http\Controllers\Backoffice\BackofficePaymentFollowUpController;
use App\Http\Controllers\Admin\ClientMasterController;

use Illuminate\View\View;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/
Route::prefix('client')
    ->name('client.')
    ->middleware(['auth', 'verified', 'role:client'])
    ->group(function () {

        Route::prefix('meetings')->name('meetings.')->group(function () {

            Route::get('/', [ClientMeetingController::class, 'index'])
                ->name('index');

            Route::post('/', [ClientMeetingController::class, 'store'])
                ->name('store');

            Route::put('{meeting}', [ClientMeetingController::class, 'update'])
    ->name('update');

            Route::delete('{meeting}', [ClientMeetingController::class, 'destroy'])
                ->name('destroy');

            Route::post('{meeting}/availability', [ClientMeetingController::class, 'submitAvailability'])
                ->name('availability');

            Route::post('{meeting}/reschedule', [ClientMeetingController::class, 'requestReschedule'])
                ->name('reschedule');
        });
    
    
        // Tickets
        Route::get('/tickets', [ClientSupportTicketController::class, 'index'])
            ->name('tickets.index');

        Route::post('/tickets', [ClientSupportTicketController::class, 'store'])
            ->name('tickets.store');

        Route::get('/tickets/{ticket}', [ClientSupportTicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/tickets/{ticket}/reply', [ClientSupportTicketController::class, 'reply'])
            ->name('tickets.reply');

        Route::post('/tickets/{ticket}/confirm', [ClientSupportTicketController::class, 'confirmResolved'])
            ->name('tickets.confirm');

        Route::post('/tickets/{ticket}/reopen', [ClientSupportTicketController::class, 'reopen'])
            ->name('tickets.reopen');

        // Orders (AJAX helpers)
    Route::get('/orders/list', [ClientOrderController::class, 'listForTickets'])
    ->name('orders.list');
        Route::get('/orders/{order}/documents', [ClientOrderController::class, 'documents'])
            ->name('orders.documents');
        Route::post('/orders/upload-document', [ClientOrderController::class, 'uploadDocument'])
            ->name('orders.upload-document');

        // Orders pages
        Route::get('/orders', [ClientOrderController::class, 'index'])
            ->name('orders.index');
        Route::get('/orders/{order}', [ClientOrderController::class, 'show'])
            ->name('orders.show');
         Route::get('/orders/{order}/certificate', [ClientOrderController::class, 'downloadCertificate'])
    ->name('orders.certificate');
        // Invoices
        Route::get('/invoices', [ClientInvoiceController::class,'index'])
            ->name('invoices.index');
        Route::get('/invoices/preview/{id}', [ClientInvoiceController::class,'previewInvoice'])
            ->name('invoices.preview');
        Route::get('/invoices/download/{id}', [ClientInvoiceController::class,'downloadInvoice'])
            ->name('invoices.download');
        Route::get('/invoices/{id}', [ClientInvoiceController::class,'show'])
            ->name('invoices.show');

        // Dashboard
        Route::get('/dashboard', [ClientDashboardController::class,'index'])
            ->name('dashboard');
    });















/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Backoffice Routes
|--------------------------------------------------------------------------
*/
Route::prefix('backoffice')
    ->name('backoffice.')
    ->middleware(['auth', 'verified', 'role:backoffice'])
    ->group(function () {
// Inside the backoffice prefix group, e.g., Route::prefix('backoffice')->name('backoffice.')->group(...)

Route::prefix('payment-follow-ups')
            ->name('payment-follow-ups.')
            ->group(function () {

                Route::get(
                    '/',
                    [BackofficePaymentFollowUpController::class, 'index']
                )->name('index');

                Route::post(
                    '/',
                    [BackofficePaymentFollowUpController::class, 'store']
                )->name('store');

                Route::get(
                    '/{paymentId}/details',
                    [BackofficePaymentFollowUpController::class, 'details']
                )->name('details');

                Route::get(
                    '/{paymentId}/follow-ups',
                    [BackofficePaymentFollowUpController::class, 'getFollowUps']
                )->name('get-follow-ups');
            });
        // ─────────────────────────────────────────────────────────────
        // 🔥 PDF ROUTE – MUST BE OUTSIDE THE computations GROUP
        // ─────────────────────────────────────────────────────────────
        Route::get('/computations/{id}/pdf', 
            [App\Http\Controllers\Backoffice\ComputationController::class, 'downloadPdf']
        )->name('computations.pdf');

        // ─────────────────────────────────────────────────────────────
        // 📋 APPROPRIATION
        // ─────────────────────────────────────────────────────────────
        Route::prefix('appropriation')
            ->name('appropriation.')
            ->controller(App\Http\Controllers\Backoffice\AppropriationController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/save-draft', 'saveDraft')->name('save-draft');
                Route::post('/finalize', 'finalize')->name('finalize');
                Route::get('/export-pdf', 'exportPdf')->name('export-pdf');
            });

        // ─────────────────────────────────────────────────────────────
        // 📋 COMPUTATIONS (Main Computation CRUD + Subsidiaries)
        // ─────────────────────────────────────────────────────────────
        Route::prefix('computations')
            ->name('computations.')
            ->group(function () {

                // ─── Debtors Reconciliation ────────────────
                Route::prefix('{computation}/debtors-reconciliation')
                    ->name('debtors-reconciliation.')
                    ->controller(\App\Http\Controllers\Backoffice\DebtorReconciliationController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/register', 'register')->name('register');
                        Route::get('/unregister', 'unregister')->name('unregister');
                        Route::get('/data', 'getData')->name('data');
                        Route::post('/save', 'saveDraft')->name('save');
                        Route::post('/add-row', 'addRow')->name('add-row');
                        Route::delete('/delete-row/{rowId}', 'deleteRow')->name('delete-row');
                        Route::post('/upload-signature', 'uploadSignature')->name('upload-signature');
                        Route::post('/clear-signature', 'clearSignature')->name('clear-signature');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });

                // ─── Creditors Reconciliation ──────────────
                Route::prefix('{computation}/creditors-reconciliation')
                    ->name('creditors-reconciliation.')
                    ->controller(\App\Http\Controllers\Backoffice\CreditorReconciliationController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/register', 'register')->name('register');
                        Route::get('/unregister', 'unregister')->name('unregister');
                        Route::get('/data', 'getData')->name('data');
                        Route::post('/save', 'saveDraft')->name('save');
                        Route::post('/add-row', 'addRow')->name('add-row');
                        Route::delete('/delete-row/{rowId}', 'deleteRow')->name('delete-row');
                        Route::post('/upload-signature', 'uploadSignature')->name('upload-signature');
                        Route::post('/clear-signature', 'clearSignature')->name('clear-signature');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });

                // ─── Bank Reconciliation ────────────────────  ✅ NEW
                Route::prefix('{computation}/bank-reconciliation')
                    ->name('bank-reconciliation.')
                    ->controller(\App\Http\Controllers\Backoffice\BankReconciliationController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/data', 'getData')->name('data');
                        Route::post('/save', 'saveDraft')->name('save');

                        // Cheques
                        Route::post('/add-cheque', 'addChequeRow')->name('add-cheque');
                        Route::delete('/delete-cheque/{rowId}', 'deleteChequeRow')->name('delete-cheque');

                        // Deposits
                        Route::post('/add-deposit', 'addDepositRow')->name('add-deposit');
                        Route::delete('/delete-deposit/{rowId}', 'deleteDepositRow')->name('delete-deposit');

                        // Adjustments
                        Route::post('/add-adjustment', 'addAdjustmentRow')->name('add-adjustment');
                        Route::delete('/delete-adjustment/{rowId}', 'deleteAdjustmentRow')->name('delete-adjustment');

                        // Signature & Finalize
                        Route::post('/upload-signature', 'uploadSignature')->name('upload-signature');
                        Route::post('/clear-signature', 'clearSignature')->name('clear-signature');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });

                // ─── Balance Sheet ────────────────────────
                              // ─── Balance Sheet ────────────────────────
                            // ─── Balance Sheet ────────────────────────
                Route::prefix('{computation}/balance-sheet')
                    ->name('balance-sheet.')
                    ->controller(App\Http\Controllers\Backoffice\BalanceSheetController::class)
                    ->group(function () {
                        // Main view (Will auto-create BS if missing)
                        Route::get('/', 'index')->name('index');

                        // AJAX: Update a single inline editable row
                        Route::post('/item/{item}', 'updateItem')->name('update-item');

                        // ✅ Fixed: Added {balanceSheet} so Laravel can inject BalanceSheet model into your controller's finalize method
                        Route::post('/finalize/{balanceSheet}', 'finalize')->name('finalize');

                        // ✅ Fixed: Added {balanceSheet} so Laravel can inject BalanceSheet model into your exportPdf method
                        Route::get('/export-pdf/{balanceSheet}', 'exportPdf')->name('export-pdf');
                    });

                // ─── Profit & Loss ────────────────────────
             // ─── Profit & Loss ────────────────────────
Route::prefix('{computation}/profit-loss')
    ->name('profit-loss.')
    ->controller(\App\Http\Controllers\Backoffice\ProfitLossController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'getData')->name('data');
        Route::post('/save', 'saveData')->name('save');
        Route::delete('/category/{categoryId}', 'deleteCategory')->name('delete-category');
        Route::delete('/category/{categoryId}/row/{rowId}', 'deleteRow')->name('delete-row');
        Route::post('/finalize', 'finalize')->name('finalize');
    });

                // ─── Trading Account ──────────────────────
                             // ─── Trading Account ──────────────────────
                        // ─── Trading Account ──────────────────────
                Route::prefix('{computation}/trading-account')
                    ->name('trading-account.')
                    ->controller(App\Http\Controllers\Backoffice\TradingAccountController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'saveRows')->name('store'); // JS এ store নামেই কল করা হয়েছে
                        Route::delete('/row/{id}', 'deleteRow')->name('row.delete');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });

                // ─── Sales Reconciliation ────────────────
                Route::prefix('{computation}/sales-reconciliation')
                    ->name('sales-reconciliation.')
                    ->controller(\App\Http\Controllers\Backoffice\SalesReconciliationController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/register', 'register')->name('register');
                        Route::get('/unregister', 'unregister')->name('unregister');
                        Route::get('/data', 'getData')->name('data');
                        Route::post('/save', 'saveDraft')->name('save');
                        Route::post('/add-row', 'addRow')->name('add-row');
                        Route::delete('/delete-row/{rowId}', 'deleteRow')->name('delete-row');
                        Route::post('/upload-signature', 'uploadSignature')->name('upload-signature');
                        Route::post('/clear-signature', 'clearSignature')->name('clear-signature');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });
                    
                // ─── Purchase Reconciliation ─────────────
                Route::prefix('{computation}/purchase-reconciliation')
                    ->name('purchase-reconciliation.')
                    ->controller(\App\Http\Controllers\Backoffice\PurchaseReconciliationController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/register', 'register')->name('register');
                        Route::get('/unregister', 'unregister')->name('unregister');
                        Route::get('/data', 'getData')->name('data');
                        Route::post('/save', 'saveDraft')->name('save');
                        Route::post('/add-row', 'addRow')->name('add-row');
                        Route::delete('/delete-row/{rowId}', 'deleteRow')->name('delete-row');
                        Route::post('/upload-signature', 'uploadSignature')->name('upload-signature');
                        Route::post('/clear-signature', 'clearSignature')->name('clear-signature');
                        Route::post('/finalize', 'finalize')->name('finalize');
                    });

                // ════════════════════════════════════════════════════════
                // ✅ NEW: Assets Reconciliation (Income Tax)
                // ════════════════════════════════════════════════════════
                Route::prefix('{computation}/assets-reconciliation')
                    ->name('assets-reconciliation.')
                    ->controller(App\Http\Controllers\Backoffice\AssetsReconciliationController::class)
                    ->group(function () {
                        // Main view
                        Route::get('/', 'index')->name('index');

                        // AJAX: Load all data
                        Route::get('/data', 'loadData')->name('data');

                        // AJAX: Save all data (assets, investments, security deposits)
                        Route::post('/save', 'saveData')->name('save');

                        // AJAX: Delete single rows
                        Route::delete('/asset/{assetId}', 'deleteAsset')->name('delete-asset');
                        Route::delete('/investment/{investmentId}', 'deleteInvestment')->name('delete-investment');
                        Route::delete('/security/{securityId}', 'deleteSecurityDeposit')->name('delete-security');
                    });
                    
                    
                    
                    Route::prefix('{computation}/assets-reconciliation')
    ->name('assets-reconciliation.')
    ->controller(App\Http\Controllers\Backoffice\AssetsReconciliationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'loadData')->name('data');
        Route::post('/save', 'saveData')->name('save');
        Route::delete('/asset/{assetId}', 'deleteAsset')->name('delete-asset');
        Route::delete('/investment/{investmentId}', 'deleteInvestment')->name('delete-investment');
        Route::delete('/security/{securityId}', 'deleteSecurityDeposit')->name('delete-security');
        Route::post('/finalize', 'finalize')->name('finalize'); // ✅ NEW
    });
                // ════════════════════════════════════════════════════════

                // ════════════════════════════════════════════════════════
                // ✅ Debit / Credit Note Register (already exists)
                // ════════════════════════════════════════════════════════
                Route::prefix('{computation}/debit-credit-registers')
                    ->name('debit-credit-registers.')
                    ->controller(App\Http\Controllers\Backoffice\DebitCreditNoteRegisterController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/check/{type}', 'checkRegister')->name('check');
                        Route::post('/', 'store')->name('store');
                        Route::get('{id}', 'show')->name('show');
                        Route::put('{id}', 'update')->name('update');
                        Route::delete('{id}', 'destroy')->name('destroy');
                        Route::post('{id}/finalize', 'finalize')->name('finalize');
                        Route::post('row/{rowId}/update', 'updateRow')->name('update-row');
                        Route::get('row/{rowId}/breakups', 'getBreakups')->name('get-breakups');
                        Route::post('row/{rowId}/breakups', 'saveBreakups')->name('save-breakups');
                        Route::delete('breakup/{breakupId}', 'destroyBreakup')->name('destroy-breakup');
                    });
                // ════════════════════════════════════════════════════════

                // ─── Main Computation CRUD ────────────────
                Route::get('/', [App\Http\Controllers\Backoffice\ComputationController::class, 'index'])->name('index');

                Route::match(['get', 'post'], '/sheet/{id?}', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'sheet']
                )->name('sheet');

                Route::post('{id}/save', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'saveComputation']
                )->name('save-computation');

                Route::post('{id}/submit', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'submitComputation']
                )->name('submit');

                Route::post('{id}/save-values', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'saveValues']
                )->name('save-values');

                Route::delete('/{id}', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'destroy']
                )->name('destroy');

                Route::post('{id}/upload-media', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'uploadMedia']
                )->name('upload-media');

                Route::post('{id}/remove-media', 
                    [App\Http\Controllers\Backoffice\ComputationController::class, 'removeMedia']
                )->name('remove-media');

                // ⚠️ DO NOT ADD THE PDF ROUTE HERE – IT'S ALREADY DEFINED ABOVE
            });

        // ─────────────────────────────────────────────────────────────
        // 📋 DOCUMENTS
        // ─────────────────────────────────────────────────────────────
        Route::prefix('documents')
            ->name('documents.')
            ->group(function () {
                Route::get('/', [
                    App\Http\Controllers\Backoffice\ClientDocsController::class,
                    'allDocuments'
                ])->name('index');

                Route::post('/upload', [
                    App\Http\Controllers\Backoffice\ClientDocsController::class,
                    'storeGlobal'
                ])->name('store');

                Route::get('/{id}', [
                    App\Http\Controllers\Backoffice\ClientDocsController::class,
                    'showGlobal'
                ])->name('show');

                Route::delete('/{id}', [
                    App\Http\Controllers\Backoffice\ClientDocsController::class,
                    'destroyGlobal'
                ])->name('destroy');
            });

        // ─────────────────────────────────────────────────────────────
        // 📋 REPORTS
        // ─────────────────────────────────────────────────────────────
        Route::get('/reports/profit-loss/{computation}', [
            App\Http\Controllers\Backoffice\ReportController::class,
            'profitLoss'
        ])->name('reports.profit-loss');

        Route::get('/reports/balance-sheet/{computation}', [
            App\Http\Controllers\Backoffice\ReportController::class,
            'balanceSheet'
        ])->name('reports.balance-sheet');

        // ─────────────────────────────────────────────────────────────
        // 📋 PARTNER WEBSITES
        // ─────────────────────────────────────────────────────────────
        Route::get('/partner-websites', [
            App\Http\Controllers\Backoffice\PartnerWebsiteController::class,
            'index'
        ])->name('partner-websites.index');

        // ─────────────────────────────────────────────────────────────
        // 📋 SUPPORT TICKETS
        // ─────────────────────────────────────────────────────────────
        Route::get('/support-tickets', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'index'
        ])->name('support-tickets.index');

        Route::post('/support-tickets', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'store'
        ])->name('support-tickets.store');

        Route::get('/support-tickets/{supportTicket}', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'show'
        ])->name('support-tickets.show');

        Route::post('/support-tickets/{supportTicket}/reply', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'reply'
        ])->name('support-tickets.reply');

        Route::post('/support-tickets/{supportTicket}/pending', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'markPending'
        ])->name('support-tickets.pending');

        Route::post('/support-tickets/{supportTicket}/resolved', [
            App\Http\Controllers\Backoffice\SupportTicketController::class,
            'markResolved'
        ])->name('support-tickets.resolved');

        // ─────────────────────────────────────────────────────────────
        // 📋 TASKS
        // ─────────────────────────────────────────────────────────────
        Route::post('/tasks/{task}/renew', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'renewService'
        ])->name('tasks.renew');

        Route::get('/tasks/{taskId}/renew/check', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'checkRenewEligibility'
        ])->name('tasks.renew.check');

        Route::get('/tasks', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'index'
        ])->name('tasks.index');

        Route::get('/tasks/{taskId}/details', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'getTaskDetails'
        ])->name('tasks.details');

        Route::post('/tasks/{taskId}/start', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'start'
        ])->name('tasks.start');

        Route::post('/tasks/{taskId}/wait', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'markWaiting'
        ])->name('tasks.wait');

        Route::post('/tasks/{taskId}/submit', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'submit'
        ])->name('tasks.submit');

        Route::post('/tasks/{taskId}/remarks', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'updateRemarks'
        ])->name('tasks.remarks');

        Route::post('/tasks/{taskId}/documents/upload', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'uploadDocument'
        ])->name('tasks.documents.upload');

        Route::get('/tasks/documents/{documentId}/download', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'downloadDocument'
        ])->name('tasks.download');

        Route::post('/tasks/{taskId}/messages/send', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'sendMessage'
        ])->name('tasks.messages.send');

        Route::post('/tasks/documents/{documentId}/replace', [
            App\Http\Controllers\Backoffice\BackofficeTaskController::class,
            'replaceDocument'
        ])->name('tasks.documents.replace');

        // ─────────────────────────────────────────────────────────────
        // 📋 LEAVE REQUESTS
        // ─────────────────────────────────────────────────────────────
        Route::get('/leave-requests', [
            App\Http\Controllers\Backoffice\LeaveRequestController::class,
            'index'
        ])->name('leave-requests.index');

        Route::post('/leave-requests', [
            App\Http\Controllers\Backoffice\LeaveRequestController::class,
            'store'
        ])->name('leave-requests.store');

        Route::delete('/leave-requests/{id}/cancel', [
            App\Http\Controllers\Backoffice\LeaveRequestController::class,
            'cancel'
        ])->name('leave-requests.cancel');

        Route::get('/leave-requests/{id}/attachment', [
            App\Http\Controllers\Backoffice\LeaveRequestController::class,
            'viewAttachment'
        ])->name('leave-requests.attachment');

        // ─────────────────────────────────────────────────────────────
        // 📋 HOLIDAY CALENDAR
        // ─────────────────────────────────────────────────────────────
        Route::get('/holiday-calendar', [
            App\Http\Controllers\Backoffice\HolidayCalendaController::class,
            'index'
        ])->name('holidays.calendar');

        // ─────────────────────────────────────────────────────────────
        // 📋 NOTICES
        // ─────────────────────────────────────────────────────────────
        Route::get('/notices', [
            App\Http\Controllers\Backoffice\BackofficeNoticeController::class,
            'index'
        ])->name('notices.index');

        // ─────────────────────────────────────────────────────────────
        // 📋 DASHBOARD
        // ─────────────────────────────────────────────────────────────
        Route::get('/dashboard', [
            App\Http\Controllers\Backoffice\DashboardController::class,
            'index'
        ])->name('dashboard');

        Route::post('/attendance/clock-in', [
            App\Http\Controllers\Backoffice\DashboardController::class,
            'clockIn'
        ])->name('attendance.clockIn');

        Route::post('/attendance/clock-out', [
            App\Http\Controllers\Backoffice\DashboardController::class,
            'clockOut'
        ])->name('attendance.clockOut');

    });
/*
|--------------------------------------------------------------------------
| Marketing Routes
|--------------------------------------------------------------------------
*/
Route::prefix('marketing')
    ->name('marketing.')
    ->middleware(['auth', 'verified', 'role:marketing'])
    ->group(function () {
        
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
        Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        
        
        
/*
|--------------------------------------------------------------------------
| Marketing Meetings (Single Page + AJAX Popups)
|--------------------------------------------------------------------------
*/
        Route::prefix('meetings')->name('meetings.')->group(function () {

            // List meetings + open create modal
            Route::get('/', [
                \App\Http\Controllers\Marketing\MeetingController::class,
                'index'
            ])->name('index');

            // Schedule meeting (submit form / AJAX)
            Route::post('/', [
                \App\Http\Controllers\Marketing\MeetingController::class,
                'store'
            ])->name('store');
        });



        
             Route::get('/support-tickets', [
            \App\Http\Controllers\Marketing\SupportTicketController::class,
            'index'
        ])->name('support-tickets.index');
Route::post('/support-tickets', [
    \App\Http\Controllers\Marketing\SupportTicketController::class,
    'store'
])->name('support-tickets.store');
        // View single ticket (AJAX popup)
        Route::get('/support-tickets/{supportTicket}', [
            \App\Http\Controllers\Marketing\SupportTicketController::class,
            'show'
        ])->name('support-tickets.show');

        // Reply to ticket (client-facing only)
        Route::post('/support-tickets/{supportTicket}/reply', [
            \App\Http\Controllers\Marketing\SupportTicketController::class,
            'reply'
        ])->name('support-tickets.reply');

        // Mark ticket as pending (waiting for client / payment)
        Route::post('/support-tickets/{supportTicket}/pending', [
            \App\Http\Controllers\Marketing\SupportTicketController::class,
            'markPending'
        ])->name('support-tickets.pending');
        
        
        Route::get('/holiday-calendar', [
    \App\Http\Controllers\Marketing\HolidayCalendarController::class,
    'index'
])->name('holidays.calendar');
        
  Route::get('/leave-requests', [
            \App\Http\Controllers\Marketing\LeaveRequestController::class,
            'index'
        ])->name('leave-requests.index');

        // Submit Leave Request
        Route::post('/leave-requests/store', [
            \App\Http\Controllers\Marketing\LeaveRequestController::class,
            'store'
        ])->name('leave-requests.store');

        // Cancel Pending Leave Request
        Route::post('/leave-requests/{id}/cancel', [
            \App\Http\Controllers\Marketing\LeaveRequestController::class,
            'cancel'
        ])->name('leave-requests.cancel');

        // View Attachment
        Route::get('/leave-requests/{id}/attachment', [
            \App\Http\Controllers\Marketing\LeaveRequestController::class,
            'viewAttachment'
        ])->name('leave-requests.attachment');
        
        
        
    //     Route::post('/attendance/clock-in', [\App\Http\Controllers\Marketing\AttendanceController::class, 'clockIn'])
    // ->name('attendance.clockIn');

    //   Route::post('/attendance/clock-out', [\App\Http\Controllers\Marketing\AttendanceController::class, 'clockOut'])
    // ->name('attendance.clockOut');


        /*
        |--------------------------------------------------------------------------
        | ✅ NEW ORDER DOCUMENT UPLOAD ROUTES (AJAX MODAL) - MarketingOrderDocumentController
        |--------------------------------------------------------------------------
        */
        Route::get('/orders/{orderId}/documents/load', [MarketingOrderDocumentController::class, 'loadOrderDocuments'])
            ->name('orders.documents.load');

        Route::post('/orders/documents/upload', [MarketingOrderDocumentController::class, 'uploadOrderDocument'])
            ->name('orders.documents.upload');

        Route::delete('/orders/documents/file/{fileId}', [MarketingOrderDocumentController::class, 'deleteDocumentFile'])
            ->name('orders.documents.file.delete');

        Route::delete('/orders/documents/{orderDocumentId}/delete-all', [MarketingOrderDocumentController::class, 'deleteAllFilesOfDocument'])
            ->name('orders.documents.delete_all');

        Route::get('/orders/{orderId}/documents/{documentMasterId}/exists', [MarketingOrderDocumentController::class, 'checkDocumentExists'])
            ->name('orders.documents.exists');


        /*
        |--------------------------------------------------------------------------
        | Notices
        |--------------------------------------------------------------------------
        */
        Route::get('notices', [
            \App\Http\Controllers\Marketing\MarketingNoticeController::class,
            'index'
        ])->name('notices.index');


        /*
        |--------------------------------------------------------------------------
        | Document Upload Section (Old Feature - DocumentUploadController)
        |--------------------------------------------------------------------------
        */
        Route::get('/documents/upload',
            [DocumentUploadController::class, 'index']
        )->name('documents.upload.index');

        Route::post('/documents/upload',
            [DocumentUploadController::class, 'upload']
        )->name('documents.upload.store');

        Route::get('/documents/view/{id}',
            [DocumentUploadController::class, 'view']
        )->name('documents.view');

        Route::delete('/documents/delete/{id}',
            [DocumentUploadController::class, 'delete']
        )->name('documents.delete');

        Route::get('/documents/upload/client/{client}/services',
            [DocumentUploadController::class, 'getClientServices']
        )->name('documents.upload.client.services');


        /*
        |--------------------------------------------------------------------------
        | Marketing Documents Listing
        |--------------------------------------------------------------------------
        */
        Route::get('/documents',
            [\App\Http\Controllers\Marketing\DocumentController::class, 'index']
        )->name('documents.index');

        Route::get('/documents/service/{id}',
            [\App\Http\Controllers\Marketing\DocumentController::class, 'showServiceDocuments']
        )->name('documents.service');


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [MarketingDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/dashboard/chart-data', [MarketingDashboardController::class, 'getChartData'])
            ->name('dashboard.chart-data');
            
              Route::post('/attendance/clock-in', [MarketingDashboardController::class, 'clockIn'])
    ->name('attendance.clockIn');

Route::post('/attendance/clock-out', [MarketingDashboardController::class, 'clockOut'])
    ->name('attendance.clockOut');



        /*
        |--------------------------------------------------------------------------
        | Clients Resource
        |--------------------------------------------------------------------------
        */
        Route::resource('clients', MarketingClientController::class)->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Orders (MarketingOrderController)
        |--------------------------------------------------------------------------
        */
        Route::get('/orders/create', [MarketingOrderController::class, 'create'])
            ->name('orders.create');

        Route::post('/orders/process-step', [MarketingOrderController::class, 'processStep'])
            ->name('orders.process-step');

        Route::get('/orders', [MarketingOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [MarketingOrderController::class, 'show'])
            ->name('orders.show');

        Route::delete('/orders/{order}', [MarketingOrderController::class, 'destroy'])
            ->name('orders.destroy');

        Route::post('/orders/ajax/validate-coupon', [MarketingOrderController::class, 'validateCoupon'])
            ->name('orders.ajax.validate-coupon');

        Route::post('/orders/ajax/calculate-amounts', [MarketingOrderController::class, 'calculateAmounts'])
            ->name('orders.ajax.calculate-amounts');
            
        Route::post('/orders/{order}/pay-due', [MarketingOrderController::class, 'payDue'])->name('orders.pay-due');
        Route::get('/orders/{order}/payment-status', [MarketingOrderController::class, 'getPaymentStatus'])->name('orders.payment-status');


        /*
        |--------------------------------------------------------------------------
        | Invoice (MarketingOrderController)
        |--------------------------------------------------------------------------
        */
        Route::get('/orders/{order}/invoice',
            [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'invoice']
        )->name('orders.invoice');

        Route::get('/orders/{order}/invoice/download',
            [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'downloadInvoice']
        )->name('orders.invoice.download');


        /*
        |--------------------------------------------------------------------------
        | Quick Create Order
        |--------------------------------------------------------------------------
        */
        Route::post('/orders/quick-create', [MarketingOrderController::class, 'quickCreate'])
            ->name('orders.quick-create');


        /*
        |--------------------------------------------------------------------------
        | ❌ DUPLICATE ROUTES (OLD DOCUMENT UPLOAD SYSTEM) - COMMENTED
        |--------------------------------------------------------------------------
        | These are duplicates of:
        | /orders/documents/upload
        | /orders/{order}/documents
        | /orders/{order}/documents/check/{documentMasterId}
        | /orders/documents/delete/{id}
        |
        | Keeping them commented to avoid conflicts.
        */
        /*
        Route::get(
            '/orders/{order}/documents',
            [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'loadOrderDocuments']
        )->name('orders.documents.index');

        Route::post(
            '/orders/documents/upload',
            [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'uploadOrderDocument']
        )->name('orders.documents.upload');
        */
  // Payment Follow-up
    Route::get(
        '/payment-follow-ups',
        [PaymentFollowUpController::class, 'index']
    )->name('payment-follow-ups.index');

    Route::post(
        '/payment-follow-ups',
        [PaymentFollowUpController::class, 'store']
    )->name('payment-follow-ups.store');

    Route::get(
        '/payment-follow-ups/{paymentId}',
        [PaymentFollowUpController::class, 'show']
    )->name('payment-follow-ups.show');

    Route::get(
        '/payment-follow-ups/{paymentId}/history',
        [PaymentFollowUpController::class, 'history']
    )->name('payment-follow-ups.history');

    Route::get(
        '/payment-follow-ups/{paymentId}/follow-ups',
        [PaymentFollowUpController::class, 'getFollowUps']
    )->name('payment-follow-ups.get-follow-ups');
    });

/*
|--------------------------------------------------------------------------
| ❌ DUPLICATE ROUTES OUTSIDE GROUP (OLD DOCUMENT UPLOAD SYSTEM) - COMMENTED
|--------------------------------------------------------------------------
| These routes were outside the marketing group in your code.
| Keep commented to avoid conflict with new controller routes.
*/
/*
Route::post(
    '/orders/documents/delete/{id}',
    [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'deleteOrderDocument']
)->name('orders.documents.delete');

Route::get(
    '/orders/{order}/documents/check/{documentMasterId}',
    [\App\Http\Controllers\Marketing\MarketingOrderController::class, 'checkDocumentExists']
)->name('orders.documents.check');
*/





/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        Route::prefix('client-master')
            ->name('client-master.')
            ->controller(ClientMasterController::class)
            ->group(function () {

                // ==========================================================
                // CLIENT MASTER BASIC ROUTES
                // ==========================================================

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                // ==========================================================
                // REMINDERS
                // IMPORTANT: MUST BE BEFORE /{client}
                // ==========================================================

                Route::get('/reminders', 'getReminders')
                    ->name('reminders');

                // ==========================================================
                // CLIENT ROUTES
                // ==========================================================

                Route::get('/{client}/edit', 'edit')->name('edit');

                Route::put('/{client}', 'update')->name('update');

                Route::delete('/{client}', 'destroy')->name('destroy');

                Route::get('/{client}/pdf', 'exportPdf')->name('pdf');

                Route::get('/{client}', 'show')->name('show');

                // ==========================================================
                // BANK ACCOUNTS
                // ==========================================================

                Route::post('/{client}/bank-accounts', 'storeBankAccount')
                    ->name('bank-accounts.store');

                Route::put('/bank-accounts/{bankAccount}', 'updateBankAccount')
                    ->name('bank-accounts.update');

                Route::delete('/bank-accounts/{bankAccount}', 'destroyBankAccount')
                    ->name('bank-accounts.destroy');

                // ==========================================================
                // CASES
                // ==========================================================

                Route::post('/{client}/cases', 'storeCase')
                    ->name('cases.store');

                Route::put('/{client}/cases/{case}', 'updateCase')
                    ->name('cases.update');

                Route::delete('/{client}/cases/{case}', 'destroyCase')
                    ->name('cases.destroy');

                Route::post('/{client}/cases/{case}/follow-ups', 'storeCaseFollowUp')
                    ->name('cases.follow-ups.store');

                Route::delete('/{client}/cases/{case}/follow-ups/{followUp}', 'destroyCaseFollowUp')
                    ->name('cases.follow-ups.destroy');

                // ==========================================================
                // COMPLIANCES
                // ==========================================================

                Route::post('/{client}/compliances', 'storeCompliance')
                    ->name('compliances.store');

                Route::put('/{client}/compliances/{compliance}', 'updateCompliance')
                    ->name('compliances.update');

                Route::delete('/{client}/compliances/{compliance}', 'destroyCompliance')
                    ->name('compliances.destroy');

                Route::post('/{client}/compliances/{compliance}/follow-ups', 'storeComplianceFollowUp')
                    ->name('compliances.follow-ups.store');

                Route::delete('/{client}/compliances/{compliance}/follow-ups/{followUp}', 'destroyComplianceFollowUp')
                    ->name('compliances.follow-ups.destroy');

                // ==========================================================
                // TURNOVER
                // ==========================================================

                Route::post('/{client}/turnovers', 'saveTurnover')
                    ->name('turnovers.store');

                Route::delete('/turnovers/{turnover}', 'destroyTurnover')
                    ->name('turnovers.destroy');

                Route::post('/{client}/turnovers/bulk', 'saveBulkTurnover')
                    ->name('turnovers.bulk');

                Route::get('/{client}/turnovers/summary', 'getYearlySummary')
                    ->name('turnovers.summary')
                    ->withoutMiddleware(['verified']);

                Route::get('/{client}/turnovers/{fy}', 'getTurnoverData')
                    ->name('turnovers.data');

                // ==========================================================
                // PORTAL CREDENTIALS
                // ==========================================================

                Route::post('/{client}/portal-credentials', 'storePortalCredential')
                    ->name('portal-credentials.store');

                Route::put('/{client}/portal-credentials/{credential}', 'updatePortalCredential')
                    ->name('portal-credentials.update');
Route::get('/{client}/portal-credentials/{credential}', 'getPortalCredential')
    ->name('portal-credentials.show');
                Route::delete('/{client}/portal-credentials/{credential}', 'destroyPortalCredential')
                    ->name('portal-credentials.destroy');

                // ==========================================================
                // CUSTOM FIELDS
                // ==========================================================

                Route::post('/custom-fields', 'storeCustomField')
                    ->name('custom-fields.store');

                Route::put('/custom-fields/{customField}', 'updateCustomField')
                    ->name('custom-fields.update');

                Route::delete('/custom-fields/{customField}', 'destroyCustomField')
                    ->name('custom-fields.destroy');

                Route::post('/{client}/custom-fields/values', 'saveCustomFieldValues')
                    ->name('custom-fields.values.save');
                    
                    
            });
    


    
        // ============================================================
        // 🔥 EXISTING CLIENT MANAGEMENT ROUTES (Keep as is)
        // ============================================================

        Route::prefix('clients')->group(function () {

            // 🔥 BULK ROUTES MUST COME FIRST
            Route::post('/bulk-edit', [ClientController::class, 'bulkEdit'])
                ->name('clients.bulk-edit');
            Route::post('/bulk-update', [ClientController::class, 'bulkUpdate'])
                ->name('clients.bulk-update');

            // Standard routes
            Route::get('/', [ClientController::class, 'index'])->name('clients.index');
            Route::post('/', [ClientController::class, 'store'])->name('clients.store');
            Route::put('/{id}', [ClientController::class, 'update'])->name('clients.update');
            Route::post('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
            Route::get('/{id}', [ClientController::class, 'show'])->name('clients.show');

            // Document routes
            Route::post('/{id}/documents/upload', [ClientController::class, 'uploadDocuments'])
                ->name('clients.documents.upload');
            Route::delete('/documents/{docId}', [ClientController::class, 'deleteDocument'])
                ->name('clients.documents.delete');

            // Folder management
            Route::post('/{client}/documents/folders', [ClientController::class, 'createFolder'])
                ->name('clients.documents.folders.create');
            Route::post('/{client}/documents/folders/rename', [ClientController::class, 'renameFolder'])
                ->name('clients.documents.folders.rename');
            Route::delete('/{client}/documents/folders/delete', [ClientController::class, 'deleteFolder'])
                ->name('clients.documents.folders.delete');
        });
    
    

    
        
        Route::prefix('partner-websites')->name('partner-websites.')->group(function () {

    Route::get('/', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'index'])
        ->name('index');

    Route::post('/', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'store'])
        ->name('store');

    Route::get('/{id}', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'show'])
        ->name('show');

    Route::post('/{id}', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'update'])
        ->name('update');

    Route::delete('/{id}', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'destroy'])
        ->name('destroy');

    Route::get('/toggle/{id}', [\App\Http\Controllers\Admin\PartnerWebsiteController::class, 'toggle'])
        ->name('toggle');

});
        
        
        
        
        
        
        
         Route::get('/service-selling-analysis', 
            [AdminServiceSellingAnalysisController::class, 'index']
        )->name('service.selling.analysis');

        
        
        
        
        

        
        
        
        
        
// =========================================================
// PAYMENT ROUTES (basic + Pay Later tracking)
// =========================================================
Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
Route::post('/payments', [AdminPaymentController::class, 'store'])->name('payments.store');
Route::delete('/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
Route::get('/payments/{id}/receipt', [AdminPaymentController::class, 'receipt'])->name('payments.receipt');
Route::get('/payments/{id}/receipt-data', [AdminPaymentController::class, 'getReceiptData'])->name('payments.receipt-data');
Route::get('/payments/{id}/download-receipt', [AdminPaymentController::class, 'downloadReceipt'])->name('payments.download-receipt');

// ✅ Pay Later Tracking Routes (NEW)
Route::post('/payments/{paymentId}/follow-up', [AdminPaymentController::class, 'storeFollowUp'])
    ->name('payments.follow-up.store');
Route::get('/payments/{paymentId}/follow-ups', [AdminPaymentController::class, 'followUpHistory'])
    ->name('payments.follow-ups');
Route::post('/payments/{paymentId}/mark-paid', [AdminPaymentController::class, 'markAsPaid'])
    ->name('payments.mark-paid');  
        

        
        
        
            Route::get(
        'order-support-documents/{orderId}',
        [OrderSupportDocumentController::class,'index']
    );

    Route::post(
        'order-support-documents/upload',
        [OrderSupportDocumentController::class,'store']
    );

    Route::delete(
        'order-support-documents/{id}',
        [OrderSupportDocumentController::class,'destroy']
    );

        Route::prefix('workflow-health')
            ->name('workflow-health.')
            ->controller(ServiceWorkflowHealthController::class)
            ->group(function () {

                // Dashboard
                Route::get('/', 'index')->name('index');

                // View workflow health details
                Route::get('/order/{orderId}', 'show')
                    ->whereNumber('orderId')
                    ->name('show');

                // Recalculate workflow
                Route::post('/order/{orderId}/recalculate', 'recalculate')
                    ->whereNumber('orderId')
                    ->name('recalculate');

                // Upload certificate
                Route::post('/order/{orderId}/upload-certificate', 'uploadCertificate')
                    ->whereNumber('orderId')
                    ->name('upload-certificate');

                // Refresh all
                Route::post('/refresh-all', 'refreshAll')
                    ->name('refresh-all');
                    Route::post('/order/{orderId}/replace-certificate', 'replaceCertificate')
    ->whereNumber('orderId')
    ->name('replace-certificate');

            });
    

    

    



    

Route::prefix('meetings')->name('meetings.')->group(function () {
    Route::get('/', [MeetingController::class, 'index'])->name('index');

    Route::post('{meeting}/approve', [MeetingController::class, 'approve'])
        ->name('approve');

    Route::post('{meeting}/reject', [MeetingController::class, 'reject'])
        ->name('reject');
});
    

        
        Route::prefix('support-tickets')->group(function () {
    Route::get('/', [SupportTicketController::class, 'index'])
        ->name('support-tickets.index');

    Route::post('/', [SupportTicketController::class, 'store'])
        ->name('support-tickets.store');

    Route::get('/{supportTicket}', [SupportTicketController::class, 'show'])
        ->name('support-tickets.show');

    Route::patch('/{supportTicket}', [SupportTicketController::class, 'update'])
        ->name('support-tickets.update');

    Route::delete('/{supportTicket}', [SupportTicketController::class, 'destroy'])
        ->name('support-tickets.destroy');

    // 🔹 NEW: Ticket Conversation (Admin / Staff)
    Route::post(
        '/{supportTicket}/reply',
        [SupportTicketController::class, 'reply']
    )->name('support-tickets.reply');
});
        
        
        
             Route::prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/', [CouponController::class, 'store'])->name('coupons.store');

    // 🔹 ADD THIS (for Edit modal data fetch)
    Route::get('/{id}/edit', [CouponController::class, 'edit'])->name('coupons.edit');

    Route::put('/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    Route::patch('/{id}/status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
});





        
            Route::prefix('service-categories')->group(function () {

    Route::get('/', [ServiceCategoryController::class, 'index'])
        ->name('categories.index');

    Route::post('/', [ServiceCategoryController::class, 'store'])
        ->name('categories.store');

    Route::put('/{serviceCategory}', [ServiceCategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])
        ->name('categories.destroy');

    // ✅ POPUP / AJAX ROUTE (NO PAGE)
    Route::get('/{id}/services-popup', [ServiceCategoryController::class, 'servicesPopup'])
        ->name('categories.services.popup');
});
        
        
        
        
        
        

 Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/{orderId}/load-documents', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'loadOrderDocuments'
    ])->name('loadDocuments');

    Route::post('/upload-document', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'uploadOrderDocument'
    ])->name('uploadDocument');

    Route::delete('/document-files/{fileId}', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'deleteDocumentFile'
    ])->name('documentFiles.delete');

    Route::delete('/order-documents/{orderDocumentId}/files', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'deleteAllFilesOfDocument'
    ])->name('orderDocuments.deleteAllFiles');

    Route::get('/{orderId}/documents/{documentMasterId}/exists', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'checkDocumentExists'
    ])->name('checkDocumentExists');

    Route::post('/order-documents/{orderDocumentId}/status', [
        \App\Http\Controllers\Admin\AdminOrderDocumentController::class,
        'changeDocumentStatus'
    ])->name('orderDocuments.changeStatus');
});

        
        
Route::get('/orders/buy-service', [AdminOrderController::class, 'create'])
            ->name('orders.create');
        
        Route::post('/orders/process-step', [AdminOrderController::class, 'processStep'])
            ->name('orders.processStep');
        
        Route::get('/admin-orders', [AdminOrderController::class, 'index'])
            ->name('orders.index');
        
        Route::get('/admin-orders/{id}', [AdminOrderController::class, 'show'])
            ->name('orders.show');
        
        Route::delete('/admin-orders/{id}', [AdminOrderController::class, 'destroy'])
            ->name('orders.destroy');
            
        Route::get('/admin-orders/{order}/invoice', [AdminOrderController::class, 'invoice'])
            ->name('orders.invoice');
        Route::get('/orders/{order}/download-invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
        
Route::post('/admin/clock-in', [AdminAttendanceController::class, 'clockIn'])
    ->name('admin.clock.in');

Route::post('/admin/clock-out', [AdminAttendanceController::class, 'clockOut'])
    ->name('admin.clock.out');
        
        
        
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');

    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    Route::post('/holidays/defaults', [HolidayController::class, 'addDefaultHolidays'])->name('holidays.defaults');
        
        
        

        
     Route::get('/leave-approvals/{id}/details', [
    \App\Http\Controllers\Admin\LeaveApprovalController::class,
    'showDetails'
])->name('leave-approvals.details');
        
// Leave Approvals (Admin)
Route::get('/leave-approvals', [
    \App\Http\Controllers\Admin\LeaveApprovalController::class,
    'index'
])->name('leave-approvals.index');

Route::post('/leave-approvals/{id}/approve', [
    \App\Http\Controllers\Admin\LeaveApprovalController::class,
    'approve'
])->name('leave-approvals.approve');

Route::post('/leave-approvals/{id}/reject', [
    \App\Http\Controllers\Admin\LeaveApprovalController::class,
    'reject'
])->name('leave-approvals.reject');

        
        
        
        Route::get('/leave-policies', [
    \App\Http\Controllers\Admin\LeavePolicyController::class,
    'index'
])->name('leave-policies.index');

Route::post('/leave-policies/store', [
    \App\Http\Controllers\Admin\LeavePolicyController::class,
    'store'
])->name('leave-policies.store');

/*
|--------------------------------------------------------------------------
| + Button (Add Leave Type from same page using AJAX)
|--------------------------------------------------------------------------
*/
Route::post('/leave-types/store', [
    \App\Http\Controllers\Admin\LeavePolicyController::class,
    'storeLeaveType'
])->name('leave-types.store');
        
        
        
        
        
        
Route::get('/attendance-report', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])
    ->name('attendance.report');
        
        
        
        /*
|--------------------------------------------------------------------------
| Admin Document Verification (Marketing Uploaded Docs Approval)
|--------------------------------------------------------------------------
*/
Route::get('/document-verification', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'index'
])->name('document-verification.index');

Route::get('/document-verification/{id}/load', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'load'
])->name('document-verification.load');

Route::post('/document-verification/{id}/approve', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'approve'
])->name('document-verification.approve');

Route::post('/document-verification/{id}/reject', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'reject'
])->name('document-verification.reject');

/* ✅ NEW — BULK ACTIONS */
Route::post('/document-verification/{orderId}/approve-all', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'approveAll'
])->name('document-verification.approve-all');

Route::post('/document-verification/{orderId}/reject-all', [
    \App\Http\Controllers\Admin\DocumentVerificationController::class,
    'rejectAll'
])->name('document-verification.reject-all');

        
              Route::get('tasks/submitted', [AdminTaskVerificationController::class, 'index'])
            ->name('tasks.submitted');

        // View submitted task details
        Route::get('tasks/{task}/verify', [AdminTaskVerificationController::class, 'show'])
            ->name('tasks.verify.show');

        // Verify submitted task
        Route::post('tasks/{task}/verify', [AdminTaskVerificationController::class, 'verify'])
            ->name('tasks.verify.store');

        // Reject submitted task
        Route::post('tasks/{task}/reject', [AdminTaskVerificationController::class, 'reject'])
            ->name('tasks.reject');
        
        Route::get('notices', [AdminNoticeController::class, 'index'])->name('notices.index');
        Route::post('notices', [AdminNoticeController::class, 'store'])->name('notices.store');
        Route::get('notices/{notice}', [AdminNoticeController::class, 'show'])->name('notices.show');
        Route::put('notices/{notice}', [AdminNoticeController::class, 'update'])->name('notices.update');
        Route::delete('notices/{notice}', [AdminNoticeController::class, 'destroy'])->name('notices.destroy');
        Route::patch('notices/{notice}/toggle', [AdminNoticeController::class, 'toggleStatus'])->name('notices.toggle');

        Route::prefix('tasks')
            ->name('tasks.')
            ->group(function () {
                Route::get('/assign', [AdminTaskAssignController::class, 'index'])
                    ->name('assign.index');
                Route::post('/assign/{task}', [AdminTaskAssignController::class, 'assign'])
                    ->name('assign.store');
                Route::post('/reassign/{task}', [AdminTaskAssignController::class, 'reassign'])
                    ->name('reassign');
            });

        Route::get('/documents/upload',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'index']
        )->name('documents.upload.index');

        Route::post('/documents/upload',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'upload']
        )->name('documents.upload.store');

        Route::post('/documents/upload-multiple',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'uploadMultiple']
        )->name('documents.upload.multiple');

        Route::get('/documents/view/{id}',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'view']
        )->name('documents.view');

        Route::get('/documents/download/{id}',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'download']
        )->name('documents.download');

        Route::delete('/documents/delete/{id}',
            [\App\Http\Controllers\Admin\AdminDocumentUploadController::class, 'delete']
        )->name('documents.delete');
        
        Route::get('/orders/pending-approval', [AdminOrderApprovalController::class, 'index'])
            ->name('orders.pending');
            
        Route::post('/orders/{order}/approve', [AdminOrderApprovalController::class, 'approve'])
            ->name('orders.approve');
            
        Route::post('/orders/{order}/reject', [AdminOrderApprovalController::class, 'reject'])
            ->name('orders.reject');
            
        Route::get('/payments/{payment}', [AdminOrderApprovalController::class, 'showPayment'])
            ->name('payments.show');
            
        Route::get('/orders/{order}/details', [AdminOrderApprovalController::class, 'showOrder'])
            ->name('orders.details');
            
        Route::get('/payments/{payment}/proof', [AdminOrderApprovalController::class, 'viewPaymentProof'])
            ->name('payments.proof');
           
        Route::put('/orders/{order}/update-payment', [AdminOrderApprovalController::class, 'updatePayment'])
            ->name('orders.update-payment');
            
        Route::get('/orders/{order}/history', [AdminOrderApprovalController::class, 'statusHistory'])
            ->name('orders.history');
            
        Route::post('/orders/add-addon', [AdminOrderApprovalController::class, 'addAddonService'])
         ->name('orders.add-addon');
        
        Route::post('/orders/{order}/edit-invoice', [AdminOrderApprovalController::class, 'updateInvoice'])
            ->name('orders.edit-invoice');
            
        Route::get('/orders/{order}/edit-invoice-data', [AdminOrderApprovalController::class, 'editInvoice'])
            ->name('orders.edit-invoice-data');
            
        Route::get('/payments/all', function () {
            return app(AdminOrderApprovalController::class)->index(request());
        })->name('payments.all');
        
        Route::get('/payments/pending', function () {
            return app(AdminOrderApprovalController::class)->index(request()->merge(['status' => 'submitted']));
        })->name('payments.pending');
        
        Route::get('/payments/approved', function () {
            return app(AdminOrderApprovalController::class)->index(request()->merge(['status' => 'approved']));
        })->name('payments.approved');
        
        Route::get('/payments/rejected', function () {
            return app(AdminOrderApprovalController::class)->index(request()->merge(['status' => 'rejected']));
        })->name('payments.rejected');
        
        Route::get('/payments/{payment}/proof', [AdminOrderApprovalController::class, 'viewPaymentProof'])
    ->name('payments.proof');
    
        Route::post('/orders/{order}/generate-invoice', [
    AdminOrderApprovalController::class,
    'confirmGovtFeeAndGenerateInvoice'
])->name('orders.generate-invoice');

// Revise invoice after generation (govt fee change)
Route::post('/orders/{order}/revise-invoice', [
    AdminOrderApprovalController::class,
    'reviseInvoice'
])->name('orders.revise-invoice');
        Route::get('/orders/{order}/invoice/view', [
    AdminOrderApprovalController::class,
    'viewInvoice'
])->name('orders.invoice.view');

        Route::prefix('upi-settings')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UPIController::class, 'index'])->name('upi.index');
            Route::post('/', [\App\Http\Controllers\Admin\UPIController::class, 'store'])->name('upi.store');
            Route::get('/view-qr', [\App\Http\Controllers\Admin\UPIController::class, 'viewQR'])->name('upi.view');
            Route::get('/regenerate-qr', [\App\Http\Controllers\Admin\UPIController::class, 'regenerateQR'])->name('upi.regenerate');
            Route::get('/download-qr', [\App\Http\Controllers\Admin\UPIController::class, 'downloadQR'])->name('upi.download');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\UPIController::class, 'destroy'])->name('upi.destroy');
            Route::get('/api/get-settings', [\App\Http\Controllers\Admin\UPIController::class, 'getUPISettings'])->name('upi.api.settings');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/clock-in', [DashboardController::class, 'clockIn'])
            ->name('attendance.clockIn');

        Route::post('/clock-out', [DashboardController::class, 'clockOut'])
            ->name('attendance.clockOut');

        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit'); 
        Route::put('staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('staff/{id}/status', [StaffController::class, 'toggleStatus'])->name('staff.status');
        Route::delete('staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('/admin/staff/{id}', [StaffController::class, 'show'])
    ->name('staff.show');

     Route::prefix('document-requirements')->group(function () {

    // Main page
    Route::get('/', [DocumentRequirementController::class, 'index'])
        ->name('document-requirements.index');

    // List requirements (grouped by service)
    Route::get('/list', [DocumentRequirementController::class, 'getRequirements'])
        ->name('document-requirements.list');

    // 🔥 SERVICE-BASED DOCUMENTS (NEW - REQUIRED)
    Route::get('/documents/by-service', [DocumentRequirementController::class, 'getDocumentsByService'])
        ->name('document-requirements.documents.by-service');

    // Add new document to master
    Route::post('/add-document', [DocumentRequirementController::class, 'addDocument'])
        ->name('document-requirements.add-document');

    // Store new requirements
    Route::post('/', [DocumentRequirementController::class, 'store'])
        ->name('document-requirements.store');

    // Update requirements for service
    Route::put('/service/{serviceId}', [DocumentRequirementController::class, 'update'])
        ->name('document-requirements.update');

    // Toggle document status
    Route::post('/{id}/toggle-status', [DocumentRequirementController::class, 'toggleStatus'])
        ->name('document-requirements.toggle-status');

});

        Route::prefix('services')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('services.index');
            Route::post('/', [ServiceController::class, 'store'])->name('services.store');
            Route::get('/statistics', [ServiceController::class, 'statistics'])->name('services.statistics');
            Route::post('/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
            Route::get('/{service}', [ServiceController::class, 'show'])->name('services.show');
            Route::put('/{service}', [ServiceController::class, 'update'])->name('services.update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
            Route::put('/{service}/status', [ServiceController::class, 'updateStatus'])->name('services.status.update');
        });



        /*
        |--------------------------------------------------------------------------
        | Work Processes Management - FIXED ROUTES
        |--------------------------------------------------------------------------
        */
        Route::prefix('work-processes')->name('work-processes.')->group(function () {
            Route::get('/', [WorkProcessController::class, 'index'])
                ->name('index');
            
            Route::post('/', [WorkProcessController::class, 'store'])
                ->name('store');
            
            Route::get('/{id}/edit', [WorkProcessController::class, 'edit'])
                ->name('edit');
            
            Route::get('/{id}/steps', [WorkProcessController::class, 'steps'])
                ->name('steps');
            
            Route::put('/{id}', [WorkProcessController::class, 'update'])
                ->name('update');
            
            Route::delete('/{id}', [WorkProcessController::class, 'destroy'])
                ->name('destroy');
            
            Route::post('/change-status', [WorkProcessController::class, 'changeStatus'])
                ->name('change-status');
        });
        
        

    






    });

require __DIR__ . '/auth.php';