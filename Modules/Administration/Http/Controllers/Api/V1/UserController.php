<?php

declare(strict_types=1);

namespace Modules\Administration\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Administration\Requests\StoreUserRequest;
use Modules\Administration\Requests\UpdateUserRequest;
use Modules\Administration\Resources\UserResource;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

/**
 * Resolves roles as models (not bare name strings) before syncRoles(): once
 * `auth:sanctum` authenticates a request, Laravel's Authenticate middleware
 * calls Auth::shouldUse('sanctum'), which flips Spatie's "current guard" and
 * makes name-based lookups fail with "no permission/role for guard sanctum"
 * even though every role/permission was seeded under the "web" guard.
 */
class UserController extends Controller
{
    #[OA\Get(path: '/api/v1/users', summary: 'Lista usuários', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de usuários')])]
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return UserResource::collection(User::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/users/{user}', summary: 'Exibe um usuário', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Usuário encontrado')])]
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    #[OA\Post(path: '/api/v1/users', summary: 'Cria um usuário', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Usuário criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = User::query()->create($data);
        $user->syncRoles(Role::query()->whereIn('name', $roles)->get());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/users/{user}', summary: 'Atualiza um usuário', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Usuário atualizado')])]
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        $user->update($data);

        if ($roles !== null) {
            $user->syncRoles(Role::query()->whereIn('name', $roles)->get());
        }

        return new UserResource($user);
    }

    #[OA\Delete(path: '/api/v1/users/{user}', summary: 'Remove um usuário', tags: ['Administration'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Usuário removido'), new OA\Response(response: 403, description: 'Não é possível excluir a própria conta')])]
    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
