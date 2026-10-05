<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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
        $activeBranchId = session('active_branch_id');

        $salesQuery = SaleModel::query()->whereDate('created_at', $today);
        $purchasesQuery = PurchaseModel::query()->whereDate('created_at', $today);
        $expensesQuery = ExpenseModel::query()->whereDate('date', $today);
        $cashRegisterQuery = CashRegister::query()->where('status', 'open');

        if ($activeBranchId) {
            $salesQuery->where('branch_id', $activeBranchId);
            $purchasesQuery->where('branch_id', $activeBranchId);
            $expensesQuery->where('branch_id', $activeBranchId);
            $cashRegisterQuery->where('branch_id', $activeBranchId);
        }

        $todaySales = (float) $salesQuery->sum('grand_total');
        $todayPurchases = (float) $purchasesQuery->sum('total');
        $todayExpenses = (float) $expensesQuery->sum('amount');
        $todayTransactions = $salesQuery->count();

        // Calculate today COGS for gross profit
        $todaySalesItems = (clone $salesQuery)
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
        $cashInHand = (float) $cashRegisterQuery->sum('expected_balance');

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

        $recentSales = SaleModel::query()
            ->when($activeBranchId, fn ($q) => $q->where('branch_id', $activeBranchId))
            ->with(['customer', 'user', 'branch'])
            ->latest()
            ->limit(6)
            ->get();

        $criticalLowStock = ProductModel::query()
            ->where('status', 'active')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->limit(6)
            ->get();

        // Multi-Branch Overview for Central Super Admin Dashboard
        $branches = Branch::where('status', 'active')
            ->withCount(['users', 'warehouses'])
            ->with(['cashRegisters' => fn ($q) => $q->where('status', 'open')])
            ->get()
            ->map(function ($b) use ($today) {
                $b->today_sales = (float) SaleModel::where('branch_id', $b->id)->whereDate('created_at', $today)->sum('grand_total');
                $b->mtd_sales = (float) SaleModel::where('branch_id', $b->id)->whereMonth('created_at', now()->month)->sum('grand_total');
                $b->open_shifts_count = $b->cashRegisters->count();
                return $b;
            });

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
            'branches' => $branches,
            'activeBranchId' => $activeBranchId,
        ]);
    }
}

