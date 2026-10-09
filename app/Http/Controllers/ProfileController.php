<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form along with their Sales History and Analytics Dashboard.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $tab = $request->input('tab', 'dashboard');

        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // 1. Core Sales Metrics for Authenticated User
        $userSalesQuery = Sale::where('user_id', $user->id);

        $todaySales = (float) (clone $userSalesQuery)->whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('grand_total');
        $todayOrders = (int) (clone $userSalesQuery)->whereDate('created_at', $today)->where('status', '!=', 'cancelled')->count();

        $thisMonthSales = (float) (clone $userSalesQuery)->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '<=', $endOfMonth)->where('status', '!=', 'cancelled')->sum('grand_total');
        $thisMonthOrders = (int) (clone $userSalesQuery)->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '<=', $endOfMonth)->where('status', '!=', 'cancelled')->count();

        $totalSales = (float) (clone $userSalesQuery)->where('status', '!=', 'cancelled')->sum('grand_total');
        $totalOrders = (int) (clone $userSalesQuery)->where('status', '!=', 'cancelled')->count();
        $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        $totalDiscounts = (float) (clone $userSalesQuery)->where('status', '!=', 'cancelled')->sum(DB::raw('discount_amount + invoice_discount + points_discount'));
        $totalPaid = (float) (clone $userSalesQuery)->where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalDue = (float) (clone $userSalesQuery)->where('status', '!=', 'cancelled')->sum('due_amount');

        // Total items sold by this user
        $totalItemsSold = (int) SaleItem::whereIn('sale_id', function ($q) use ($user) {
            $q->select('id')->from('sales')->where('user_id', $user->id)->where('status', '!=', 'cancelled');
        })->sum('quantity');

        // Active Shift Status for Cashier / User
        $activeShift = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        // 2. Payment Method Breakdown for this User
        $paymentBreakdown = Sale::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total_amount'))
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        // 3. Daily Sales Trend for the last 14 days
        $daysRange = collect(range(13, 0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->toDateString();
        });

        $dailySalesRaw = Sale::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', now()->subDays(13)->toDateString())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $dailyCountsRaw = Sale::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', now()->subDays(13)->toDateString())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartLabels = [];
        $chartSalesData = [];
        $chartOrdersData = [];

        foreach ($daysRange as $d) {
            $chartLabels[] = Carbon::parse($d)->format('M d');
            $chartSalesData[] = (float) ($dailySalesRaw[$d] ?? 0);
            $chartOrdersData[] = (int) ($dailyCountsRaw[$d] ?? 0);
        }

        // 4. Top 6 Best Selling Products by this User
        $topProducts = SaleItem::whereIn('sale_id', function ($q) use ($user) {
            $q->select('id')->from('sales')->where('user_id', $user->id)->where('status', '!=', 'cancelled');
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
        ->with('product.category', 'product.unit')
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->limit(6)
        ->get();

        // 5. Paginated & Filterable Sales History for this User
        $historyQuery = Sale::where('user_id', $user->id)
            ->with(['customer', 'member', 'items.product', 'payments', 'shift']);

        if ($search = $request->input('search')) {
            $historyQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn ($mq) => $mq->where('member_number', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        if ($startDate = $request->input('start_date')) {
            $historyQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $historyQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            $historyQuery->where('payment_method', $paymentMethod);
        }

        if ($status = $request->input('status')) {
            $historyQuery->where('status', $status);
        }

        // Filtered summary for history tab header
        $filteredCount = (clone $historyQuery)->count();
        $filteredTotal = (float) (clone $historyQuery)->where('status', '!=', 'cancelled')->sum('grand_total');

        $salesHistory = $historyQuery->latest()->paginate(12)->withQueryString();

        // Quick recent sales for dashboard tab
        $recentSales = Sale::where('user_id', $user->id)
            ->with(['customer', 'member', 'items'])
            ->latest()
            ->limit(6)
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'tab' => $tab,
            'todaySales' => $todaySales,
            'todayOrders' => $todayOrders,
            'thisMonthSales' => $thisMonthSales,
            'thisMonthOrders' => $thisMonthOrders,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'totalItemsSold' => $totalItemsSold,
            'totalDiscounts' => $totalDiscounts,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'activeShift' => $activeShift,
            'paymentBreakdown' => $paymentBreakdown,
            'chartLabels' => $chartLabels,
            'chartSalesData' => $chartSalesData,
            'chartOrdersData' => $chartOrdersData,
            'topProducts' => $topProducts,
            'salesHistory' => $salesHistory,
            'recentSales' => $recentSales,
            'filteredCount' => $filteredCount,
            'filteredTotal' => $filteredTotal,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit', ['tab' => 'account'])->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

