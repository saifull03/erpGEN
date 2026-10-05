<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\WarehouseProductStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function index(): View
    {
        $transfers = StockTransfer::with(['fromWarehouse.branch', 'toWarehouse.branch', 'creator', 'receiver', 'items.product'])
            ->latest()
            ->paginate(15);

        $warehouses = Warehouse::with('branch')->where('status', 'active')->get();
        $products = Product::where('status', 'active')->select('id', 'sku', 'name', 'current_stock')->get();

        return view('transfers.index', compact('transfers', 'warehouses', 'products'));
    }

    public function create(): View
    {
        $warehouses = Warehouse::with('branch')->where('status', 'active')->get();
        $products = Product::where('status', 'active')->select('id', 'sku', 'name', 'current_stock')->get();

        return view('transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $transferNo = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $transfer = StockTransfer::create([
                'transfer_no' => $transferNo,
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'created_by' => Auth::id(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('transfers.index')->with('success', "Stock transfer {$transferNo} created in pending status.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create transfer: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockTransfer $transfer): View
    {
        $transfer->load(['fromWarehouse.branch', 'toWarehouse.branch', 'creator', 'receiver', 'items.product.unit']);
        return view('transfers.show', compact('transfer'));
    }

    public function ship(StockTransfer $transfer): RedirectResponse
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be shipped.');
        }

        DB::beginTransaction();
        try {
            foreach ($transfer->items as $item) {
                $stockRecord = WarehouseProductStock::firstOrCreate(
                    ['warehouse_id' => $transfer->from_warehouse_id, 'product_id' => $item->product_id],
                    ['stock' => 0]
                );

                $prevStock = $stockRecord->stock;
                $stockRecord->decrement('stock', $item->quantity);
                $newStock = $stockRecord->stock;

                // Record stock movement
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $transfer->from_warehouse_id,
                    'branch_id' => $transfer->fromWarehouse->branch_id,
                    'type' => 'transfer_out',
                    'quantity' => -$item->quantity,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'user_id' => Auth::id(),
                    'reference' => $transfer->transfer_no,
                    'notes' => "Shipped transfer to {$transfer->toWarehouse->name}",
                ]);
            }

            $transfer->update([
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', "Transfer {$transfer->transfer_no} marked as in-transit.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive(Request $request, StockTransfer $transfer): RedirectResponse
    {
        if ($transfer->status !== 'in_transit') {
            return back()->with('error', 'Only in-transit transfers can be marked as received.');
        }

        DB::beginTransaction();
        try {
            foreach ($transfer->items as $item) {
                $receivedQty = $item->quantity; // standard full receive
                $item->update(['received_quantity' => $receivedQty]);

                $destStock = WarehouseProductStock::firstOrCreate(
                    ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $item->product_id],
                    ['stock' => 0]
                );

                $prevStock = $destStock->stock;
                $destStock->increment('stock', $receivedQty);
                $newStock = $destStock->stock;

                // Record stock movement
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'branch_id' => $transfer->toWarehouse->branch_id,
                    'type' => 'transfer_in',
                    'quantity' => $receivedQty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'user_id' => Auth::id(),
                    'reference' => $transfer->transfer_no,
                    'notes' => "Received transfer from {$transfer->fromWarehouse->name}",
                ]);
            }

            $transfer->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', "Stock transfer {$transfer->transfer_no} received and inventory updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(StockTransfer $transfer): RedirectResponse
    {
        if (in_array($transfer->status, ['received', 'cancelled'])) {
            return back()->with('error', 'Completed or already cancelled transfers cannot be modified.');
        }

        DB::beginTransaction();
        try {
            // If already shipped, restore stock to from_warehouse
            if ($transfer->status === 'in_transit') {
                foreach ($transfer->items as $item) {
                    $stockRecord = WarehouseProductStock::firstOrCreate(
                        ['warehouse_id' => $transfer->from_warehouse_id, 'product_id' => $item->product_id],
                        ['stock' => 0]
                    );
                    $stockRecord->increment('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $transfer->from_warehouse_id,
                        'branch_id' => $transfer->fromWarehouse->branch_id,
                        'type' => 'adjustment_in',
                        'quantity' => $item->quantity,
                        'reference' => $transfer->transfer_no,
                        'notes' => "Restored stock due to cancelled transfer {$transfer->transfer_no}",
                    ]);
                }
            }

            $transfer->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', "Stock transfer {$transfer->transfer_no} has been cancelled.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
