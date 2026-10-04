<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Customer as CustomerModel;
use App\Models\Expense as ExpenseModel;
use App\Models\Member as MemberModel;
use App\Models\Product as ProductModel;
use App\Models\Purchase as PurchaseModel;
use App\Models\Sale as SaleModel;
use App\Models\Supplier as SupplierModel;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index(InventoryService $inventoryService)
    {
        $today = now()->toDateString();

        $todaySales = (float) SaleModel::query()->whereDate('created_at', $today)->sum('grand_total');
        $todayPurchases = (float) PurchaseModel::query()->whereDate('created_at', $today)->sum('total');
        $todayExpenses = (float) ExpenseModel::query()->whereDate('date', $today)->sum('amount');
        $todayTransactions = SaleModel::query()->whereDate('created_at', $today)->count();

        // Calculate today COGS for gross profit
        $todaySalesItems = SaleModel::query()
            ->whereDate('created_at', $today)
            ->with('items.product')
            ->get();

        $todayCogs = 0;
        foreach ($todaySalesItems as $sale) {
            foreach ($sale->items as $item) {
                $pPrice = $item->product ? (float) $item->product->purchase_price : 0;
                $todayCogs += $pPrice * $item->quantity;
            }
        }
        $todayProfit = round($todaySales - $todayCogs - $todayExpenses, 2);

        // Cash in hand from active shifts
        $cashInHand = (float) CashRegister::query()->where('status', 'open')->sum('expected_balance');

        $lowStockProducts = ProductModel::query()
            ->where('status', 'active')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockProducts = ProductModel::query()
            ->where('status', 'active')
            ->where('current_stock', '<=', 0)
            ->count();

        $expiringProducts = ProductModel::query()
            ->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30)->toDateString())
            ->count();

        $recentSales = SaleModel::query()->with(['customer', 'user'])->latest()->limit(5)->get();
        $criticalLowStock = ProductModel::query()
            ->where('status', 'active')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalProducts' => ProductModel::query()->where('status', 'active')->count(),
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'expiringProducts' => $expiringProducts,
            'totalCustomers' => CustomerModel::query()->count(),
            'totalMembers' => MemberModel::query()->count(),
            'totalSuppliers' => SupplierModel::query()->count(),
            'todaySales' => $todaySales,
            'todayPurchases' => $todayPurchases,
            'todayExpenses' => $todayExpenses,
            'todayProfit' => $todayProfit,
            'todayTransactions' => $todayTransactions,
            'cashInHand' => $cashInHand,
            'recentSales' => $recentSales,
            'criticalLowStock' => $criticalLowStock,
        ]);
    }
}
