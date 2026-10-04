<?php

namespace App\Http\Controllers;

use App\Models\Promotion as PromotionModel;
use App\Services\AuditService;
use Illuminate\Http\Request;

class Promotion extends Controller
{
    public function index()
    {
        $promotions = PromotionModel::query()->latest()->paginate(20);

        return view('promotions.index', [
            'promotions' => $promotions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:promotions,code'],
            'type' => ['required', 'string', 'in:percentage,fixed,bogo'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['min_spend'] = $validated['min_spend'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $promo = PromotionModel::query()->create($validated);
        AuditService::log('create_promotion', 'Promotions', (string) $promo->id, null, $promo->toArray());

        return redirect()->route('promotions.index')->with('success', "Promotion '{$promo->title}' created.");
    }

    public function toggle(PromotionModel $promotion)
    {
        $promotion->update(['is_active' => ! $promotion->is_active]);

        return redirect()->route('promotions.index')->with('success', "Promotion status updated.");
    }

    public function destroy(PromotionModel $promotion)
    {
        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'Promotion deleted.');
    }
}
