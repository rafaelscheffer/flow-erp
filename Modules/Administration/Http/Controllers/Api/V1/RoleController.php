<?php

declare(strict_types=1);

namespace Modules\Administration\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Administration\Requests\StoreRoleRequest;
use Modules\Administration\Requests\UpdateRoleRequest;
use Modules\Administration\Resources\RoleResource;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Explicitly forces guard_name 'web' on create and resolves permissions as
 * models (not bare name strings) before syncPermissions(): once
 * `auth:sanctum` authenticates a request, Laravel's Authenticate middleware
 * calls Auth::shouldUse('sanctum'), which flips Spatie's "current guard".
 * Left alone, a Role created here would silently get guard_name 'sanctum'
 * (invisible to the Filament panel) and syncPermissions() by name would fail
 * with "no permission for guard sanctum" — even though every permission was
 * seeded under 'web'.
 */
class RoleController extends Controller
{
    #[OA\Get(path: '/api/v1/roles', summary: 'Lista papéis', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de papéis')])]
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        return RoleResource::collection(Role::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/roles/{role}', summary: 'Exibe um papel', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Papel encontrado')])]
    public function show(Role $role): RoleResource
    {
        $this->authorize('view', $role);

        return new RoleResource($role);
    }

    #[OA\Post(path: '/api/v1/roles', summary: 'Cria um papel', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Papel criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = Role::query()->create([...$data, 'guard_name' => 'web']);
        $role->syncPermissions(Permission::query()->whereIn('name', $permissions)->get());

        return (new RoleResource($role))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/roles/{role}', summary: 'Atualiza um papel', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Papel atualizado')])]
    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? null;
        unset($data['permissions']);

        $role->update($data);

        if ($permissions !== null) {
            $role->syncPermissions(Permission::query()->whereIn('name', $permissions)->get());
        }

        return new RoleResource($role);
    }

    #[OA\Delete(path: '/api/v1/roles/{role}', summary: 'Remove um papel', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Papel removido')])]
    public function destroy(Role $role): Response
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->noContent();
    }
}
