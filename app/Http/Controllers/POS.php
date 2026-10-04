<?php

namespace App\Http\Controllers;

use App\Models\Product as ProductModel;
use Illuminate\Http\Request;

class POS extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'products' => ProductModel::query()->where('status', 'active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = ProductModel::query()->where('barcode', $data['barcode'])->orWhere('sku', $data['barcode'])->firstOrFail();

        if ($product->current_stock < $data['quantity']) {
            return back()->withErrors(['barcode' => 'Insufficient stock available.']);
        }

        return redirect()->route('pos.index')->with('success', "Item added: {$product->name}");
    }
}
