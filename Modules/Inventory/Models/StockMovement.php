<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Database\Factories\StockMovementFactory;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

/**
 * Immutable ledger entry — never updated or deleted after creation
 * (see StockMovementPolicy and the migration note). Corrections are made by
 * registering a new "ajuste" movement, not by editing this one.
 */
class StockMovement extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'type',
        'product_id',
        'product_variant_id',
        'stock_location_id',
        'quantity',
        'transfer_group_id',
        'notes',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
        ];
    }

    protected static function newFactory(): StockMovementFactory
    {
        return StockMovementFactory::new();
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

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
