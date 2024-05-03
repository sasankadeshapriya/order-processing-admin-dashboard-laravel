<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\VehicleController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/error', function () {
    return view('pages.error');
});


//product route
Route::get('/product', [ProductController::class, 'showData'])->name('product.manage');
Route::delete('/product/{id}', [ProductController::class, 'deleteProduct'])->name('product.delete');
Route::get('/add-product', [ProductController::class, 'addProductForm'])->name('product.add');
Route::post('/add-product', [ProductController::class, 'submitProduct'])->name('product.submit');
Route::get('/product/edit/{id}', [ProductController::class, 'editProductForm'])->name('product.edit');
Route::put('/product/update/{id}', [ProductController::class, 'updateProduct'])->name('product.update');

//batch route
Route::get('/batch', [BatchController::class, 'showData'])->name('batch.manage');
Route::delete('/batch/{id}', [BatchController::class, 'deleteBatch'])->name('batch.delete');
Route::get('/add-batch', [BatchController::class, 'addBatchForm'])->name('batch.add');
Route::post('/add-batch', [BatchController::class, 'submitBatch'])->name('batch.submit');
Route::get('/batch/edit/{id}', [BatchController::class, 'editBatchForm'])->name('batch.edit');
Route::put('/batch/update/{id}', [BatchController::class, 'updateBatch'])->name('batch.update');

// Routes for managing vehicles
Route::get('/vehicle', [VehicleController::class, 'showData'])->name('vehicle.manage');
Route::delete('/vehicle/{id}', [VehicleController::class, 'deleteVehicle'])->name('vehicle.delete');
Route::get('/add-vehicle', [VehicleController::class, 'addVehicleForm'])->name('vehicle.add');
Route::post('/add-vehicle', [VehicleController::class, 'submitVehicle'])->name('vehicle.submit');
Route::get('/vehicle/edit/{id}', [VehicleController::class, 'editVehicleForm'])->name('vehicle.edit');
Route::put('/vehicle/update/{id}', [VehicleController::class, 'updateVehicle'])->name('vehicle.update');

