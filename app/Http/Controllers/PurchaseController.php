<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'user_id' => auth()->id() ?? 1,
                    'purchase_number' => 'PO-' . strtoupper(uniqid()),
                    'total' => 0,
                    'status' => 'completed',
                    'purchased_at' => now(),
                ]);

                $total = 0;
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];
                    $unit_cost = (float) $item['unit_cost'];
                    $subtotal = $unit_cost * $qty;

                    $purchase->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $unit_cost,
                        'subtotal' => $subtotal,
                    ]);

                    $product->increment('stock', $qty); // Tambah stok
                    $total += $subtotal;
                }
                $purchase->update(['total' => $total]);
            });

            return redirect()->back()->with('success', 'Barang Masuk berhasil dicatat dan stok bertambah!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem (Rollback): ' . $e->getMessage());
        }
    }
}
