<?php

declare(strict_types=1);

namespace Modules\Inventory\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Requests\StoreStockLocationRequest;
use Modules\Inventory\Requests\UpdateStockLocationRequest;
use Modules\Inventory\Resources\StockLocationResource;
use OpenApi\Attributes as OA;

class StockLocationController extends Controller
{
    #[OA\Get(path: '/api/v1/locations', summary: 'Lista locais de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de locais')])]
    public function index()
    {
        $this->authorize('viewAny', StockLocation::class);

        return StockLocationResource::collection(StockLocation::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/locations/{location}', summary: 'Exibe um local de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Local encontrado')])]
    public function show(StockLocation $location): StockLocationResource
    {
        $this->authorize('view', $location);

        return new StockLocationResource($location);
    }

    #[OA\Post(path: '/api/v1/locations', summary: 'Cria um local de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Local criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreStockLocationRequest $request): JsonResponse
    {
        $location = StockLocation::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new StockLocationResource($location))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/locations/{location}', summary: 'Atualiza um local de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Local atualizado')])]
    public function update(UpdateStockLocationRequest $request, StockLocation $location): StockLocationResource
    {
        $location->update($request->validated());

        return new StockLocationResource($location);
    }

    #[OA\Delete(path: '/api/v1/locations/{location}', summary: 'Remove um local de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Local removido')])]
    public function destroy(StockLocation $location): Response
    {
        $this->authorize('delete', $location);

        $location->delete();

        return response()->noContent();
    }
}
