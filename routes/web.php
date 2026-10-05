<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Brand;
use App\Http\Controllers\Category;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Dashboard as DashboardController;
use App\Http\Controllers\Expense;
use App\Http\Controllers\Inventory;
use App\Http\Controllers\Member;
use App\Http\Controllers\POS;
use App\Http\Controllers\Product;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Promotion;
use App\Http\Controllers\Purchase;
use App\Http\Controllers\Report;
use App\Http\Controllers\Sale;
use App\Http\Controllers\Settings;
use App\Http\Controllers\Shift;
use App\Http\Controllers\Supplier;
use App\Http\Controllers\Unit;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS module
    Route::get('/pos', [POS::class, 'index'])->name('pos.index');
    Route::post('/pos', [POS::class, 'store'])->name('pos.store');
    Route::post('/pos/complete', [POS::class, 'complete'])->name('pos.complete');
    Route::patch('/pos/{product}', [POS::class, 'update'])->name('pos.update');
    Route::delete('/pos/{product}', [POS::class, 'remove'])->name('pos.remove');
    Route::delete('/pos', [POS::class, 'clear'])->name('pos.clear');
    Route::post('/pos/hold', [POS::class, 'hold'])->name('pos.hold');
    Route::post('/pos/resume/{code}', [POS::class, 'resume'])->name('pos.resume');
    Route::delete('/pos/hold/{code}', [POS::class, 'deleteHeld'])->name('pos.delete_held');
    Route::get('/pos/search-products', [POS::class, 'searchProducts'])->name('pos.search_products');
    Route::get('/pos/search-members', [POS::class, 'searchMembers'])->name('pos.search_members');
    Route::get('/pos/lookup-member', [POS::class, 'lookupMember'])->name('pos.lookup_member');
    Route::post('/pos/open-shift', [POS::class, 'openShift'])->name('pos.open_shift');
    Route::post('/pos/close-shift', [POS::class, 'closeShift'])->name('pos.close_shift');

    // Sales & Invoices & Returns
    Route::resource('sales', Sale::class)->only(['index', 'show']);
    Route::get('/sales/{sale}/thermal', [Sale::class, 'thermal'])->name('sales.thermal');
    Route::get('/sales/{sale}/invoice', [Sale::class, 'invoice'])->name('sales.invoice');
    Route::post('/sales/{sale}/return', [Sale::class, 'processReturn'])->name('sales.return');

    // Products & Barcodes
    Route::resource('products', Product::class);
    Route::get('/products/{product}/barcode', [Product::class, 'barcode'])->name('products.barcode');

    // Categories, Brands, Units
    Route::resource('categories', Category::class);
    Route::resource('brands', Brand::class);
    Route::resource('units', Unit::class);

    // Multi-Branch & Warehouses & Transfers
    Route::resource('branches', \App\Http\Controllers\BranchController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/branches/switch', [\App\Http\Controllers\BranchController::class, 'switchBranch'])->name('branches.switch');
    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class)->only(['index', 'store', 'update']);
    Route::resource('transfers', \App\Http\Controllers\StockTransferController::class);
    Route::post('/transfers/{transfer}/ship', [\App\Http\Controllers\StockTransferController::class, 'ship'])->name('transfers.ship');
    Route::post('/transfers/{transfer}/receive', [\App\Http\Controllers\StockTransferController::class, 'receive'])->name('transfers.receive');
    Route::post('/transfers/{transfer}/cancel', [\App\Http\Controllers\StockTransferController::class, 'cancel'])->name('transfers.cancel');

    // Inventory & Stock Tracking
    Route::get('/inventory', [Inventory::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [Inventory::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/alerts', [Inventory::class, 'alerts'])->name('inventory.alerts');

    // Purchases & Returns
    Route::resource('purchases', Purchase::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/purchases/{purchase}/return', [Purchase::class, 'processReturn'])->name('purchases.return');

    // Suppliers & Supplier Ledger
    Route::resource('suppliers', Supplier::class);
    Route::get('/suppliers/{supplier}/ledger', [Supplier::class, 'ledger'])->name('suppliers.ledger');

    // Customers & Customer Ledger
    Route::resource('customers', Customer::class);
    Route::get('/customers/{customer}/ledger', [Customer::class, 'ledger'])->name('customers.ledger');

    // Memberships & Member Cards & Points History
    Route::resource('members', Member::class);
    Route::get('/members/{member}/card', [Member::class, 'card'])->name('members.card');
    Route::get('/members/{member}/points', [Member::class, 'pointHistory'])->name('members.points');

    // Expenses & Expense Categories
    Route::resource('expenses', Expense::class)->only(['index', 'store', 'destroy']);
    Route::post('/expenses/categories', [Expense::class, 'storeCategory'])->name('expenses.categories.store');

    // Shifts & Cash Management
    Route::get('/shifts', [Shift::class, 'index'])->name('shifts.index');
    Route::post('/shifts/open', [Shift::class, 'open'])->name('shifts.open');
    Route::post('/shifts/{shift}/close', [Shift::class, 'close'])->name('shifts.close');

    // Accounts & Unified Financial Ledger
    Route::get('/accounts/ledger', [Account::class, 'ledger'])->name('accounts.ledger');

    // Discounts & Promotions
    Route::resource('promotions', Promotion::class)->only(['index', 'store', 'destroy']);
    Route::patch('/promotions/{promotion}/toggle', [Promotion::class, 'toggle'])->name('promotions.toggle');

    // User & Staff Management
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Reports
    Route::get('/reports', [Report::class, 'index'])->name('reports.index');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');

    // Settings
    Route::resource('settings', Settings::class)->only(['index', 'store']);

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
