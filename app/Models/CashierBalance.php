<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierBalance extends Model
{
    protected $fillable = [
        'date',
        'opening_balance',
        'closing_balance',
        'total_sales',
        'total_expenses',
        'additional_earnings',
        'manual_expenses',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['date'];

    /**
     * Get all manual transactions for this balance.
     */
    public function manualTransactions()
    {
        return $this->hasMany(CashierManualTransaction::class);
    }

    /**
     * Get the user who created the balance record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the balance record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}