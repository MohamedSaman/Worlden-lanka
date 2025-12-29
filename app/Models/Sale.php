<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_type',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_type',
        'payment_status',
        'notes',
        'delivery_note',
        'due_amount',
        'user_id',
        'sales_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sales_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerAccount()
    {
        return $this->hasOne(CustomerAccount::class);
    }
    // Generate unique invoice numbers
    public static function generateInvoiceNumber($salesDate = null)
    {
        if (!$salesDate) {
            $salesDate = now();
        } elseif (is_string($salesDate)) {
            $salesDate = \Carbon\Carbon::parse($salesDate);
        }

        $prefix = 'INV-';
        $date = $salesDate->format('Ymd');
        $lastInvoice = self::whereDate('sales_date', $salesDate)
            ->where('invoice_number', 'like', "{$prefix}{$date}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
