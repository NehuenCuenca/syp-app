<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
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
        Route::get('/types', [ContactController::class, 'getContactsTypes'])->name('contacts.types');
    });
    Route::apiResource('contacts', ContactController::class);

    Route::prefix('products')->group(function () {
        Route::patch('/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/filters', [ProductController::class, 'getFilters'])
        ->name('orders.filters');
        Route::get('/filtered', [ProductController::class, 'getFilteredProducts'])->name('products.filtered');
        Route::get('/stats', [ProductController::class, 'getStats'])->name('products.stats');
    });
    Route::apiResource('products', ProductController::class);

    // Ruta adicional para filtros y ordenamiento
    Route::get('/orders/filters', [OrderController::class, 'getFilters'])
        ->name('orders.filters');
        
    Route::get('/orders/filtered', [OrderController::class, 'getFilteredOrders'])
        ->name('orders.filtered');

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
});