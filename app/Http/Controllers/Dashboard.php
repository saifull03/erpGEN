<?php

namespace App\Http\Controllers;

use App\Models\Customer as CustomerModel;
use App\Models\Expense as ExpenseModel;
use App\Models\Member as MemberModel;
use App\Models\Product as ProductModel;
use App\Models\Purchase as PurchaseModel;
use App\Models\Sale as SaleModel;
use App\Models\Supplier as SupplierModel;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index()
    {
        $todaySales = SaleModel::query()->whereDate('created_at', today())->sum('grand_total');
        $todayPurchases = PurchaseModel::query()->whereDate('created_at', today())->sum('total');
        $todayExpenses = ExpenseModel::query()->whereDate('created_at', today())->sum('amount');
        $todayProfit = $todaySales - $todayPurchases - $todayExpenses;

        return view('dashboard', [
            'totalProducts' => ProductModel::query()->count(),
            'lowStockProducts' => ProductModel::query()->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'outOfStockProducts' => ProductModel::query()->where('current_stock', '<=', 0)->count(),
            'totalCustomers' => CustomerModel::query()->count(),
            'totalMembers' => MemberModel::query()->count(),
            'totalSuppliers' => SupplierModel::query()->count(),
            'todaySales' => $todaySales,
            'todayPurchases' => $todayPurchases,
            'todayExpenses' => $todayExpenses,
            'todayProfit' => $todayProfit,
            'todayTransactions' => SaleModel::query()->whereDate('created_at', today())->count() + PurchaseModel::query()->whereDate('created_at', today())->count(),
        ]);
    }
}
