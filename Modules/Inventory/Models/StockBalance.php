<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

/**
 * Read-only materialized cache of current stock levels — maintained
 * exclusively by StockMovementObserver/StockReservationObserver. Not
 * Auditable (it's a derived projection, not user-authored data) and not
 * user-editable through the admin panel.
 */
class StockBalance extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'stock_location_id',
        'quantity',
        'reserved_quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }
}
