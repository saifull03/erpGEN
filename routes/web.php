<?php

use App\Http\Controllers\Brand;
use App\Http\Controllers\Category;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Dashboard as DashboardController;
use App\Http\Controllers\Expense;
use App\Http\Controllers\Member;
use App\Http\Controllers\POS;
use App\Http\Controllers\Product;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Purchase;
use App\Http\Controllers\Report;
use App\Http\Controllers\Sale;
use App\Http\Controllers\Settings;
use App\Http\Controllers\Supplier;
use App\Http\Controllers\Unit;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('products', Product::class);
    Route::resource('categories', Category::class);
    Route::resource('brands', Brand::class);
    Route::resource('units', Unit::class);
    Route::resource('suppliers', Supplier::class);
    Route::resource('customers', Customer::class);
    Route::resource('members', Member::class);
    Route::resource('purchases', Purchase::class);
    Route::resource('sales', Sale::class);
    Route::resource('expenses', Expense::class);
    Route::resource('settings', Settings::class)->only(['index', 'store']);
    Route::resource('reports', Report::class)->only(['index']);
    Route::get('/pos', [POS::class, 'index'])->name('pos.index');
    Route::post('/pos', [POS::class, 'store'])->name('pos.store');
    Route::post('/pos/complete', [POS::class, 'complete'])->name('pos.complete');
    Route::patch('/pos/{product}', [POS::class, 'update'])->name('pos.update');
    Route::delete('/pos/{product}', [POS::class, 'remove'])->name('pos.remove');
    Route::delete('/pos', [POS::class, 'clear'])->name('pos.clear');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
