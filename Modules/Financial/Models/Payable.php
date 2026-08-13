<?php

declare(strict_types=1);

namespace Modules\Financial\Models;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Financial\Database\Factories\PayableFactory;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\PaymentMethod;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\Supplier;

class Payable extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'purchase_order_id',
        'description',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'payment_method',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'status' => PayableStatus::class,
            'paid_at' => 'datetime',
            'payment_method' => PaymentMethod::class,
        ];
    }

    protected static function newFactory(): PayableFactory
    {
        return PayableFactory::new();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
