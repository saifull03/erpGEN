<?php

namespace App\Http\Controllers;

use App\Models\Brand as BrandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Brand extends Controller
{
    public function index()
    {
        return view('brands.index', ['brands' => BrandModel::query()->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        BrandModel::query()->create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand created.');
    }

    public function update(Request $request, BrandModel $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $brand->update($validated);

        return redirect()->route('brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(BrandModel $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted.');
    }
}
