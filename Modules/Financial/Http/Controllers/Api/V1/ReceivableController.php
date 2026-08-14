<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;
use Modules\Financial\Requests\StoreReceivableRequest;
use Modules\Financial\Requests\UpdateReceivableRequest;
use Modules\Financial\Resources\ReceivableResource;
use OpenApi\Attributes as OA;

class ReceivableController extends Controller
{
    #[OA\Get(path: '/api/v1/receivables', summary: 'Lista contas a receber', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de contas a receber')])]
    public function index()
    {
        $this->authorize('viewAny', Receivable::class);

        return ReceivableResource::collection(Receivable::query()->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/receivables/{receivable}', summary: 'Exibe uma conta a receber', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'receivable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Conta a receber encontrada')])]
    public function show(Receivable $receivable): ReceivableResource
    {
        $this->authorize('view', $receivable);

        return new ReceivableResource($receivable);
    }

    #[OA\Post(path: '/api/v1/receivables', summary: 'Cria uma conta a receber manual', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Conta a receber criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreReceivableRequest $request): JsonResponse
    {
        $receivable = Receivable::query()->create([
            ...$request->validated(),
            'status' => ReceivableStatus::Pending,
            'created_by' => $request->user()->id,
        ]);

        return (new ReceivableResource($receivable))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/v1/receivables/{receivable}',
        summary: 'Atualiza uma conta a receber pendente',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'receivable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Conta a receber atualizada'), new OA\Response(response: 422, description: 'Conta não está mais pendente')]
    )]
    public function update(UpdateReceivableRequest $request, Receivable $receivable): ReceivableResource
    {
        $this->ensureIsPending($receivable, 'editadas');

        $receivable->update($request->validated());

        return new ReceivableResource($receivable);
    }

    #[OA\Delete(
        path: '/api/v1/receivables/{receivable}',
        summary: 'Remove uma conta a receber pendente',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'receivable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Conta a receber removida'), new OA\Response(response: 422, description: 'Conta não está mais pendente')]
    )]
    public function destroy(Receivable $receivable): Response
    {
        $this->authorize('delete', $receivable);
        $this->ensureIsPending($receivable, 'removidas');

        $receivable->delete();

        return response()->noContent();
    }

    private function ensureIsPending(Receivable $receivable, string $action): void
    {
        if ($receivable->status !== ReceivableStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => "Apenas contas a receber pendentes podem ser {$action}.",
            ]);
        }
    }
}
