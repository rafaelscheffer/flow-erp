<?php

declare(strict_types=1);

namespace Modules\Inventory\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Resources\StockBalanceResource;
use OpenApi\Attributes as OA;

/**
 * Somente leitura — StockBalance é uma projeção derivada, nunca editável
 * diretamente (ver StockBalancePolicy).
 */
class StockBalanceController extends Controller
{
    #[OA\Get(path: '/api/v1/balances', summary: 'Lista saldos de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de saldos')])]
    public function index()
    {
        $this->authorize('viewAny', StockBalance::class);

        return StockBalanceResource::collection(StockBalance::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/balances/{balance}', summary: 'Exibe um saldo de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'balance', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Saldo encontrado')])]
    public function show(StockBalance $balance): StockBalanceResource
    {
        $this->authorize('view', $balance);

        return new StockBalanceResource($balance);
    }
}
