<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleItem;

// ==========================================
// RUTE LOGIN & LOGOUT (PUBLIK)
// ==========================================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// RUTE SISTEM (WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // 1. DASHBOARD UTAMA (ADMIN, STAFF, CUSTOMER)
    Route::get('/', function (Request $request) {
        $user = auth()->user();
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
                    return $query->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0);
                } elseif ($stockStatus === 'out') {
                    return $query->where('stock', '<=', 0);
                } elseif ($stockStatus === 'safe') {
                    return $query->whereColumn('stock', '>', 'minimum_stock');
                }
                return $query;
            })
            ->latest()
            ->paginate(8, ['*'], 'products_page')
            ->withQueryString();

        // Data statistik operasional (Admin & Staff) - khusus transaksi customer aktif
        $customerSalesQuery = Sale::whereHas('user', function ($q) {
            $q->where('role', 'customer');
        });

        $totalSales = (clone $customerSalesQuery)->sum('total');
        $newSalesCount = (clone $customerSalesQuery)->count();
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'minimum_stock')->get();
        $lowStockCount = $lowStockProducts->count();

        // Data statistik & pesanan khusus role Customer
        $customerOrders = collect();
        $custTotalOrders = 0;
        $custTotalSpent = 0;
        $custReceivedCount = 0;

        if ($user && $user->role === 'customer') {
            $orderSearch = $request->input('order_search');
            $orderStatus = $request->input('order_status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $customerOrders = Sale::where('user_id', $user->id)
                ->when($orderSearch, function ($query, $orderSearch) {
                    return $query->where(function ($q) use ($orderSearch) {
                        $q->where('sale_number', 'like', "%{$orderSearch}%")
                          ->orWhereHas('items.product', function ($sub) use ($orderSearch) {
                              $sub->where('name', 'like', "%{$orderSearch}%");
                          });
                    });
                })
                ->when($orderStatus, function ($query, $orderStatus) {
                    return $query->where('status', $orderStatus);
                })
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('sold_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('sold_at', '<=', $endDate);
                })
                ->with('items.product')
                ->latest()
                ->paginate(6, ['*'], 'orders_page')
                ->withQueryString();

            $custTotalOrders = Sale::where('user_id', $user->id)->count();
            $custTotalSpent = Sale::where('user_id', $user->id)->sum('total');
            $custReceivedCount = Sale::where('user_id', $user->id)->where('status', 'completed')->count();
        }

        // Data Kategori Terlaris - dihitung dinamis dari item transaksi customer aktif
        $topCategories = Category::withCount('products')
            ->get()
            ->map(function ($cat) {
                $soldQty = SaleItem::whereHas('product', function ($q) use ($cat) {
                        $q->where('category_id', $cat->id);
                    })
                    ->whereHas('sale.user', function ($q) {
                        $q->where('role', 'customer');
                    })
                    ->sum('quantity');

                return [
                    'name' => $cat->name,
                    'products_count' => $cat->products_count,
                    'sold_count' => $soldQty,
                ];
            })
            ->sortByDesc('sold_count')
            ->values();

        return view('dashboard', compact(
            'products',
            'categories',
            'totalSales',
            'newSalesCount',
            'totalProducts',
            'totalSuppliers',
            'lowStockCount',
            'lowStockProducts',
            'topCategories',
            'customerOrders',
            'custTotalOrders',
            'custTotalSpent',
            'custReceivedCount'
        ));
    })->name('dashboard');

    // 2. KATALOG PRODUK & DETAIL (Dapat Dilihat oleh Admin & Staff)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // ========================================================
    // RUTE KHUSUS ADMINISTRATOR (ROLE: ADMIN ONLY)
    // Staff & Customer DILARANG MENGAKSES DATA MASTER BERIKUT
    // ========================================================
    Route::middleware(['role:admin'])->group(function () {

        // D. TAMBAH, EDIT, UPDATE, & HAPUS PRODUK
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // A. DATA MASTER KATEGORI (CATEGORIES)
        Route::get('/categories', function () {
            $categories = Category::withCount('products')->get();
            return view('categories.index', compact('categories'));
        })->name('categories.index');

        Route::post('/categories', function (Request $request) {
            $request->validate(['name' => 'required|string|unique:categories,name|max:255']);
            Category::create(['name' => $request->name]);
            return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
        })->name('categories.store');

        Route::put('/categories/{id}', function (Request $request, $id) {
            $category = Category::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            ]);
            $category->update(['name' => $request->name]);
            return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
        })->name('categories.update');

        Route::delete('/categories/{id}', function ($id) {
            $category = Category::withCount('products')->findOrFail($id);
            if ($category->products_count > 0) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait!');
            }
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
        })->name('categories.destroy');

        // B. DATA MASTER PEMASOK (SUPPLIERS)
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // C. TRANSAKSI MASUK / PEMBELIAN DARI SUPPLIER (RESTOCK)
        Route::get('/purchases', function () {
            $purchases = Purchase::with('items.product', 'supplier', 'user')->latest()->paginate(10)->withQueryString();
            $suppliers = Supplier::all();
            $products = Product::all();
            return view('purchases.index', compact('purchases', 'suppliers', 'products'));
        })->name('purchases.index');

        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    });

    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // 3. TRANSAKSI PENJUALAN / KASIR (Admin & Staff)
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', function () {
            return view('sales.create');
        })->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // 4. CHECKOUT & PEMBELIAN (Khusus Customer)
    Route::get('/checkout/{id}', function ($id) {
        $product = Product::with('category')->findOrFail($id);
        return view('checkout', compact('product'));
    })->name('checkout.show');

    Route::post('/checkout', function (Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            $qty = (int) $request->quantity;

            if ($product->stock < $qty) {
                return redirect()->route('checkout.show', $product->id)->with('error', "Stok untuk produk {$product->name} tidak mencukupi! Sisa stok: {$product->stock}.");
            }

            $saleNumber = 'ORDER-' . date('Ymd') . '-' . rand(1000, 9999);
            $subtotal = $product->selling_price * $qty;

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => $saleNumber,
                'total' => $subtotal,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $sale->items()->create([
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $product->selling_price,
                'subtotal' => $subtotal,
            ]);

            $product->decrement('stock', $qty);

            return redirect()->route('dashboard')->with('success', "Pesanan #{$saleNumber} berhasil! Anda membeli {$qty} unit '{$product->name}'. Stok fisik otomatis diperbarui.");
        });
    })->name('checkout.process');

    // 5. NOTA TRANSAKSI
    Route::get('/invoice/{id}', function ($id) {
        $user = auth()->user();
        $sale = Sale::with('items.product', 'user')->findOrFail($id);

        if ($user->role === 'customer' && $sale->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat nota ini.');
        }

        return view('invoices.show', compact('sale'));
    })->name('invoice.show');

    // 6. PENGATURAN (Admin & Staff)
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');



});
