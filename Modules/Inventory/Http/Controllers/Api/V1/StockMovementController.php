<?php

declare(strict_types=1);

namespace Modules\Inventory\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Requests\StoreStockMovementRequest;
use Modules\Inventory\Resources\StockMovementResource;
use OpenApi\Attributes as OA;

/**
 * Sem update/destroy — StockMovement é um ledger imutável (ver StockMovementPolicy).
 */
class StockMovementController extends Controller
{
    #[OA\Get(path: '/api/v1/movements', summary: 'Lista movimentos de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de movimentos')])]
    public function index()
    {
        $this->authorize('viewAny', StockMovement::class);

        return StockMovementResource::collection(StockMovement::query()->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/movements/{movement}', summary: 'Exibe um movimento de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'movement', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Movimento encontrado')])]
    public function show(StockMovement $movement): StockMovementResource
    {
        $this->authorize('view', $movement);

        return new StockMovementResource($movement);
    }

    #[OA\Post(
        path: '/api/v1/movements',
        summary: 'Registra um movimento de estoque manual (entrada, saída, inventário ou ajuste)',
        tags: ['Inventory'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 201, description: 'Movimento registrado'), new OA\Response(response: 422, description: 'Erro de validação')]
    )]
    public function store(StoreStockMovementRequest $request): JsonResponse
    {
        $movement = StockMovement::create($request->validated());

        return (new StockMovementResource($movement))->response()->setStatusCode(201);
    }
}
