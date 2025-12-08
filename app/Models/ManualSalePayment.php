<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualSalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'manual_sale_id',
        'amount',
        'payment_method',
        'payment_reference',
        'bank_name',
        'is_completed',
        'payment_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function manualSale()
    {
        return $this->belongsTo(ManualSale::class);
    }
}
