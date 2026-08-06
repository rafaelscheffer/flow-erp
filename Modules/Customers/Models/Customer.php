<?php

declare(strict_types=1);

namespace Modules\Customers\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customers\Database\Factories\CustomerFactory;
use Modules\Customers\Enums\CustomerType;

class Customer extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'trade_name',
        'document',
        'state_registration',
        'birth_date',
        'email',
        'phone',
        'zip_code',
        'address',
        'address_number',
        'address_complement',
        'neighborhood',
        'city',
        'state',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CustomerType::class,
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
