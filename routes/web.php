<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;



// Checkout Routes
Route::prefix('checkout')->name('checkout.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/address', [CheckoutController::class, 'address'])->name('address');
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('process-payment');
    Route::get('/review', [CheckoutController::class, 'review'])->name('review');
    Route::post('/complete', [CheckoutController::class, 'complete'])->name('complete');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Guest Checkout Route
Route::get('/checkout/guest', [CheckoutController::class, 'guest'])->name('checkout.guest');

// Address Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('addresses', AddressController::class);
});


// Rutas del carrito de compras
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::put('/update', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/save-for-later', [CartController::class, 'saveForLater'])->name('cart.save-for-later');
});

// Ruta principal
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/test', function () {
    return 'La ruta de prueba funciona correctamente';
});

// En routes/web.php
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas del carrito de compras
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\Cart\CartController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\Cart\CartController::class, 'add'])->name('add');
    Route::post('/update', [App\Http\Controllers\Cart\CartController::class, 'update'])->name('update');
    Route::post('/remove', [App\Http\Controllers\Cart\CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [App\Http\Controllers\Cart\CartController::class, 'clear'])->name('clear');
});

// Rutas públicas para la tienda
Route::get('/products', [ShopProductController::class, 'index'])->name('shop.products.index');
Route::get('/products/{slug}', [ShopProductController::class, 'show'])->name('shop.products.show');
Route::get('/category/{slug}', [ShopProductController::class, 'byCategory'])->name('shop.products.category');
Route::get('/brand/{slug}', [ShopProductController::class, 'byBrand'])->name('shop.products.brand');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de la lista de deseos
Route::prefix('wishlist')->middleware('auth')->group(function () {
    Route::get('/', [WishListController::class, 'index'])->name('wishlist.index');
    Route::post('/add', [WishListController::class, 'addItem'])->name('wishlist.add');
    Route::delete('/remove', [WishListController::class, 'removeItem'])->name('wishlist.remove');
    Route::post('/move-to-cart', [WishListController::class, 'moveToCart'])->name('wishlist.move-to-cart');
});

// Rutas de administración
Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rutas de categorías
    Route::resource('categories', CategoryController::class);
    
    // Rutas de marcas
    Route::resource('brands', BrandController::class);
    
    // Rutas de productos
    Route::resource('products', ProductController::class);
});

// Rutas de administración para inventario
Route::middleware(['auth', 'role:admin|super-admin|editor'])->prefix('admin')->name('admin.')->group(function () {
    // Rutas de inventario
    Route::resource('inventory', App\Http\Controllers\Admin\InventoryController::class)->except(['create', 'store', 'destroy']);
    
    // Rutas de movimientos de inventario
    Route::resource('inventory-movements', App\Http\Controllers\Admin\InventoryMovementController::class)->only(['index', 'show', 'create', 'store']);
    
    // Rutas de proveedores
    Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class);
});

Route::get('admin/inventory-dashboard', [App\Http\Controllers\Admin\InventoryDashboardController::class, 'index'])
    ->name('admin.inventory.dashboard');


    // Rutas de administración
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super-admin'])->group(function () {
        // Rutas de Gestión de Pedidos
        Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::put('orders/{order}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('orders/{order}/generate-invoice', [App\Http\Controllers\Admin\OrderController::class, 'generateInvoice'])->name('orders.generate-invoice');
        
        // Rutas de Gestión de Envíos
        Route::resource('shipments', App\Http\Controllers\Admin\ShipmentController::class)->except(['destroy']);
        Route::get('orders/{order}/shipments/create', [App\Http\Controllers\Admin\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('orders/{order}/shipments', [App\Http\Controllers\Admin\ShipmentController::class, 'store'])->name('shipments.store');
        Route::put('shipments/{shipment}/mark-as-delivered', [App\Http\Controllers\Admin\ShipmentController::class, 'markAsDelivered'])->name('shipments.mark-as-delivered');
        Route::post('shipments/{shipment}/tracking-update', [App\Http\Controllers\Admin\ShipmentController::class, 'addTrackingUpdate'])->name('shipments.tracking-update');
        Route::put('shipments/{shipment}/update-tracking', [App\Http\Controllers\Admin\ShipmentController::class, 'updateTracking'])->name('shipments.update-tracking');
        Route::get('shipments/{shipment}/print-label', [App\Http\Controllers\Admin\ShipmentController::class, 'printLabel'])->name('shipments.print-label');
    });
    
    // Rutas de tienda para clientes
    Route::prefix('account')->name('shop.')->middleware(['auth'])->group(function () {
        // Rutas de Pedidos para Clientes
        Route::get('orders', [App\Http\Controllers\Shop\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [App\Http\Controllers\Shop\OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/track', [App\Http\Controllers\Shop\OrderController::class, 'track'])->name('orders.track');
        Route::get('orders/{order}/review', [App\Http\Controllers\Shop\OrderController::class, 'review'])->name('orders.review');
        Route::post('orders/{order}/review', [App\Http\Controllers\Shop\OrderController::class, 'storeReview'])->name('orders.store-review');
        Route::put('orders/{order}/cancel', [App\Http\Controllers\Shop\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/request-return', [App\Http\Controllers\Shop\OrderController::class, 'requestReturn'])->name('orders.request-return');
        // routes/web.php (continuación)
    Route::get('orders/{order}/invoice', [App\Http\Controllers\Shop\OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
    Route::post('orders/{order}/reorder', [App\Http\Controllers\Shop\OrderController::class, 'reorder'])->name('orders.reorder');
});


require __DIR__.'/auth.php';
