<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cheques';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cheque_number',
        'cheque_date',
        'bank_name',
        'cheque_amount',
        'status',
        'customer_id',
        'payment_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cheque_date' => 'date',
        'cheque_amount' => 'decimal:2',
    ];

    /**
     * Get the customer associated with the cheque.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the payment associated with the cheque.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
