<?php

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;

Route::get('/', function (){
    return view('login.form');
});
Route::get('/login', [UserController::class, 'showLoginForm'])->name('showLoginForm');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
Route::post('/product/{id}', [ProductController::class, 'updateStock'])->name('product.updateStock');
Route::post('/product/destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');


Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
Route::post('/purchase/confirm-product', [PurchaseController::class, 'selectProduct'])->name('purchase.selectProduct');
Route::get('/purchase/confirm', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/purchase/payment/member', [PurchaseController::class, 'memberPayment'])->name('purchase.memberPayment');
Route::post('/purchase/store-member', [PurchaseController::class, 'storeMember'])->name('purchase.storeMember');
Route::get('/purchase/detail/{id}', [PurchaseController::class, 'detail'])->name('purchase.detail');
Route::post('/purchase/detailDetail/{id}', [PurchaseController::class, 'orderDetail'])->name('purchase.detail.update');
Route::get('/purchase/export', [PurchaseController::class, 'export'])->name('purchase.export');
Route::get('/member/check', [App\Http\Controllers\MemberController::class, 'check'])->name('member.check');
