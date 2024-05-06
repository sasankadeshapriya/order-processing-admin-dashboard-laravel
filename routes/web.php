<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AssignmentController;

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


//Routes for managing routes
Route::view('/map', 'pages.maps.index')->name('map');
Route::post('/route/store', [MapController::class, 'submitRoute'])->name('route.store');
Route::get('/route', [MapController::class, 'showData'])->name('route.manage');
Route::get('/route/edit/{id}', [MapController::class, 'editRouteForm'])->name('route.edit');
Route::put('/route/update/{id}', [MapController::class, 'updateRoute'])->name('route.update');
Route::delete('/route/{id}', [MapController::class, 'deleteRoute'])->name('route.delete');

//Routes for managing Assignments
Route::get('/assignment', [AssignmentController::class, 'showAssignments'])->name('assignment.manage');
Route::get('/add-assignment', [AssignmentController::class, 'addAssignmentForm'])->name('assignment.add');
Route::post('/add-assignment', [AssignmentController::class, 'submitAssignment'])->name('assignment.submit');
Route::get('/assignment/edit/{id}', [AssignmentController::class, 'editAssignmentForm'])->name('assignment.edit');
Route::put('/assignment/edit/{id}', [AssignmentController::class, 'updateAssignment'])->name('assignment.update');
Route::delete('/assignment/{id}', [AssignmentController::class, 'deleteAssignment'])->name('assignment.delete');

