<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VehicleController;

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