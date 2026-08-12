<?php

declare(strict_types=1);

namespace Modules\Sales\Models;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\StockLocation;
use Modules\Sales\Database\Factories\OrderFactory;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Enums\PaymentMethod;

class Order extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'stock_location_id',
        'status',
        'order_date',
        'discount_amount',
        'shipping_amount',
        'payment_method',
        'notes',
        'created_by',
        'confirmed_at',
        'invoiced_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'order_date' => 'date',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'invoiced_at' => 'datetime',
        ];
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn (OrderItem $item): float => $item->total);
    }

    public function getTotalAttribute(): float
    {
        return $this->subtotal - (float) $this->discount_amount + (float) $this->shipping_amount;
    }
}
