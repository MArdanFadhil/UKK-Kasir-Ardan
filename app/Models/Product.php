<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = 'products';

    protected $fillable = [
        'img',
        'name_product',
        'price',
        'stock',
    ];

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'product_purchase', 'product_id', 'purchase_id')
                    ->withPivot('quantity', 'price', 'subtotal')
                    ->withTimestamps();
    }

}
