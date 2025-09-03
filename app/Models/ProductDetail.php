<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_code',
        'category_id',
        'product_name',
        'supplier_price',
        'selling_price',
        'stock_quantity',
        'damage_quantity',
        'sold',
        'status',
        'customer_field'
    ];
    protected $casts = [
        'customer_field' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stock()
    {
        return $this->hasOne(ProductStock::class, 'product_id');
    }
}
