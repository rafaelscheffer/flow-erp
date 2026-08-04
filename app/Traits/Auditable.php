<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\AuditEventType;
use Illuminate\Database\Eloquent\Model;
use Modules\Administration\Services\AuditLogService;

/**
 * @mixin Model
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            static::recordAuditEvent($model, AuditEventType::Created, [], $model->getAuditableAttributes());
        });

        static::updated(function (Model $model): void {
            $changes = $model->getAuditableChanges();

            if ($changes === []) {
                return;
            }

            static::recordAuditEvent(
                $model,
                AuditEventType::Updated,
                array_intersect_key($model->getRawOriginal(), $changes),
                $changes,
            );
        });

        static::deleted(function (Model $model): void {
            static::recordAuditEvent($model, AuditEventType::Deleted, $model->getAuditableAttributes(), []);
        });
    }

    protected static function recordAuditEvent(Model $model, AuditEventType $event, array $old, array $new): void
    {
        if (app()->runningInConsole() && ! app()->environment('testing')) {
            return;
        }

        app(AuditLogService::class)->record($model, $event, $old, $new);
    }

    public function getAuditableChanges(): array
    {
        return array_diff_key($this->getAuditableAttributes($this->getChanges()), array_flip($this->auditExcludedAttributes()));
    }

    public function getAuditableAttributes(?array $attributes = null): array
    {
        $attributes ??= $this->getAttributes();

        return array_diff_key($attributes, array_flip($this->auditExcludedAttributes()));
    }

    protected function auditExcludedAttributes(): array
    {
        return array_merge(['password', 'remember_token'], $this->auditExclude ?? []);
    }
}
