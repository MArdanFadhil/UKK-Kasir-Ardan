<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase;
use App\Exports\ExportData;
use Illuminate\Http\Request;
use App\Exports\PurchasesExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with('member')->latest()->paginate(10);
        return view('purchase.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::all();
        return view('purchase.create', compact('products'));
        
    }

    public function store(Request $request)
    {
        $quantities = session('quantities'); // Data produk yang dibeli
        $memberType = $request->member_type; // Tipe member (member/non-member)
        $memberPhone = $request->member_type === 'member' ? $request->phone_number : '0000000000'; // Nomor telepon member
        $userId = auth()->id(); // ID pengguna yang melakukan transaksi

        if ($memberType === 'member' && $request->phone_number) {
            // Use the inputted name or provide a default if empty
            $memberName = $request->member_name ?? 'Member'; // Use 'member_name' from input
        
            $member = Member::firstOrCreate(
                ['phone_number' => $request->phone_number], // Use 'phone_number' from input
                ['name' => $memberName] // Save the entered name
            );
        
            if ($member->wasRecentlyCreated) {
                $member->points = 100; 
            } else {
                $member->name = $memberName;
            }
        
            $member->save(); // Save or update the member data
            $memberId = $member->id; // Fetch the member's ID
            session(['member_id' => $memberId]); // Save the ID in the session
        
        } else {
            // If it's not a member, create default non-member data
            $member = Member::firstOrCreate(
                ['phone_number' => '0000000000'], // Default phone number
                ['name' => 'Non Member'] // Default name
            );
        
            $memberId = $member->id;
        }

        // Membuat entri pembelian
        $purchase = Purchase::create([
            'pay_date' => now(),
            'purchase_date' => now(),
            'member_id' => $memberId,
            'payment_amount' => floatval(str_replace(['Rp', '.', ','], '', $request->payment_amount)), // Jumlah pembayaran
            'total_price' => 0, // Tidak menghitung total harga
            'total_pay' => 0, // Tidak menghitung total pembayaran
            'total_return' => 0, // Tidak menghitung kembalian
            'user_id' => $userId,
            'used_point' => 0, // Tidak menghitung poin yang digunakan
        ]);

        // Menyimpan produk yang dibeli
        if (!empty($quantities)) {
            foreach ($quantities as $productId => $qty) {
                if ($qty > 0) {
                    $product = Product::findOrFail($productId);

                    // Menambahkan produk ke pembelian
                    $purchase->products()->attach($productId, [
                        'quantity' => $qty,
                        'price' => $product->price,
                        'subtotal' => 0, // Tidak menghitung subtotal
                    ]);

                    // Mengurangi stok produk
                    $product->decrement('stock', $qty);
                }
            }
        }

        // Jika member, arahkan ke halaman pembayaran
        if ($memberType === 'member') {
            return redirect()->route('purchase.memberPayment', ['id' => $purchase->id]);
        }

        // Reset session jika bukan member
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
        $quantities = collect($request->input('quantities'))->filter(function ($qty) {
            return $qty > 0;
        });

        // Cek apakah ada produk yang dipilih
        if ($quantities->isEmpty()) {
            return redirect()->back()->with('error', 'Silakan pilih produk terlebih dahulu.');
        }

        $products = Product::whereIn('id', $quantities->keys())->get();

        $total = 0;
        foreach ($products as $product) {
            $qty = $quantities[$product->id] ?? 0;
            $total += $product->price * $qty;
        }

        // Simpan ke session
        session([
            'quantities' => $quantities,
            'payment_amount' => $total,
        ]);

        return view('purchase.confirm', [
            'products' => $products,
            'quantities' => $quantities,
            'total' => $total,
        ]);
    }

    public function memberPayment()
    {
        $quantities = session('quantities') ?? [];

        if (empty($quantities)) {
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan.');
        }

        $member = Member::find(session('member_id'));

        $productIds = collect($quantities)->keys()->toArray();
        $products = Product::whereIn('id', $productIds)->get();

        $totalAmount = 0;
        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = $products->firstWhere('id', $productId);
                $totalAmount += $product->price * $qty;
            }
        }

        return view('purchase.payment_member', [
            'member' => $member,
            'quantities' => $quantities,
            'products' => $products,
            'total' => $totalAmount,
            'discount' => 0, // default no discount
            'totalAmountAfterDiscount' => $totalAmount,
            'payment_amount' => session('payment_amount'),
        ]);
    }

    public function storeMember(Request $request)
    {
        // Ambil data dari session
        $quantities = session('quantities');
        $memberId = session('member_id');
        $userId = auth()->id();

        // Ambil jumlah uang yang dibayar (payment_amount)
        $paymentAmount = floatval(str_replace(['Rp', '.', ','], '', $request->payment_amount));

        // Periksa apakah ada item yang dibeli
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

        // Konversi poin menjadi rupiah (asumsi 1 poin = 10 Rupiah)
        $pointsValue = 10; // Setiap poin bernilai 10 IDR
        $pointValue = $usedPoint * $pointsValue;

        // Hitung total harga setelah dikurangi poin
        $totalAfterPoint = max($totalPrice - $pointValue, 0); // Pastikan tidak kurang dari 0

        // Hitung kembalian jika jumlah pembayaran lebih besar dari total harga
        $totalReturn = max($paymentAmount - $totalAfterPoint, 0); // Pastikan minimal 0

        // Membuat transaksi pembelian
        $purchase = Purchase::create([
            'pay_date' => now(),
            'purchase_date' => now(),
            'member_id' => $memberId,
            'payment_amount' => $paymentAmount, // Pay tetap sesuai dengan yang dibayar
            'total_price' => $totalPrice, // Harga total barang
            'total_pay' => $paymentAmount, // Pay tetap
            'total_return' => $totalReturn, // Kembalian
            'user_id' => $userId,
            'used_point' => $usedPoint, // Simpan poin yang digunakan
        ]);

        // Menambahkan item pembelian ke dalam transaksi
        if (!empty($quantities)) {
            foreach ($quantities as $productId => $qty) {
                if ($qty > 0) {
                    $product = Product::findOrFail($productId);

                    $purchase->products()->attach($productId, [
                        'quantity' => $qty,
                        'price' => $product->price,
                        'subtotal' => $product->price * $qty,
                    ]);

                    // Kurangi stok produk
                    $product->decrement('stock', $qty);
                }
            }
        }

        // Update poin anggota (member)
        $member = Member::find(session('member_id'));

        // Calculate totalAfterPoint and totalReturn properly
        $totalAfterPoint = max($totalPrice - $pointValue, 0);
        $totalReturn = max($paymentAmount - $totalAfterPoint, 0);

        // Member earns points from spending
        $earnedPoints = floor($totalAfterPoint * 0.01); // 1% of totalAfterPoint

        if ($member) {
            // Deduct used points
            if ($usedPoint > 0 && $member->points >= $usedPoint) {
                $member->points -= $usedPoint;
            }

            // Add earned points
            $member->points += $earnedPoints;
            $member->save();
        }
        // Hapus data session setelah transaksi
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
        $purchase->save();

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

}
