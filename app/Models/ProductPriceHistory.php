<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'old_purchase_price',
        'new_purchase_price',
        'old_selling_price',
        'new_selling_price',
        'user_id',
        'reason',
    ];

    protected $casts = [
        'old_purchase_price' => 'decimal:2',
        'new_purchase_price' => 'decimal:2',
        'old_selling_price' => 'decimal:2',
        'new_selling_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
