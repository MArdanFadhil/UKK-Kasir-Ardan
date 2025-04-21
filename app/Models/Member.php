<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';
    protected $fillable = [
        'name',
        'phone_number',
        'points',
        'date',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

}
