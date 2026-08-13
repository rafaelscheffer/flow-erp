<?php

declare(strict_types=1);

namespace Modules\Financial\Models;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customers\Models\Customer;
use Modules\Financial\Database\Factories\ReceivableFactory;
use Modules\Financial\Enums\PaymentMethod;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Sales\Models\Order;

class Receivable extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_id',
        'account_id',
        'cost_center_id',
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
            'status' => ReceivableStatus::class,
            'paid_at' => 'datetime',
            'payment_method' => PaymentMethod::class,
        ];
    }

    protected static function newFactory(): ReceivableFactory
    {
        return ReceivableFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
