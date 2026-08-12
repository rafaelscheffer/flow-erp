<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Inventory\Database\Factories\StockReservationFactory;
use Modules\Inventory\Enums\StockReservationStatus;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

class StockReservation extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'stock_location_id',
        'quantity',
        'status',
        'reserved_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'status' => StockReservationStatus::class,
        ];
    }

    protected static function newFactory(): StockReservationFactory
    {
        return StockReservationFactory::new();
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

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StockReservationStatus::Active);
    }
}
