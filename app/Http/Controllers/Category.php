<?php

namespace App\Http\Controllers;

use App\Models\Category as CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Category extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => CategoryModel::query()->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        CategoryModel::query()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, CategoryModel $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(CategoryModel $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
