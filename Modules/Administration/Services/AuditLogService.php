<?php

declare(strict_types=1);

namespace Modules\Administration\Services;

use App\Enums\AuditEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Administration\Models\AuditLog;

class AuditLogService
{
    public function __construct(private readonly Request $request)
    {
    }

    public function record(Model $model, AuditEventType $event, array $old, array $new): void
    {
        AuditLog::query()->create([
            'user_id' => $this->request->user()?->getAuthIdentifier(),
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $old !== [] ? $old : null,
            'new_values' => $new !== [] ? $new : null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
