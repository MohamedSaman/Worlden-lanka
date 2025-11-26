<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnCheque extends Model
{
    use HasFactory;

    protected $table = 'return_cheques';

    protected $fillable = [
        'customer_id',
        'cheque_id',
        'cheque_amount',
        'balance_amount',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'cheque_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function cheque()
    {
        return $this->belongsTo(Cheque::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
