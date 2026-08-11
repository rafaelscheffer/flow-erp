<?php

declare(strict_types=1);

namespace Modules\Products\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Products\Database\Factories\ProductCollectionFactory;
use Modules\Products\Traits\GeneratesSlug;

class ProductCollection extends Model
{
    use Auditable;
    use GeneratesSlug;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_collections';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): ProductCollectionFactory
    {
        return ProductCollectionFactory::new();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
