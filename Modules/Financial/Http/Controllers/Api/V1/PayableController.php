<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;
use Modules\Financial\Requests\StorePayableRequest;
use Modules\Financial\Requests\UpdatePayableRequest;
use Modules\Financial\Resources\PayableResource;
use OpenApi\Attributes as OA;

class PayableController extends Controller
{
    #[OA\Get(path: '/api/v1/payables', summary: 'Lista contas a pagar', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de contas a pagar')])]
    public function index()
    {
        $this->authorize('viewAny', Payable::class);

        return PayableResource::collection(Payable::query()->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/payables/{payable}', summary: 'Exibe uma conta a pagar', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'payable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Conta a pagar encontrada')])]
    public function show(Payable $payable): PayableResource
    {
        $this->authorize('view', $payable);

        return new PayableResource($payable);
    }

    #[OA\Post(path: '/api/v1/payables', summary: 'Cria uma conta a pagar manual', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Conta a pagar criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StorePayableRequest $request): JsonResponse
    {
        $payable = Payable::query()->create([
            ...$request->validated(),
            'status' => PayableStatus::Pending,
            'created_by' => $request->user()->id,
        ]);

        return (new PayableResource($payable))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/v1/payables/{payable}',
        summary: 'Atualiza uma conta a pagar pendente',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'payable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Conta a pagar atualizada'), new OA\Response(response: 422, description: 'Conta não está mais pendente')]
    )]
    public function update(UpdatePayableRequest $request, Payable $payable): PayableResource
    {
        $this->ensureIsPending($payable, 'editadas');

        $payable->update($request->validated());

        return new PayableResource($payable);
    }

    #[OA\Delete(
        path: '/api/v1/payables/{payable}',
        summary: 'Remove uma conta a pagar pendente',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'payable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Conta a pagar removida'), new OA\Response(response: 422, description: 'Conta não está mais pendente')]
    )]
    public function destroy(Payable $payable): Response
    {
        $this->authorize('delete', $payable);
        $this->ensureIsPending($payable, 'removidas');

        $payable->delete();

        return response()->noContent();
    }

    private function ensureIsPending(Payable $payable, string $action): void
    {
        if ($payable->status !== PayableStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => "Apenas contas a pagar pendentes podem ser {$action}.",
            ]);
        }
    }
}
