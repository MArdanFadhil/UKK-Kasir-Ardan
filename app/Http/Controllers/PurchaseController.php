<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PurchasesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $purchases = Purchase::with(['member', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('member', function ($q2) use ($search) {
                        $q2->where('name', 'ILIKE', "%$search%");
                    })
                    ->orWhereRaw("TO_CHAR(pay_date, 'DD Mon YYYY') ILIKE ?", ["%$search%"]);
                });
            })
            ->latest()
            ->paginate(10);
    
        return view('purchase.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::all();
        return view('purchase.create', compact('products'));
    }

    public function store(Request $request)
    {
        $quantities = session('quantities');
        $memberType = $request->member_type;
        $userId = auth()->id();

        if ($memberType === 'member' && $request->phone_number) {
            $member = Member::firstOrCreate(
                ['phone_number' => $request->phone_number],
                ['name' => $request->member_name ?? 'Member', 'points' => 100]
            );
            
            $isNew = $member->wasRecentlyCreated;
            
            session([
                'member_id' => $member->id,
                'payment_amount' => $request->payment_amount,
                'member_type' => 'member',
                'is_new_member' => $isNew,
            ]);
            
            return redirect()->route('purchase.memberPayment');
        }
        
        $member = Member::firstOrCreate(
            ['phone_number' => '0000000000'],
            ['name' => 'Non Member', 'points' => 0]
        );

        $totalPrice = 0;
        if (!empty($quantities)) {
            foreach ($quantities as $productId => $qty) {
                if ($qty > 0) {
                    $product = Product::findOrFail($productId);
                    $totalPrice += $product->price * $qty;
                }
            }
        }

        $paymentAmount = floatval(str_replace(['Rp', '.', ','], '', $request->payment_amount));
        $totalReturn = max($paymentAmount - $totalPrice, 0);

        $purchase = Purchase::create([
            'pay_date' => now(),
            'purchase_date' => now(),
            'member_id' => $member->id,
            'payment_amount' => $paymentAmount,
            'total_price' => $totalPrice,
            'total_pay' => $paymentAmount,
            'total_return' => $totalReturn,
            'user_id' => $userId,
            'used_point' => 0,
        ]);

        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::findOrFail($productId);
                $purchase->products()->attach($productId, [
                    'quantity' => $qty,
                    'price' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);
                $product->decrement('stock', $qty);
            }
        }

        session()->forget(['quantities', 'payment_amount', 'total_amount', 'member_id', 'member_type', 'phone_number']);

        return redirect()->route('purchase.detail', $purchase->id);
    }

    public function confirm()
    {
        $quantities = session('quantities', []);
        $products = Product::whereIn('id', array_keys($quantities))->get();
        $total = 0;

        foreach ($products as $product) {
            $qty = $quantities[$product->id];
            $total += $product->price * $qty;
        }

        return view('purchase.confirm', compact('products', 'quantities', 'total'));
    }

    public function selectProduct(Request $request)
    {
        $quantities = collect($request->input('quantities'))->filter(fn($qty) => $qty > 0);

        if ($quantities->isEmpty()) {
            return redirect()->back()->with('error', 'Silakan pilih produk terlebih dahulu.');
        }

        $products = Product::whereIn('id', $quantities->keys())->get();
        $total = 0;

        foreach ($products as $product) {
            $qty = $quantities[$product->id] ?? 0;
            $total += $product->price * $qty;
        }

        session([
            'quantities' => $quantities,
            'payment_amount' => $total,
        ]);

        return view('purchase.confirm', compact('products', 'quantities', 'total'));
    }

    public function memberPayment()
    {
        $quantities = session('quantities') ?? [];

        if (empty($quantities)) {
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan.');
        }

        $memberId = session('member_id');
        $member = Member::find($memberId);

        if (!$member) {
            return redirect()->route('purchase.index')->with('error', 'Member tidak ditemukan.');
        }

        $productIds = collect($quantities)->keys()->toArray();
        $products = Product::whereIn('id', $productIds)->get();
        $totalAmount = 0;

        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = $products->firstWhere('id', $productId);
                if ($product) {
                    $totalAmount += $product->price * $qty;
                }
            }
        }

        return view('purchase.payment_member', [
            'member' => $member,
            'quantities' => $quantities,
            'products' => $products,
            'total' => $totalAmount,
            'discount' => 0,
            'totalAmountAfterDiscount' => $totalAmount,
            'payment_amount' => session('payment_amount'),
        ]);
    }

    public function storeMember(Request $request)
    {
        $quantities = session('quantities');
        $memberId = session('member_id');
        $userId = auth()->id();
        $paymentAmount = floatval(str_replace(['Rp', '.', ','], '', $request->payment_amount));

        $totalPrice = 0;
        if (!empty($quantities)) {
            foreach ($quantities as $productId => $qty) {
                if ($qty > 0) {
                    $product = Product::findOrFail($productId);
                    $totalPrice += $product->price * $qty;
                }
            }
        }

        $usedPoint = intval($request->used_point ?? 0);
        $pointsValue = 10;
        $pointValue = $usedPoint * $pointsValue;
        $totalAfterPoint = max($totalPrice - $pointValue, 0);
        $totalReturn = max($paymentAmount - $totalAfterPoint, 0);

        $purchase = Purchase::create([
            'pay_date' => now(),
            'purchase_date' => now(),
            'member_id' => $memberId,
            'payment_amount' => $paymentAmount,
            'total_price' => $totalPrice,
            'total_pay' => $paymentAmount,
            'total_return' => $totalReturn,
            'user_id' => $userId,
            'used_point' => $usedPoint,
        ]);

        $isNewMember = session('is_new_member', false); // default false
        $usedPoint = intval($request->used_point ?? 0);

        if ($isNewMember && $usedPoint > 0) {
            return redirect()->back()->withErrors([
                'used_point' => 'Member baru tidak bisa langsung menggunakan poin.'
            ])->withInput();
        }

        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::findOrFail($productId);
                $purchase->products()->attach($productId, [
                    'quantity' => $qty,
                    'price' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);
                $product->decrement('stock', $qty);
            }
        }

        $member = Member::find($memberId);
        $earnedPoints = floor($totalAfterPoint * 0.01);

        if ($member) {
            $member->name = $request->input('member_name');
            $member->phone_number = $request->input('phone_number');

            if ($usedPoint > 0 && $member->points >= $usedPoint) {
                $member->points -= $usedPoint;
            }

            $member->points += $earnedPoints;
            $member->save();
        }

        session()->forget(['quantities', 'payment_amount', 'total_amount', 'member_id', 'name', 'phone_number']);

        return redirect()->route('purchase.detail', $purchase->id)->with([
            'total' => $totalAfterPoint,
            'return' => $totalReturn,
        ]);
    }

    public function detail($id)
    {
        try {
            $purchase = Purchase::with(['products', 'member', 'user'])->findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('purchase.index')->with('error', 'Pembelian tidak ditemukan.');
        }

        return view('purchase.detail', compact('purchase'));
    }

    public function orderDetail(Request $request)
    {
        $purchaseId = $request->input('purchase_id');
        $purchase = Purchase::find($purchaseId);

        if (!$purchase) {
            return redirect()->back()->with('error', 'Pembelian tidak ditemukan.');
        }

        return redirect()->route('purchase.detail', $purchase->id)
                         ->with('success', 'Detail pembelian diperbarui.');
    }

    public function export()
    {
        return Excel::download(new PurchasesExport, 'data-pembelian.xlsx');
    }

    public function downloadReceipt($id)
    {
        $purchase = Purchase::with(['products', 'member', 'user'])->findOrFail($id);

        return Pdf::loadView('purchase.receipt', compact('purchase'))
                ->download("struk-pembelian-{$purchase->id}.pdf");
    }
}
