<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class Report extends Controller
{
    protected ReportService $reportService;
    protected InventoryService $inventoryService;

    public function __construct(ReportService $reportService, InventoryService $inventoryService)
    {
        $this->reportService = $reportService;
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'sales');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $data = [
            'tab' => $tab,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        if ($tab === 'sales') {
            $data['salesData'] = $this->reportService->getSalesReport($startDate, $endDate, $request->input('user_id'));
            $data['cashiers'] = User::query()->where('status', 'active')->get();
        } elseif ($tab === 'purchases') {
            $data['purchaseData'] = $this->reportService->getPurchaseReport($startDate, $endDate, $request->input('supplier_id'));
            $data['suppliers'] = Supplier::query()->where('status', 'active')->get();
        } elseif ($tab === 'profit_loss') {
            $data['plData'] = $this->reportService->getProfitAndLossReport($startDate, $endDate);
        } elseif ($tab === 'inventory') {
            $data['valuation'] = $this->inventoryService->getInventoryValuation();
            $data['lowStock'] = $this->inventoryService->getLowStockProducts();
            $data['outOfStock'] = $this->inventoryService->getOutOfStockProducts();
            $data['expiring'] = $this->inventoryService->getExpiringProducts(30);
        } elseif ($tab === 'dues') {
            $data['customerDues'] = Customer::query()->where('current_balance', '>', 0)->get();
            $data['supplierDues'] = Supplier::query()->where('current_payable_balance', '>', 0)->get();
        } elseif ($tab === 'members') {
            $data['members'] = Member::query()->with(['membershipType', 'customer'])->orderByDesc('points')->paginate(20);
        }

        return view('reports.index', $data);
    }
}
