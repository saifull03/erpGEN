<?php

namespace App\Http\Controllers;

use App\Models\Setting as SettingModel;
use Illuminate\Http\Request;

class Settings extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'settings' => SettingModel::query()->pluck('value', 'key'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        foreach ($validated as $key => $value) {
            SettingModel::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with('success', 'System settings updated.');
    }
}
