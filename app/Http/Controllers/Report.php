<?php

namespace App\Http\Controllers;

use App\Models\Product as ProductModel;
use App\Models\Purchase as PurchaseModel;
use App\Models\Sale as SaleModel;
use Illuminate\Http\Request;

class Report extends Controller
{
    public function index()
    {
        $sales = SaleModel::query()->sum('grand_total');
        $purchases = PurchaseModel::query()->sum('total');
        $products = ProductModel::query()->where('current_stock', '<=', 0)->count();

        return view('reports.index', [
            'totalSales' => $sales,
            'totalPurchases' => $purchases,
            'grossProfit' => $sales - $purchases,
            'outOfStockProducts' => $products,
        ]);
    }
}
