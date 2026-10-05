<?php

namespace App\Http\Controllers;

use App\Models\Setting as SettingModel;
use App\Services\AuditService;
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
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'store_phone' => ['nullable', 'string', 'max:50'],
            'store_email' => ['nullable', 'email', 'max:255'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'currency_code' => ['required', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'loyalty_points_per_hundred' => ['nullable', 'integer', 'min:0'],
            'loyalty_discount_per_hundred_points' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:1'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            SettingModel::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        AuditService::log('update_settings', 'Settings', null, null, $validated);

        return redirect()->route('settings.index')->with('success', 'erpGEN supermarket system settings updated successfully.');
    }
}
