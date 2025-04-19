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

    public function check(Request $request)
    {
        // Ambil nomor telepon dari parameter query
        $phone = $request->query('phone');

        // Cari member berdasarkan nomor telepon
        $member = Member::where('phone_number', $phone)->first();

        if ($member) {
            // Jika member ditemukan, kembalikan nama dan poin
            return response()->json([
                'exists' => true,
                'name' => $member->name,
                'points' => $member->points,
            ]);
        }

        // Jika member tidak ditemukan
        return response()->json([
            'exists' => false,
        ]);
    }

}
