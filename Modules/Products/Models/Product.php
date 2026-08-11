<?php

declare(strict_types=1);

namespace Modules\Products\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Products\Database\Factories\ProductFactory;
use Modules\Products\Enums\ProductType;
use Modules\Products\Traits\GeneratesSlug;

class Product extends Model
{
    use Auditable;
    use GeneratesSlug;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_category_id',
        'brand_id',
        'product_collection_id',
        'type',
        'name',
        'slug',
        'description',
        'internal_code',
        'sku',
        'ean',
        'ncm',
        'weight',
        'height',
        'width',
        'length',
        'cost_price',
        'sale_price',
        'promotional_price',
        'min_stock',
        'max_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'weight' => 'decimal:3',
            'height' => 'decimal:3',
            'width' => 'decimal:3',
            'length' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'promotional_price' => 'decimal:2',
            'min_stock' => 'integer',
            'max_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ProductCollection::class, 'product_collection_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
