<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerArrear extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meter_no',
        'estate_id',
        'arrear_type',
        'description',
        'amount_due',
        'amount_paid',
        'balance',
        'billing_period',
        'due_date',
        'generated_date',
        'paid_date',
        'status',
        'reference_table',
        'reference_id',
        'transaction_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount_due' => 'double',
        'amount_paid' => 'double',
        'balance' => 'double',
        'due_date' => 'date',
        'generated_date' => 'date',
        'paid_date' => 'date',
        'status' => 'integer',
        'user_id' => 'integer',
        'estate_id' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('status', 0);
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFullyPaid($query)
    {
        return $query->where('status', 2);
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', [0, 1])->where('balance', '>', 0);
    }

    // Helper methods
    public function markAsPaid($transactionId = null)
    {
        $this->update([
            'status' => 2,
            'paid_date' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    public function applyPayment($amount, $transactionId = null)
    {
        $paymentAmount = min($amount, $this->balance);

        $this->amount_paid += $paymentAmount;
        $this->balance -= $paymentAmount;

        if ($this->balance == 0) {
            $this->status = 2; // Fully paid
            $this->paid_date = now();
        } else {
            $this->status = 1; // Partially paid
        }

        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }

        $this->save();

        return $paymentAmount;
    }
}
