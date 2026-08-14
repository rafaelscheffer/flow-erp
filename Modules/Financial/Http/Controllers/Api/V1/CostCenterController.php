<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Financial\Models\CostCenter;
use Modules\Financial\Requests\StoreCostCenterRequest;
use Modules\Financial\Requests\UpdateCostCenterRequest;
use Modules\Financial\Resources\CostCenterResource;
use OpenApi\Attributes as OA;

class CostCenterController extends Controller
{
    #[OA\Get(path: '/api/v1/cost-centers', summary: 'Lista centros de custo', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de centros de custo')])]
    public function index()
    {
        $this->authorize('viewAny', CostCenter::class);

        return CostCenterResource::collection(CostCenter::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/cost-centers/{cost_center}', summary: 'Exibe um centro de custo', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'cost_center', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Centro de custo encontrado')])]
    public function show(CostCenter $costCenter): CostCenterResource
    {
        $this->authorize('view', $costCenter);

        return new CostCenterResource($costCenter);
    }

    #[OA\Post(path: '/api/v1/cost-centers', summary: 'Cria um centro de custo', tags: ['Financial'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Centro de custo criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreCostCenterRequest $request): JsonResponse
    {
        $costCenter = CostCenter::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new CostCenterResource($costCenter))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/cost-centers/{cost_center}', summary: 'Atualiza um centro de custo', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'cost_center', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Centro de custo atualizado')])]
    public function update(UpdateCostCenterRequest $request, CostCenter $costCenter): CostCenterResource
    {
        $costCenter->update($request->validated());

        return new CostCenterResource($costCenter);
    }

    #[OA\Delete(path: '/api/v1/cost-centers/{cost_center}', summary: 'Remove um centro de custo', tags: ['Financial'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'cost_center', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Centro de custo removido')])]
    public function destroy(CostCenter $costCenter): Response
    {
        $this->authorize('delete', $costCenter);

        $costCenter->delete();

        return response()->noContent();
    }
}
