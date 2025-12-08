<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualSalesItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'manual_sale_id',
        'product_name',
        'product_code',
        'category',
        'quantity',
        'quantity_type',
        'price',
        'discount',
        'total',
    ];

    public function manualSale()
    {
        return $this->belongsTo(ManualSale::class);
    }
}
