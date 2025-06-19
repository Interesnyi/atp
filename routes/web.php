<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PurchasesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/warehouses/suppliers/view/{id}', 'WarehousesController@viewSupplier');
Route::get('/warehouses/suppliers/edit/{id}', 'WarehousesController@editSupplier');

Route::get('/warehouses/items', 'WarehousesController@items');
Route::get('/warehouses/items/create', 'WarehousesController@createItem');
Route::post('/warehouses/items/store', 'WarehousesController@storeItem');
Route::get('/warehouses/items/edit/{id}', 'WarehousesController@editItem');
Route::post('/warehouses/items/update/{id}', 'WarehousesController@updateItem');
Route::post('/warehouses/items/delete/{id}', 'WarehousesController@deleteItem');

// Заказ-наряды (ремонт автомобилей)
Route::get('orders', 'OrdersController@index');
Route::get('orders/create', 'OrdersController@create');
Route::post('orders/store', 'OrdersController@store');
Route::get('orders/view/{id}', 'OrdersController@view');
Route::get('orders/edit/{id}', 'OrdersController@edit');
Route::post('orders/update/{id}', 'OrdersController@update');
Route::post('orders/delete/{id}', 'OrdersController@delete');

// Закупки
Route::get('/purchases', 'PurchasesController@index');
Route::get('/purchases/create', 'PurchasesController@create');
Route::post('/purchases/store', 'PurchasesController@store');
Route::get('/purchases/edit/{id}', 'PurchasesController@edit');
Route::post('/purchases/update/{id}', 'PurchasesController@update');
Route::post('/purchases/delete/{id}', 'PurchasesController@delete');
Route::post('/purchases/add-item/{id}', 'PurchasesController@addItem');

require __DIR__.'/auth.php';