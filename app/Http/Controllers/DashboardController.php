<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today();
        $totalPurchasesToday = 0;
        $lastUpdated = null;
    
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login');
        }
    
        $roleId = $user->role_id;
    
        if ($roleId != 1) {
            $totalPurchasesToday = Purchase::whereDate('created_at', $today)->count();
            $lastUpdated = Purchase::whereDate('created_at', $today)->latest()->value('created_at');
        }
        $dailySales = Purchase::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get();
        
        $dates = $dailySales->pluck('date');
        $barLabels = [];
        $barData = [];
        
        foreach ($dates as $date) {
            $formatted = Carbon::parse($date)->format('d M');
            $barLabels[] = $formatted;
        
            $matched = $dailySales->firstWhere('date', $date);
            $barData[] = $matched ? $matched->total : 0;
        }
        
    
        // Pie Chart: Jumlah produk berdasarkan nama produk
        $products = Product::select('name_product')
            ->groupBy('name_product')
            ->get();
    
        $pieLabels = $products->pluck('name_product');
        $pieData = $products->map(function ($product) {
            return Product::where('name_product', $product->name_product)->count();
        });
    
        return view('dashboard', compact(
            'roleId',
            'totalPurchasesToday',
            'lastUpdated',
            'barLabels',
            'barData',
            'pieLabels',
            'pieData'
        ));
    }    

}

