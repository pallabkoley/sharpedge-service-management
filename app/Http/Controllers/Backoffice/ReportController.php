<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;

class ReportController extends Controller
{
   private function getCommonData($values)
{
    $salary = $values['total_salary'] ?? 0;

    $otherIncome =
        ($values['fd_interest'] ?? 0) +
        ($values['dividend'] ?? 0);

    $capitalGain =
        ($values['short_term_gain'] ?? 0) +
        ($values['long_term_gain'] ?? 0);

    // 🔥 FIXED
    $houseProperty =
        ($values['annual_value'] ?? 0)
        - ($values['municipal_tax'] ?? 0)
        - ($values['housing_interest'] ?? 0);

    $grossProfit = $values['gross_profit'] ?? 0;

    if ($grossProfit >= 0) {
        $businessProfit = $grossProfit;
        $businessLoss = 0;
    } else {
        $businessProfit = 0;
        $businessLoss = abs($grossProfit);
    }

    return compact(
        'salary',
        'otherIncome',
        'capitalGain',
        'houseProperty',
        'businessProfit',
        'businessLoss'
    );
}

    // ======================= PROFIT & LOSS =======================
    public function profitLoss(Computation $computation)
    {
        $values = $computation->getValuesByCode();
        $data = $this->getCommonData($values);

        $totalIncome =
            $data['salary'] +
            $data['houseProperty'] +
            $data['otherIncome'] +
            $data['capitalGain'] +
            $data['businessProfit'] -
            $data['businessLoss'];

        $netProfit = $totalIncome;

        return view('Backoffice.reports.profit-loss', array_merge(
            ['computation' => $computation],
            $data,
            compact('totalIncome', 'netProfit')
        ));
    }

    // ======================= BALANCE SHEET =======================
    public function balanceSheet(Computation $computation)
{
    $values = $computation->getValuesByCode();
    $data = $this->getCommonData($values);

    // =========================
    // NET PROFIT (P&L)
    // =========================
    $netProfit =
        $data['salary'] +
        $data['houseProperty'] +
        $data['otherIncome'] +
        $data['capitalGain'] +
        $data['businessProfit'] -
        $data['businessLoss'];

    // =========================
    // ASSETS
    // =========================
    $cash    = $values['cash_in_hand'] ?? 0;
    $bank    = $values['bank_balance'] ?? 0;
    $debtors = $values['debtors'] ?? 0;
    $stock   = $values['closing_stock'] ?? 0;

    $assets = $cash + $bank + $debtors + $stock;

    // =========================
    // LIABILITIES
    // =========================
    $creditors = $values['creditors'] ?? 0;
    $loan      = $values['loan'] ?? 0;

    $liabilities = $creditors + $loan + $data['businessLoss'];

    // =========================
    // 🔥 CAPITAL (FIXED)
    // =========================
    $capital = $assets; // ✅ BALANCE MATCH FIX

    // =========================
    // RETURN VIEW
    // =========================
    return view('Backoffice.reports.balance-sheet', compact(
        'assets',
        'liabilities',
        'capital',
        'cash',
        'bank',
        'debtors',
        'stock',
        'creditors',
        'loan',
        'netProfit'
    ) + $data);
}
}