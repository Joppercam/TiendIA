<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\HomeController;

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


require __DIR__.'/auth.php';
