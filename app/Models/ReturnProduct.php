<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'return_quantity',
        'selling_price',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'return_quantity' => 'integer',
        'selling_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductDetail::class, 'product_id');
    }
}
