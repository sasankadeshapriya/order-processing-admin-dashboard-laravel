<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MapController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/error', function () {
    return view('pages.error');
});

// Routes for managing products
Route::get('/product', [ProductController::class, 'showData'])->name('product.manage');
Route::delete('/product/{id}', [ProductController::class, 'deleteProduct'])->name('product.delete');
Route::get('/add-product', [ProductController::class, 'addProductForm'])->name('product.add');
Route::post('/add-product', [ProductController::class, 'submitProduct'])->name('product.submit');
Route::get('/product/edit/{id}', [ProductController::class, 'editProductForm'])->name('product.edit');
Route::put('/product/update/{id}', [ProductController::class, 'updateProduct'])->name('product.update');

// Routes for managing vehicles
Route::get('/vehicle', [VehicleController::class, 'showData'])->name('vehicle.manage');
Route::delete('/vehicle/{id}', [VehicleController::class, 'deleteVehicle'])->name('vehicle.delete');
Route::get('/add-vehicle', [VehicleController::class, 'addVehicleForm'])->name('vehicle.add');
Route::post('/add-vehicle', [VehicleController::class, 'submitVehicle'])->name('vehicle.submit');
Route::get('/vehicle/edit/{id}', [VehicleController::class, 'editVehicleForm'])->name('vehicle.edit');
Route::put('/vehicle/update/{id}', [VehicleController::class, 'updateVehicle'])->name('vehicle.update');

// Web routes in Laravel typically found in routes/web.php
Route::view('/map', 'pages.maps.index')->name('map');
Route::post('/submit.route', [MapController::class, 'submitRoute'])->name('submit.route');


Route::get('/route', [MapController::class, 'showData'])->name('route.manage');
Route::get('/route/edit/{id}', [MapController::class, 'editRouteForm'])->name('route.edit');
Route::put('/route/update/{id}', [MapController::class, 'updateRoute'])->name('route.update');
Route::delete('/route/{id}', [MapController::class, 'deleteRoute'])->name('route.delete');


