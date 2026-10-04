<?php

namespace App\Http\Controllers;

use App\Models\Sale as SaleModel;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Sale extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index(Request $request)
    {
        $query = SaleModel::query()->with(['customer', 'member', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn ($mq) => $mq->where('member_number', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sales = $query->latest()->paginate(20)->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
        ]);
    }

    public function show(SaleModel $sale)
    {
        $sale->load(['items.product', 'customer', 'member.membershipType', 'user', 'payments', 'returns.items.product']);

        return view('sales.show', [
            'sale' => $sale,
        ]);
    }

    public function thermal(SaleModel $sale)
    {
        $sale->load(['items.product', 'customer', 'member.membershipType', 'user', 'payments']);

        return view('sales.thermal', [
            'sale' => $sale,
        ]);
    }

    public function invoice(SaleModel $sale)
    {
        $sale->load(['items.product', 'customer', 'member.membershipType', 'user', 'payments']);

        return view('sales.invoice', [
            'sale' => $sale,
        ]);
    }

    public function processReturn(Request $request, SaleModel $sale)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'payment_method' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $saleReturn = $this->returnService->processSaleReturn(
                $sale,
                $data['items'],
                $data['payment_method'],
                $data['reason'],
                $request->user()
            );

            return redirect()->route('sales.show', $sale)->with('success', "Sale Return #{$saleReturn->return_number} processed successfully. Refund: ৳ " . number_format($saleReturn->total_refund, 2));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
