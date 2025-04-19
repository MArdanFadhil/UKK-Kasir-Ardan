<?php

namespace App\Models;

use App\Models\User;
use App\Models\Member;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'pay_date',
        'total_price',
        'total_pay',
        'total_return',
        'member_id',
        'user_id',
        'poin',
        'used_point',
        'member_type',
        'payment_amount',
        'change',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_purchase', 'purchase_id', 'product_id')
                    ->withPivot('quantity', 'price', 'subtotal')
                    ->withTimestamps();
    }

}
