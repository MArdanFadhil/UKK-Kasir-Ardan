<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(5);
        return view('product.index', compact('products'));
    }


    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'img' => 'required|mimes:jpg,jpeg,png|max:2048',
                'name_product' => 'required|string',
                'stock' => 'required|integer',
                'price' => 'required|integer',
            ]);

            if ($request->hasFile('img')) {
                $originalName = $request->file('img')->getClientOriginalName();
                $imgPath = $request->file('img')->storeAs('store/product/', $originalName, 'public');
                $imgName = basename($imgPath);
            } else {
                return redirect()->back()->with('error', 'Image Not Found');
            }

            Product::create([
                'img' => $imgName,
                'name_product' => $request->name_product,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

            return redirect()->route('product.index')->with('success', 'Successfully Add Data');
        } catch (\Exception $err) {
            return redirect()->back()->with('error', 'Failed To Save Product: ' . $err->getMessage());
        }
    }

        public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'img' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'name_product' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('img')) {
            if ($product->img && \Storage::disk('public')->exists('store/product/' . $product->img)) {
                \Storage::disk('public')->delete('store/product/' . $product->img);
            }

            $imgName = time() . '_' . $request->file('img')->getClientOriginalName();
            $request->file('img')->storeAs('store/product/', $imgName, 'public');
        } else {
            $imgName = $product->img;
        }

        $product->update([
            'img' => $imgName,
            'name_product' => $request->name_product,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('product.index')->with('success', 'Product Updated Successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'stock' => 'required|integer',
        ]);

        // Temukan produk berdasarkan ID
        $product = Product::find($id);

        // Cek jika produk tidak ditemukan
        if (!$product) {
            return redirect()->route('product.index')->with('error', 'Product not found.');
        }

        // Tambahkan stock yang baru
        $product->stock = $request->input('stock'); // Mengganti stock produk dengan nilai baru
        $product->save(); // Simpan perubahan ke database

        return redirect()->route('product.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $data = Product::findOrFail($id);
            $data->forceDelete();

            return redirect()->route('product.index')->with('success', 'Product Successfully Deleted');
        } catch (\Exception $err) {
            return redirect()->route('product.index')->with('error', 'Failed To Delete Product: ' . $err->getMessage());
        }
    }

}
