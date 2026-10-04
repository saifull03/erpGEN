<?php

namespace App\Http\Controllers;

use App\Models\Product as ProductModel;
use App\Models\Sale as SaleModel;
use App\Models\StockMovement;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class POS extends Controller
{
    public function index()
    {
        $cart = $this->cart(request());
        $products = ProductModel::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return view('pos.index', [
            'products' => $products,
            'cart' => $cart,
            'subtotal' => $this->subtotal($products, $cart),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = ProductModel::query()
            ->where('status', 'active')
            ->where(function ($query) use ($data) {
                $query->where('barcode', $data['barcode'])->orWhere('sku', $data['barcode']);
            })
            ->first();

        if (! $product) {
            return back()->withErrors(['barcode' => 'Product not found.'])->withInput();
        }

        $cart = $this->cart($request);
        $quantity = ($cart[$product->id] ?? 0) + $data['quantity'];

        if ($product->current_stock < $quantity) {
            return back()->withErrors(['barcode' => 'Insufficient stock available.']);
        }

        $cart[$product->id] = $quantity;
        $request->session()->put('pos_cart', $cart);

        return redirect()->route('pos.index')->with('success', "Item added: {$product->name}");
    }

    public function complete(Request $request)
    {
        $cart = $this->cart($request);

        if ($cart === []) {
            return back()->withErrors(['cart' => 'Add at least one product before completing the sale.']);
        }

        $validated = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'member_number' => ['nullable', 'string', 'max:50'],
        ]);

        $sale = DB::transaction(function () use ($cart, $validated) {
            $member = null;
            if (! empty($validated['member_number'])) {
                $member = Member::query()
                    ->where('member_number', $validated['member_number'])
                    ->where('status', 'active')
                    ->first();

                if (! $member) {
                    throw ValidationException::withMessages([
                        'member_number' => 'Active member not found for this membership number.',
                    ]);
                }
            }

            $products = ProductModel::query()
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product || $product->status !== 'active') {
                    throw ValidationException::withMessages(['cart' => 'One of the selected products is no longer available.']);
                }

                if ($product->current_stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => "Insufficient stock for {$product->name}."]);
                }

                $subtotal += $product->selling_price * $quantity;
            }

            $subtotal = round($subtotal, 2);
            $paidAmount = round((float) $validated['paid_amount'], 2);
            if ($paidAmount > $subtotal) {
                throw ValidationException::withMessages(['paid_amount' => 'Paid amount cannot be greater than the sale total.']);
            }

            $sale = SaleModel::query()->create([
                'invoice_number' => 'INV-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
                'user_id' => auth()->id(),
                'customer_id' => $member?->customer_id,
                'member_id' => $member?->id,
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
                'paid_amount' => $paidAmount,
                'due_amount' => max(0, $subtotal - $paidAmount),
                'status' => $paidAmount >= $subtotal ? 'completed' : 'partial',
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $itemTotal = round($product->selling_price * $quantity, 2);
                $previousStock = $product->current_stock;
                $newStock = $previousStock - $quantity;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->selling_price,
                    'subtotal' => $itemTotal,
                ]);

                $product->update(['current_stock' => $newStock]);
                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reference' => $sale->invoice_number,
                    'user_id' => auth()->id(),
                ]);
            }

            return $sale;
        });

        $request->session()->forget('pos_cart');

        return redirect()->route('sales.index')->with('success', "Sale {$sale->invoice_number} completed.");
    }

    public function update(Request $request, ProductModel $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($product->status !== 'active' || $product->current_stock < $data['quantity']) {
            return back()->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        $cart = $this->cart($request);
        $cart[$product->id] = $data['quantity'];
        $request->session()->put('pos_cart', $cart);

        return redirect()->route('pos.index')->with('success', 'Cart updated.');
    }

    public function remove(Request $request, ProductModel $product)
    {
        $cart = $this->cart($request);
        unset($cart[$product->id]);
        $request->session()->put('pos_cart', $cart);

        return redirect()->route('pos.index')->with('success', 'Item removed from cart.');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('pos_cart');

        return redirect()->route('pos.index')->with('success', 'Cart cleared.');
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
