<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppropriationAccount extends Model
{
    use HasFactory;

    protected $table = 'appropriation_accounts';

    protected $fillable = [
        'company_id', 'financial_year_id', 'company_name', 'trade_code',
        'financial_year_end', 'assessment_year', 'net_profit',
        'total_debit', 'total_credit', 'closing_capital', 'status',
        'prepared_by', 'place', 'itr_ack_no', 'audit_status', 'udin',
        'rdis', 'audit_ack_no', 'audit_date'
    ];

    protected $casts = [
        'net_profit' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'closing_capital' => 'decimal:2',
        'audit_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function items()
    {
        return $this->hasMany(AppropriationItem::class);
    }

    public function debitItems()
    {
        return $this->hasMany(AppropriationItem::class)->where('side', 'debit')->orderBy('sort_order');
    }

    public function creditItems()
    {
        return $this->hasMany(AppropriationItem::class)->where('side', 'credit')->orderBy('sort_order');
    }

    // Recalculate totals from related items
    public function recalculateTotals()
{
    $totalDebit = $this->debitItems()->sum('amount');
    $totalCredit = $this->creditItems()->sum('amount');
    $this->total_debit = $totalDebit;
    $this->total_credit = $totalCredit;
    
    // Example closing capital calculation (adjust as needed)
    $netProfit = $this->net_profit;
    $drawings = $this->debitItems()->where('section', 'drawings')->sum('amount');
    $interestOnCapital = $this->debitItems()->where('section', 'interest_on_capital')->sum('amount');
    $salary = $this->debitItems()->where('section', 'salary')->sum('amount');
    $openingCapital = 1000000; // This could be fetched from previous year
    
    $this->closing_capital = $openingCapital + $netProfit - $drawings - $interestOnCapital - $salary;
    $this->save();
    
    return $this;
}
}