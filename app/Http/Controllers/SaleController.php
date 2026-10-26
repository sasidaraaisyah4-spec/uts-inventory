<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // Menampilkan daftar penjualan khusus transaksi oleh customer
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sales = Sale::whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('sale_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($sub) use ($search) {
                          $sub->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('items.product', function ($sub) use ($search) {
                          $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('sold_at', '<=', $endDate);
            })
            ->with('items.product', 'user')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    // Menyimpan transaksi penjualan dengan Database Transaction & Validasi Stok
    public function store(Request $request)
    {
        // Validasi input dari user
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Gunakan Database Transaction agar atomik (rollback jika gagal)
            DB::transaction(function () use ($request) {
                
                // Buat nomor penjualan unik
                $saleNumber = 'SALE-' . date('Ymd') . '-' . rand(1000, 9999);

                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'sale_number' => $saleNumber,
                    'total' => 0,
                    'status' => 'completed',
                    'sold_at' => now(),
                ]);

                $total = 0;

                foreach ($request->items as $itemData) {
                    // Ambil produk dan kunci barisnya (lockForUpdate) untuk mencegah race condition
                    $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                    $qty = (int) $itemData['quantity'];

                    // Business Rule: Periksa apakah stok cukup
                    if ($product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'items' => "Stok untuk produk '{$product->name}' tidak mencukupi! Sisa stok: {$product->stock}.",
                        ]);
                    }

                    $subtotal = $product->selling_price * $qty;

                    // Simpan detail item penjualan
                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Kurangi stok produk secara otomatis
                    $product->decrement('stock', $qty);

                    $total += $subtotal;
                }

                // Update total keseluruhan transaksi di server
                $sale->update(['total' => $total]);
            });

            return redirect()->back()->with('success', 'Transaksi penjualan berhasil disimpan dan stok telah diperbarui!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}