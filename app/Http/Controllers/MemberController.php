<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function check(Request $request)
    {
        $phone = $request->query('phone');
        $member = Member::where('phone_number', $phone)->first();
        
        if ($member) {
            return response()->json([
                'exists' => true,
                'name' => $member->name,
                'points' => $member->points
            ]);
        }
        
        return response()->json([
            'exists' => false
        ]);
    }
}