<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\VehicleInventoryController;

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


// Web routes in Laravel typically found in routes/web.php
Route::view('/map', 'pages.maps.index')->name('map');
Route::post('/submit.route', [MapController::class, 'submitRoute'])->name('submit.route');
Route::get('/route', [MapController::class, 'showData'])->name('route.manage');
Route::get('/route/edit/{id}', [MapController::class, 'editRouteForm'])->name('route.edit');
Route::put('/route/update/{id}', [MapController::class, 'updateRoute'])->name('route.update');
Route::delete('/route/{id}', [MapController::class, 'deleteRoute'])->name('route.delete');


//vehicle inventory
Route::get('/vehicle-inventory', [VehicleInventoryController::class, 'showVehicleInventory'])->name('vehicle.inventory');
Route::delete('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'delete'])->name('vehicle-inventory.delete');
Route::get('/add-vehicle-inventory', [VehicleInventoryController::class, 'addVehicleInventoryForm'])->name('vehicle-inventory.add');
Route::post('/add-vehicle-inventory', [VehicleInventoryController::class, 'submitVehicleInventory'])->name('vehicle-inventory.submit');
Route::get('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'editVehicleInventoryForm'])->name('vehicle-inventory.edit');
Route::put('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'updateVehicleInventory'])->name('vehicle-inventory.update');
