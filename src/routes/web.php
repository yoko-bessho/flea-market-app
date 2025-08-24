<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Models\Item;
use App\Models\Purchase;

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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.detail');

Route::get('/logout', [ItemController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/setProfile', [UserController::class, 'setProfile'])->name('setProfile');
    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('update');

    Route::post('/item/{item}/like', [ItemController::class, 'like'])->name('item.like');
    Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('item.comment');

    Route::get('/purchase/{item}', [PurchaseController::class, 'purchase'])->name('purchase');
    // Route::post('/purchase/{item}/buyItem', [PurchaseController::class, 'buyItem'])->name('item.buyItem');


    Route::post('/checkout', [PurchaseController::class, 'checkout'])->name('checkout');
    Route::get('/chackout/success', [PurchaseController::class, 'success'])->name('success');
    Route::get('/chackout/cancel', [PurchaseController::class, 'cancel'])->name('cancel');


    Route::get('/purchase/address-edit/{item}', [PurchaseController::class, 'addressEdit'])->name('purchase.address.edit');
    Route::post('/purchase/address-update/{item}', [PurchaseController::class, 'addressUpdate'])->name('address.update');

    Route::get('/sell', [ItemController::class, 'create'])->name('itemCreate');

});
