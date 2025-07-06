<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('products', ProductController::class);
Route::post('products/{id}/restore', [ProductController::class, 'restore']);
Route::get('products-filtered', [ProductController::class, 'getFilteredProducts']);
Route::get('products-categories', [ProductController::class, 'getCategories']);
Route::get('products-stats', [ProductController::class, 'getStats']);
