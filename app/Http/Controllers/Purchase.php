<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase as PurchaseModel;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Purchase extends Controller
{
    protected PurchaseService $purchaseService;
    protected ReturnService $returnService;

    public function __construct(PurchaseService $purchaseService, ReturnService $returnService)
    {
        $this->purchaseService = $purchaseService;
        $this->returnService = $returnService;
    }

    public function index(Request $request)
    {
        $query = PurchaseModel::query()->with(['supplier', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($supId = $request->input('supplier_id')) {
            $query->where('supplier_id', $supId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->latest()->paginate(20)->withQueryString();
        $suppliers = Supplier::query()->where('status', 'active')->get();

        return view('purchases.index', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::query()->where('status', 'active')->get();
        $products = Product::query()->where('status', 'active')->get();

        return view('purchases.create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_invoice_number' => ['nullable', 'string', 'max:50', 'unique:purchases,purchase_invoice_number'],
            'date' => ['required', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'paid' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:draft,confirmed,received'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $purchase = $this->purchaseService->createPurchase($data, $data['items'], $request->user());

            return redirect()->route('purchases.show', $purchase)->with('success', "Purchase order #{$purchase->purchase_invoice_number} created successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(PurchaseModel $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product', 'returns.items.product']);

        return view('purchases.show', [
            'purchase' => $purchase,
        ]);
    }

    public function processReturn(Request $request, PurchaseModel $purchase)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $purchaseReturn = $this->returnService->processPurchaseReturn(
                $purchase,
                $data['items'],
                $data['reason'],
                $request->user()
            );

            return redirect()->route('purchases.show', $purchase)->with('success', "Purchase Return #{$purchaseReturn->return_number} processed successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
