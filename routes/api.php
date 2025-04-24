<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::prefix('v1')->group(function () {
    // Autenticación
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    // Productos
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    
    // Categorías
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    
    // Marcas
    Route::get('brands', [BrandController::class, 'index']);
    Route::get('brands/{brand}', [BrandController::class, 'show']);
});

// Rutas protegidas (requieren autenticación)
Route::prefix('v1')->middleware(['auth:sanctum', 'api.throttle'])->group(function () {
    // Perfil y autenticación
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Carrito
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'addItem']);
    Route::put('cart/update', [CartController::class, 'updateItem']);
    Route::delete('cart/remove', [CartController::class, 'removeItem']);
    Route::post('cart/clear', [CartController::class, 'clear']);
    
    // Órdenes
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    
    // Rutas protegidas por rol (admin)
    Route::middleware(['role:admin,super-admin'])->group(function () {
        // Gestión de productos
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
        
        // Gestión de usuarios
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{user}', [UserController::class, 'show']);
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
        
        // Gestión de categorías
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        
        // Gestión de marcas
        Route::post('brands', [BrandController::class, 'store']);
        Route::put('brands/{brand}', [BrandController::class, 'update']);
        Route::delete('brands/{brand}', [BrandController::class, 'destroy']);
    });
});

// Rutas de webhooks
Route::prefix('v1/webhooks')->group(function () {
    Route::post('payment', [WebhookController::class, 'paymentWebhook']);
    Route::post('inventory', [WebhookController::class, 'inventoryWebhook']);
});
