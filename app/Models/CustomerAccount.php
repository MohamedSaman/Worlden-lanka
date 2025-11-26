<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'back_forward_amount',
        'current_due_amount',
        'paid_due',
        'total_due',
        'advance_amount'
    ];

    // A CustomerAccount belongs to a Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // A CustomerAccount belongs to a Sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
