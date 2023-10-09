<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;

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
Route::middleware(['auth.user'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/users', [UserController::class, 'list'])->name('users');
    Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/edit/{id}', [UserController::class, 'edit']);
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/delete/{id}', [UserController::class, 'delete']);
    Route::get('/items', [ItemController::class, 'index'])->name('items');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::get('/items/edit/{id}', [ItemController::class, 'edit']);
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/update', [ItemController::class, 'update'])->name('items.update');
    Route::get('/items/delete/{id}', [ItemController::class, 'delete']);
    Route::get('/items/generate_barcode/{id}', [ItemController::class,  'generate_barcode']);
    Route::get('/items/download_barcode/{id}', [ItemController::class, 'download_barcode']);
    Route::get('/items/print_barcode/{id}', [ItemController::class, 'print_barcode']);
    Route::get('/items/print_all_barcode', [ItemController::class, 'print_all_barcode']);
    Route::get('/sales', [SaleController::class, 'index'])->name('sales');
    Route::get('/generate_inv', [InvoiceController::class, 'invoice_number']);
    Route::get('/sales/load_sales', [SaleController::class, 'load_sales'])->name('sales.load_sales');
    Route::post('/sales/store_cart', [SaleController::class, 'store_cart'])->name('sales.store_cart');
    Route::post('/sales/add_cart',[SaleController::class, 'add_cart'])->name('sales.add_cart');
    Route::get('/sales/delete_cart/{id}', [SaleController::class, 'delete_cart']);
});

Route::get('/signin', [UserController::class, 'signin'])->name('signin');
Route::post('/signin', [UserController::class, 'authenticate'])->name('signin');
Route::get('/signup', [UserController::class, 'signup']);
Route::get('/logout', [UserController::class, 'logout'])->name('logout');
