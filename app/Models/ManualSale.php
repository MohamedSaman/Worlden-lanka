<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'customer_type',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_type',
        'payment_status',
        'status',
        'notes',
        'delivery_note',
        'due_amount',
        'created_at',
        'updated_at',
    ];    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ManualSalesItem::class);
    }

    public function payments()
    {
        return $this->hasMany(ManualSalePayment::class);
    }
}
