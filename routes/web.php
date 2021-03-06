<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductGalleryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MyTransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');

Route::get('/details/{slug}', [FrontendController::class, 'details'])->name('frontend.details');

Route::middleware(['auth:sanctum', 'verified'])->group(function (){

    Route::get('/carts', [FrontendController::class, 'carts'])->name('frontend.carts');

    // untuk membuat order cart
    Route::post('/carts/{id}', [FrontendController::class, 'cartAdd'])->name('cart-add');

    // Route untuk delete order cart
   Route::delete('/carts/{id}', [FrontendController::class, 'cartDelete'])->name('cart-delete');

    Route::post('/checkout', [FrontendController::class, 'checkout'])->name('frontend.checkout');

    Route::get('/checkout/success', [FrontendController::class, 'success'])->name('frontend.success');

    // untuk transaksi user
    Route::resource('my-transaction', MyTransactionController::class);

});



// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->name('dashboard.')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::resource('my-transaction', MyTransactionController::class);

    // hanya admin yang bisa akses
    Route::middleware(['admin'])->group(function() {
        Route::resource('products', ProductController::class);
        Route::resource('products.gallery', ProductGalleryController::class)->shallow()->only([
            'index', 'create', 'store', 'destroy'
        ]);
        Route::resource('transaction', TransactionController::class);
        Route::resource('user', UserController::class);
    });

});
