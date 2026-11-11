
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::apiResource('products', ProductController::class);

Route::apiResource('categories', CategoryController::class);


use App\Http\Controllers\ClientController;

Route::apiResource('clients', ClientController::class);


use App\Http\Controllers\OrderController;

Route::apiResource('orders', OrderController::class);