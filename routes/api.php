<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
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
Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login')
    ->middleware('throttle:login');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('api.health')->middleware('throttle:health');

// Rutas protegidas por autenticación de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    /*
    |--------------------------------------------------------------------------
    | Contacts
    |--------------------------------------------------------------------------
    */
    Route::prefix('contacts')->group(function () {
        Route::get('filters', [ContactController::class, 'getFilters']);
        Route::get('export', [ContactController::class, 'exportContacts']);
        Route::patch('{contact}/restore', [ContactController::class, 'restore']);
    });
    Route::apiResource('contacts', ContactController::class);

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->group(function () {
        Route::get('filters', [ProductController::class, 'getFilters']);
        Route::get('export-catalog', [ProductController::class, 'exportCatalog']);
        Route::patch('{product}/restore', [ProductController::class, 'restore']);
    });
    Route::apiResource('products', ProductController::class);

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->group(function () {
        Route::get('filters', [OrderController::class, 'getFilters']);

        Route::get('create', [OrderController::class, 'create']);
        Route::get('{order}/edit', [OrderController::class, 'edit']);

        Route::get('{order}/details', [OrderController::class, 'getOrderDetails']);
        Route::get('{order}/stock-movements', [OrderController::class, 'getStockMovements']);

        Route::get('{order}/export-ticket', [OrderExportController::class, 'exportOrderToExcel']);
        Route::get('{order}/check-exportable', [OrderExportController::class, 'checkOrderExportability']);
    });
    Route::apiResource('orders', OrderController::class);

    /*
    |--------------------------------------------------------------------------
    | Order details
    |--------------------------------------------------------------------------
    */
    // Route::apiResource('order-details', OrderDetailController::class);

    /*
    |--------------------------------------------------------------------------
    | Stock Movements
    |--------------------------------------------------------------------------
    */
    Route::prefix('stock-movements')->group(function () {
        Route::get('filters', [StockMovementController::class, 'getFilters']);
    });
    Route::apiResource('stock-movements', StockMovementController::class)
        ->only(['index', 'show']);
});