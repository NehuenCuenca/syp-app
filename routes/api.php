<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
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


    Route::apiResource('contacts', ContactController::class);
    Route::patch('contacts/{id}/restore', [ContactController::class, 'restore']);
    Route::get('contacts-filtered', [ContactController::class, 'getFilteredContacts']);
    Route::get('contacts-types', [ContactController::class, 'getContactsTypes']);


    Route::apiResource('products', ProductController::class);
    Route::patch('products/{id}/restore', [ProductController::class, 'restore']);
    Route::get('products-filtered', [ProductController::class, 'getFilteredProducts']);
    Route::get('products-categories', [ProductController::class, 'getCategories']);
    Route::get('products-stats', [ProductController::class, 'getStats']);
});