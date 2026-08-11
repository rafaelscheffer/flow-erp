<?php

declare(strict_types=1);

namespace Modules\Products\Traits;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    protected static function bootGeneratesSlug(): void
    {
        static::creating(function (self $model): void {
            $model->slug ??= Str::slug($model->name);
        });
    }
}
