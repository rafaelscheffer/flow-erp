<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Financial\Models\Account;
use Modules\Financial\Requests\StoreAccountRequest;
use Modules\Financial\Requests\UpdateAccountRequest;
use Modules\Financial\Resources\AccountResource;
use OpenApi\Attributes as OA;

class AccountController extends Controller
{
    #[OA\Get(path: '/api/v1/accounts', summary: 'Lista contas contábeis', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de contas')])]
    public function index()
    {
        $this->authorize('viewAny', Account::class);

        return AccountResource::collection(Account::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/accounts/{account}', summary: 'Exibe uma conta contábil', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'account', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Conta encontrada')])]
    public function show(Account $account): AccountResource
    {
        $this->authorize('view', $account);

        return new AccountResource($account);
    }

    #[OA\Post(path: '/api/v1/accounts', summary: 'Cria uma conta contábil', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Conta criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = Account::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new AccountResource($account))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/accounts/{account}', summary: 'Atualiza uma conta contábil', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'account', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Conta atualizada')])]
    public function update(UpdateAccountRequest $request, Account $account): AccountResource
    {
        $account->update($request->validated());

        return new AccountResource($account);
    }

    #[OA\Delete(path: '/api/v1/accounts/{account}', summary: 'Remove uma conta contábil', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'account', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Conta removida')])]
    public function destroy(Account $account): Response
    {
        $this->authorize('delete', $account);

        $account->delete();

        return response()->noContent();
    }
}
