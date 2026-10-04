<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'slug',
        'category_id',
        'brand_id',
        'unit_id',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'discount_price',
        'tax_rate',
        'minimum_stock',
        'current_stock',
        'image',
        'expiry_date',
        'batch_number',
        'status',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
