<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Carbon;

class ReportService
{
    public function getSalesReport(?string $startDate = null, ?string $endDate = null, ?int $userId = null)
    {
        $query = Sale::query()->with(['user', 'customer', 'member']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sales = $query->latest()->paginate(25)->withQueryString();

        $statsQuery = clone $query;
        $totalRevenue = (float) $statsQuery->sum('grand_total');
        $totalDiscount = (float) $statsQuery->sum('discount_amount');
        $totalPaid = (float) $statsQuery->sum('paid_amount');
        $totalDue = (float) $statsQuery->sum('due_amount');
        $totalCount = $statsQuery->count();

        return [
            'sales' => $sales,
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_discount' => round($totalDiscount, 2),
                'total_paid' => round($totalPaid, 2),
                'total_due' => round($totalDue, 2),
                'total_transactions' => $totalCount,
            ],
        ];
    }

    public function getPurchaseReport(?string $startDate = null, ?string $endDate = null, ?int $supplierId = null)
    {
        $query = Purchase::query()->with(['supplier', 'user']);

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->latest()->paginate(25)->withQueryString();

        $statsQuery = clone $query;
        $totalAmount = (float) $statsQuery->sum('total');
        $totalPaid = (float) $statsQuery->sum('paid');
        $totalDue = (float) $statsQuery->sum('due');
        $totalCount = $statsQuery->count();

        return [
            'purchases' => $purchases,
            'summary' => [
                'total_amount' => round($totalAmount, 2),
                'total_paid' => round($totalPaid, 2),
                'total_due' => round($totalDue, 2),
                'total_orders' => $totalCount,
            ],
        ];
    }

    public function getProfitAndLossReport(?string $startDate = null, ?string $endDate = null)
    {
        $start = $startDate ?: now()->startOfMonth()->toDateString();
        $end = $endDate ?: now()->endOfMonth()->toDateString();

        $sales = Sale::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->with('items.product')
            ->get();

        $totalRevenue = (float) $sales->sum('grand_total');

        // Calculate Cost of Goods Sold (COGS)
        $cogs = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $purchasePrice = $item->product ? (float) $item->product->purchase_price : 0;
                $cogs += $purchasePrice * $item->quantity;
            }
        }

        $grossProfit = round($totalRevenue - $cogs, 2);

        $expenses = (float) Expense::query()
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->sum('amount');

        $netProfit = round($grossProfit - $expenses, 2);

        return [
            'start_date' => $start,
            'end_date' => $end,
            'total_revenue' => round($totalRevenue, 2),
            'cogs' => round($cogs, 2),
            'gross_profit' => $grossProfit,
            'total_expenses' => round($expenses, 2),
            'net_profit' => $netProfit,
        ];
    }
}
