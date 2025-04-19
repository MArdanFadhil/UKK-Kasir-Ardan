<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPurchase extends Model
{
    use HasFactory;
    protected $table = 'product_purchases';

    protected $fillable = [
        'price',
        'product_id',
        'purchase_id',
        'quantity',
        'subtotal',
    ];
}
