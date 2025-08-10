<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Models\Item;

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

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase');
});
