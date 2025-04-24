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
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagerController;
use App\Http\Controllers\Admin\ReportController;


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


// En routes/web.php

// Rutas públicas para reseñas
Route::group(['prefix' => 'products/{product}'], function () {
    // Reseñas
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('products.reviews.index');
    Route::get('/reviews/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('products.reviews.create');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('products.reviews.store');
    Route::get('/reviews/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('products.reviews.edit');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('products.reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('products.reviews.destroy');
    Route::post('/rate', [App\Http\Controllers\ReviewController::class, 'rateProduct'])->name('products.rate');
    
    // Preguntas y respuestas
    Route::get('/questions', [App\Http\Controllers\QuestionController::class, 'index'])->name('products.questions.index');
    Route::post('/questions', [App\Http\Controllers\QuestionController::class, 'store'])->name('products.questions.store');
    Route::get('/questions/{question}/edit', [App\Http\Controllers\QuestionController::class, 'edit'])->name('products.questions.edit');
    Route::put('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'update'])->name('products.questions.update');
    Route::delete('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'destroy'])->name('products.questions.destroy');
    Route::post('/questions/{question}/answers', [App\Http\Controllers\QuestionController::class, 'storeAnswer'])->name('products.questions.answers.store');
    Route::get('/questions/{question}/answers/{answer}/edit', [App\Http\Controllers\QuestionController::class, 'editAnswer'])->name('products.questions.answers.edit');
    Route::put('/questions/{question}/answers/{answer}', [App\Http\Controllers\QuestionController::class, 'updateAnswer'])->name('products.questions.answers.update');
    Route::delete('/questions/{question}/answers/{answer}', [App\Http\Controllers\QuestionController::class, 'destroyAnswer'])->name('products.questions.answers.destroy');
});

// Rutas de administración
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin|editor'], 'as' => 'admin.'], function () {
    // Dashboard de reseñas
    Route::get('/reviews/dashboard', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'dashboard'])->name('reviews.dashboard');
    
    // Gestión de reseñas
    Route::get('/reviews/pending', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'pendingReviews'])->name('reviews.pending');
    Route::patch('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'approveReview'])->name('reviews.approve');
    Route::delete('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'rejectReview'])->name('reviews.reject');
    Route::patch('/reviews/{review}/toggle-feature', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'toggleFeatureReview'])->name('reviews.toggle-feature');
    
    // Gestión de preguntas
    Route::get('/questions/pending', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'pendingQuestions'])->name('questions.pending');
    Route::patch('/questions/{question}/approve', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'approveQuestion'])->name('questions.approve');
    Route::delete('/questions/{question}/reject', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'rejectQuestion'])->name('questions.reject');
    
    // Gestión de respuestas
    Route::get('/answers/pending', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'pendingAnswers'])->name('answers.pending');
    Route::patch('/answers/{answer}/approve', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'approveAnswer'])->name('answers.approve');
    Route::delete('/answers/{answer}/reject', [App\Http\Controllers\Admin\ReviewsManagementController::class, 'rejectAnswer'])->name('answers.reject');
});


// Rutas de administración agrupadas
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de usuarios
    Route::resource('users', UserManagerController::class);
    
    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    
    // Rutas para analíticas (opcional, usando AnalyticsService)
    Route::get('analytics/products/{product}', [ReportController::class, 'productAnalytics'])->name('analytics.product');
    Route::get('analytics/categories/{category?}', [ReportController::class, 'categoryAnalytics'])->name('analytics.category');
    Route::get('analytics/customers/{user?}', [ReportController::class, 'customerAnalytics'])->name('analytics.customer');
});


// routes/web.php

// Rutas de administración
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super-admin'])->group(function () {
    // Gestión de Cupones
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
    Route::get('coupons/generate-code', [\App\Http\Controllers\Admin\CouponController::class, 'generateCode'])->name('coupons.generate-code');
    
    // Gestión de Descuentos
    Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class);
    
    // Gestión de Campañas
    Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class);
    
    // Gestión de Promociones (implementaremos más tarde)
    Route::resource('promotions', \App\Http\Controllers\Admin\PromotionController::class);
});

// Rutas públicas para clientes
Route::prefix('shop')->name('shop.')->group(function () {
    // Aplicar/Eliminar cupones
    Route::post('coupon/apply', [\App\Http\Controllers\Shop\CouponController::class, 'apply'])->name('coupon.apply');
    Route::delete('coupon/remove', [\App\Http\Controllers\Shop\CouponController::class, 'remove'])->name('coupon.remove');
    
    // Campañas
    Route::get('campaigns', [\App\Http\Controllers\Shop\CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('campaigns/{slug}', [\App\Http\Controllers\Shop\CampaignController::class, 'show'])->name('campaigns.show');
});

require __DIR__.'/auth.php';
