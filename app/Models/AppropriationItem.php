<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppropriationItem extends Model
{
    use HasFactory;

    protected $table = 'appropriation_items';

    protected $fillable = [
        'appropriation_account_id', 'side', 'section', 'sort_order',
        'particulars', 'partner_detail', 'amount',
        'is_system_linked', 'linked_source', 'is_deletable'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_system_linked' => 'boolean',
        'is_deletable' => 'boolean',
    ];

    public function appropriationAccount()
    {
        return $this->belongsTo(AppropriationAccount::class);
    }

    // Section constants for dropdowns
    public static function getDebitSections()
    {
        return [
            'interest_on_capital' => 'Interest on Capital',
            'bonus' => 'Bonus to Partner',
            'salary' => 'Salary to Partner',
            'general_reserve' => 'General Reserve',
            'commission' => 'Commission to Partner',
            'drawings' => 'Drawings',
            'share_of_profit' => 'Share of Profit',
            'other' => 'Other Debit',
        ];
    }

    public static function getCreditSections()
    {
        return [
            'net_profit_brought' => 'Net Profit (P&L Account)',
            'partners_current' => "Partner's Current Account",
            'interest_on_drawings' => 'Interest on Drawings',
            'other_income' => 'Other Income',
            'other' => 'Other Credit',
        ];
    }
}