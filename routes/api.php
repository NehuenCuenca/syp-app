<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OrderExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación abiertas
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rutas protegidas por autenticación de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    Route::prefix('contacts')->group(function () {
        Route::patch('/{id}/restore', [ContactController::class, 'restore'])->name('contacts.restore');
        Route::get('/filtered', [ContactController::class, 'getFilteredContacts'])->name('contacts.filtered');
        Route::get('/filters', [ContactController::class, 'getFilters'])->name('contacts.filters');
    });
    Route::apiResource('contacts', ContactController::class);

    Route::prefix('products')->group(function () {
        Route::patch('/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/filters', [ProductController::class, 'getFilters'])->name('orders.filters');
        Route::get('/filtered', [ProductController::class, 'getFilteredProducts'])->name('products.filtered');
    });
    Route::apiResource('products', ProductController::class);

    // Ruta adicional para filtros y ordenamiento
    Route::get('/orders/filters', [OrderController::class, 'getFilters'])
        ->name('orders.filters');
        
    Route::get('/orders/filtered', [OrderController::class, 'getFilteredOrders'])
        ->name('orders.filtered');

    // Exportar pedido a Excel
    Route::get('/orders/{order}/export-excel', [OrderExportController::class, 'exportOrderToExcel'])
        ->name('orders.export.excel')
        ->where('order.id', '[0-9]+');
    
    // Verificar si un pedido es exportable
    Route::get('/orders/{order}/check-exportable', [OrderExportController::class, 'checkOrderExportability'])
        ->name('orders.check.exportable')
        ->where('order.id', '[0-9]+');

    // Make a route to get the order details
    Route::get('/orders/{order}/details', [OrderController::class, 'getOrderDetails'])
    ->name('orders.details');
    
    // Make a route to get the stock movements
    Route::get('/orders/{order}/stock-movements', [OrderController::class, 'getStockMovements'])
    ->name('orders.stock-movements');

    // Rutas resource para Orders
    Route::resource('orders', OrderController::class);

    Route::resource('order-details', OrderDetailController::class, [
        'names' => [
            'index' => 'order-details.index',
            'create' => 'order-details.create',
            'store' => 'order-details.store',
            'show' => 'order-details.show',
            'edit' => 'order-details.edit',
            'update' => 'order-details.update',
            'destroy' => 'order-details.destroy'
        ]
    ]);

    Route::get('/stock-movements/filters', [StockMovementController::class, 'getFilters'])
        ->name('stock-movements.filters');
        
    Route::get('/stock-movements/filtered', [StockMovementController::class, 'getFilteredMovements'])
        ->name('stock-movements.filtered');

    Route::resource('stock-movements', StockMovementController::class, [
        'names' => [
            'index' => 'stock-movements.index',
            'create' => 'stock-movements.create',
            'store' => 'stock-movements.store',
            'show' => 'stock-movements.show',
            'edit' => 'stock-movements.edit',
            'update' => 'stock-movements.update',
            'destroy' => 'stock-movements.destroy',
        ]
    ]);
});