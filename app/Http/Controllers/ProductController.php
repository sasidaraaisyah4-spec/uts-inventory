<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // Menampilkan daftar katalog produk dengan filter dan pagination
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'customer') {
            return redirect()->route('dashboard')->with('error', 'Akses terbatas untuk pelanggan.');
        }

        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('stock_status');

        $categories = Category::all();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($sub) use ($search) {
                          $sub->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($stockStatus, function ($query, $stockStatus) {
                if ($stockStatus === 'low') {
                    // Stok kritis: stok <= minimum_stock dan stok > 0
                    return $query->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0);
                } elseif ($stockStatus === 'out') {
                    // Habis
                    return $query->where('stock', '<=', 0);
                } elseif ($stockStatus === 'safe') {
                    // Aman
                    return $query->whereColumn('stock', '>', 'minimum_stock');
                }
                return $query;
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    // Menampilkan form tambah produk
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Menyimpan data produk baru ke database
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'sku' => 'required|string|max:50|unique:products,sku',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        // Simpan hanya nama filenya saja agar konsisten
        $data['image'] = $filename;
    }

    Product::create($data);

    return redirect('/')->with('success', 'Produk mainan berhasil ditambahkan!');
}
    // Menampilkan detail lengkap produk (stok, harga, riwayat penjualan & pembelian)
    public function show($id)
    {
        $product = Product::with(['category'])->findOrFail($id);

        // Riwayat Penjualan (Sales) yang memuat produk ini
        $saleItems = \App\Models\SaleItem::with(['sale.user'])
            ->where('product_id', $id)
            ->whereHas('sale')
            ->latest()
            ->paginate(10, ['*'], 'sales_page');

        // Riwayat Pembelian / Restock (Purchases) yang memuat produk ini
        $purchaseItems = \App\Models\PurchaseItem::with(['purchase.supplier', 'purchase.user'])
            ->where('product_id', $id)
            ->whereHas('purchase')
            ->latest()
            ->paginate(10, ['*'], 'purchases_page');

        // Statistik ringkasan produk
        $totalSold = \App\Models\SaleItem::where('product_id', $id)->sum('quantity');
        $totalPurchased = \App\Models\PurchaseItem::where('product_id', $id)->sum('quantity');
        $totalRevenue = \App\Models\SaleItem::where('product_id', $id)->sum('subtotal');

        return view('products.show', compact('product', 'saleItems', 'purchaseItems', 'totalSold', 'totalPurchased', 'totalRevenue'));
    }

    // Menampilkan form edit produk
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Memperbarui data produk ke database
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $filename);
            $data['image'] = 'uploads/products/' . $filename;
        }

        $product->update($data);

        return redirect('/')->with('success', 'Produk berhasil diperbarui!');
    }

    // Menghapus produk
    public function destroy($id)
{
    $product = Product::findOrFail($id);
    
    // Hapus data relasi terkait di tabel transaksi terlebih dahulu
    \DB::table('sale_items')->where('product_id', $id)->delete();
    \DB::table('purchase_items')->where('product_id', $id)->delete();

    // Hapus file gambar jika ada
    if ($product->image && file_exists(public_path('uploads/' . basename($product->image)))) {
        unlink(public_path('uploads/' . basename($product->image)));
    }

    // Hapus produk
    $product->delete();

    return redirect('/')->with('success', 'Produk berhasil dihapus!');
}
}