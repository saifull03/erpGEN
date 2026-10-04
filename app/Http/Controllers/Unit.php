<?php

namespace App\Http\Controllers;

use App\Models\Unit as UnitModel;
use Illuminate\Http\Request;

class Unit extends Controller
{
    public function index()
    {
        return view('units.index', ['units' => UnitModel::query()->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string'],
        ]);

        UnitModel::query()->create($validated);

        return redirect()->route('units.index')->with('success', 'Unit created.');
    }

    public function update(Request $request, UnitModel $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string'],
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit updated.');
    }

    public function destroy(UnitModel $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted.');
    }
}
