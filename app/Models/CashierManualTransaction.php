<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierManualTransaction extends Model
{
    protected $fillable = [
        'cashier_balance_id',
        'type',
        'amount',
        'notes',
        'created_by'
    ];

    /**
     * Get the cashier balance that owns the transaction.
     */
    public function cashierBalance()
    {
        return $this->belongsTo(CashierBalance::class);
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}