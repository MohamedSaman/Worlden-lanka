<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuantityType extends Model
{
    protected $table = 'quantity_types';
    protected $fillable = ['name', 'code', 'description', 'is_active'];
    public $timestamps = true;
}
