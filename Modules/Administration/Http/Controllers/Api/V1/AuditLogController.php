<?php

declare(strict_types=1);

namespace Modules\Administration\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Modules\Administration\Models\AuditLog;
use Modules\Administration\Resources\AuditLogResource;
use OpenApi\Attributes as OA;

/**
 * Somente leitura — o log de auditoria nunca é criado/editado manualmente
 * (ver AuditLogPolicy, que só define viewAny/view).
 */
class AuditLogController extends Controller
{
    #[OA\Get(path: '/api/v1/audit-logs', summary: 'Lista o log de auditoria', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de entradas de auditoria')])]
    public function index()
    {
        $this->authorize('viewAny', AuditLog::class);

        return AuditLogResource::collection(AuditLog::query()->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/audit-logs/{audit_log}', summary: 'Exibe uma entrada do log de auditoria', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'audit_log', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Entrada encontrada')])]
    public function show(AuditLog $auditLog): AuditLogResource
    {
        $this->authorize('view', $auditLog);

        return new AuditLogResource($auditLog);
    }
}
