<?php

namespace App\Http\Controllers;

use App\Models\HeldSale;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\Product as ProductModel;
use App\Models\Sale as SaleModel;
use App\Services\SaleService;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class POS extends Controller
{
    protected SaleService $saleService;
    protected ShiftService $shiftService;

    public function __construct(SaleService $saleService, ShiftService $shiftService)
    {
        $this->saleService = $saleService;
        $this->shiftService = $shiftService;
    }

    public function index(Request $request)
    {
        $cart = $this->cart($request);
        $products = ProductModel::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $membershipTypes = MembershipType::query()->where('is_active', true)->get();
        $heldSales = $this->saleService->getHeldSales($request->user());
        $activeShift = $this->shiftService->getActiveShift($request->user());

        $member = null;
        if ($memberNum = $request->session()->get('pos_member_number')) {
            $member = Member::query()->with('membershipType')->where('member_number', $memberNum)->first();
        }

        $lastSale = SaleModel::query()->where('user_id', $request->user()?->id)->latest()->first();

        return view('pos.index', [
            'products' => $products,
            'cart' => $cart,
            'subtotal' => $this->subtotal($products, $cart),
            'membershipTypes' => $membershipTypes,
            'heldSales' => $heldSales,
            'activeShift' => $activeShift,
            'activeMember' => $member,
            'lastSale' => $lastSale,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => ['required', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $queryBarcode = trim($data['barcode']);
        $product = ProductModel::query()
            ->where('status', 'active')
            ->where(function ($query) use ($queryBarcode) {
                $query->where('barcode', $queryBarcode)
                      ->orWhere('sku', $queryBarcode)
                      ->orWhere('name', 'like', "%{$queryBarcode}%");
            })
            ->first();

        if (! $product) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }
            return back()->withErrors(['barcode' => "Product '{$queryBarcode}' not found or out of stock."])->withInput();
        }

        $addQty = isset($data['quantity']) ? (int) $data['quantity'] : 1;
        $cart = $this->cart($request);
        $newQty = ($cart[$product->id] ?? 0) + $addQty;

        if ($product->current_stock < $newQty) {
            $msg = "Insufficient stock for {$product->name}. Only {$product->current_stock} units available.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['barcode' => $msg]);
        }

        $cart[$product->id] = $newQty;
        $request->session()->put('pos_cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Added {$product->name} (Qty: {$newQty})",
                'product' => $product,
                'cart' => $cart,
            ]);
        }

        return redirect()->route('pos.index')->with('success', "Item added: {$product->name}");
    }

    public function complete(Request $request)
    {
        $cart = $this->cart($request);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Add at least one product before completing the sale.']);
        }

        if ($request->has('payments') && is_string($request->input('payments')) && !empty($request->input('payments'))) {
            $decoded = json_decode($request->input('payments'), true);
            if (is_array($decoded)) {
                $request->merge(['payments' => $decoded]);
            }
        }

        $data = $request->validate([
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'member_number' => ['nullable', 'string', 'max:50'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'invoice_discount' => ['nullable', 'numeric', 'min:0'],
            'points_redeemed' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'payments' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $sale = $this->saleService->createSale($data, $cart, $request->user());
            $request->session()->forget('pos_cart');
            $request->session()->forget('pos_member_number');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sale {$sale->invoice_number} completed.",
                    'sale_id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'redirect_url' => route('pos.index'),
                    'thermal_url' => route('sales.thermal', $sale),
                ]);
            }

            // Cashiers stay on POS register ready for next customer with quick thermal print option
            if ($request->user()?->isCashier() && session('manager_authorized_until', 0) <= time()) {
                return redirect()->route('pos.index')
                    ->with('success', "Sale #{$sale->invoice_number} completed successfully.")
                    ->with('last_sale_id', $sale->id);
            }

            return redirect()->route('sales.show', $sale)->with('success', "Sale #{$sale->invoice_number} completed successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, ProductModel $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($product->status !== 'active' || $product->current_stock < $data['quantity']) {
            return back()->withErrors(['quantity' => "Insufficient stock for {$product->name} (Available: {$product->current_stock})."]);
        }

        $cart = $this->cart($request);
        $cart[$product->id] = $data['quantity'];
        $request->session()->put('pos_cart', $cart);

        return redirect()->route('pos.index')->with('success', 'Cart quantity updated.');
    }

    public function remove(Request $request, ProductModel $product)
    {
        $cart = $this->cart($request);
        unset($cart[$product->id]);
        $request->session()->put('pos_cart', $cart);

        return redirect()->route('pos.index')->with('success', 'Product removed from cart.');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('pos_cart');
        $request->session()->forget('pos_member_number');

        return redirect()->route('pos.index')->with('success', 'Cart cleared.');
    }

    public function hold(Request $request)
    {
        $cart = $this->cart($request);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Cannot hold an empty cart.']);
        }

        $held = $this->saleService->holdSale(
            $cart,
            $request->input('customer_id'),
            $request->input('member_id'),
            $request->input('member_number'),
            $request->user(),
            $request->input('notes')
        );

        $request->session()->forget('pos_cart');
        $request->session()->forget('pos_member_number');

        return redirect()->route('pos.index')->with('success', "Cart placed on hold with Ref: {$held->reference_code}");
    }

    public function resume(Request $request, string $referenceCode)
    {
        $held = $this->saleService->resumeHeldSale($referenceCode);
        if (! $held) {
            return back()->withErrors(['hold' => 'Held cart not found or already resumed.']);
        }

        $request->session()->put('pos_cart', $held->cart_data);
        if ($held->member_number) {
            $request->session()->put('pos_member_number', $held->member_number);
        }

        return redirect()->route('pos.index')->with('success', "Resumed held sale {$referenceCode}");
    }

    public function deleteHeld(Request $request, string $referenceCode)
    {
        $this->saleService->deleteHeldSale($referenceCode);
        return redirect()->route('pos.index')->with('success', "Held cart {$referenceCode} deleted.");
    }

    public function searchProducts(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $products = ProductModel::query()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'sku', 'barcode', 'name', 'selling_price', 'current_stock']);

        return response()->json($products);
    }

    public function searchMembers(Request $request)
    {
        $term = trim($request->input('q', $request->input('term', '')));
        if (strlen($term) < 1) {
            return response()->json([]);
        }

        $members = Member::query()
            ->with('membershipType')
            ->where('status', 'active')
            ->where(function ($q) use ($term) {
                $q->where('member_number', 'like', "%{$term}%")
                  ->orWhere('membership_id', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%");
            })
            ->limit(8)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'member_number' => $member->member_number,
                    'phone' => $member->phone,
                    'points' => $member->points,
                    'tier' => $member->membershipType?->name ?? 'Standard',
                    'discount_percentage' => (float) ($member->membershipType?->discount_percentage ?? 0),
                ];
            });

        return response()->json($members);
    }

    public function lookupMember(Request $request)
    {
        $term = trim($request->input('term', ''));
        $member = Member::query()
            ->with('membershipType')
            ->where('status', 'active')
            ->where(function ($q) use ($term) {
                $q->where('member_number', $term)
                  ->orWhere('membership_id', $term)
                  ->orWhere('phone', $term);
            })
            ->first();

        if (! $member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_number' => $member->member_number,
                'phone' => $member->phone,
                'points' => $member->points,
                'tier' => $member->membershipType?->name ?? 'Standard',
                'discount_percentage' => (float) ($member->membershipType?->discount_percentage ?? 0),
            ],
        ]);
    }

    public function openShift(Request $request)
    {
        $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->shiftService->openShift(
            $request->user(),
            (float) $request->input('opening_balance'),
            $request->input('notes')
        );

        return redirect()->route('pos.index')->with('success', 'Shift opened successfully.');
    }

    public function closeShift(Request $request)
    {
        $request->validate([
            'actual_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $activeShift = $this->shiftService->getActiveShift($request->user());
        if (! $activeShift) {
            return back()->withErrors(['shift' => 'No active shift found.']);
        }

        $this->shiftService->closeShift(
            $activeShift,
            (float) $request->input('actual_balance'),
            $request->input('notes'),
            $request->user()
        );

        return redirect()->route('pos.index')->with('success', "Shift #{$activeShift->shift_number} closed successfully. Difference: ৳ " . number_format($activeShift->difference, 2));
    }

    private function cart(Request $request): array
    {
        return array_filter(
            array_map('intval', $request->session()->get('pos_cart', [])),
            fn (int $quantity): bool => $quantity > 0,
        );
    }

    private function subtotal($products, array $cart): float
    {
        return round($products->sum(function ($product) use ($cart) {
            return $product->selling_price * ($cart[$product->id] ?? 0);
        }), 2);
    }
}
