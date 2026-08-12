<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Inventory\Database\Factories\StockLocationFactory;

class StockLocation extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): StockLocationFactory
    {
        return StockLocationFactory::new();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
