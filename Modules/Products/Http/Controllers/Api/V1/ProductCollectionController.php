<?php

declare(strict_types=1);

namespace Modules\Products\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Products\Models\ProductCollection;
use Modules\Products\Requests\StoreProductCollectionRequest;
use Modules\Products\Requests\UpdateProductCollectionRequest;
use Modules\Products\Resources\ProductCollectionResource;
use OpenApi\Attributes as OA;

class ProductCollectionController extends Controller
{
    #[OA\Get(path: '/api/v1/collections', summary: 'Lista coleções', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de coleções')])]
    public function index()
    {
        $this->authorize('viewAny', ProductCollection::class);

        return ProductCollectionResource::collection(ProductCollection::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/collections/{collection}', summary: 'Exibe uma coleção', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'collection', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Coleção encontrada')])]
    public function show(ProductCollection $collection): ProductCollectionResource
    {
        $this->authorize('view', $collection);

        return new ProductCollectionResource($collection);
    }

    #[OA\Post(path: '/api/v1/collections', summary: 'Cria uma coleção', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Coleção criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreProductCollectionRequest $request): JsonResponse
    {
        $collection = ProductCollection::query()->create($request->validated());

        return (new ProductCollectionResource($collection))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/collections/{collection}', summary: 'Atualiza uma coleção', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'collection', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Coleção atualizada')])]
    public function update(UpdateProductCollectionRequest $request, ProductCollection $collection): ProductCollectionResource
    {
        $collection->update($request->validated());

        return new ProductCollectionResource($collection);
    }

    #[OA\Delete(path: '/api/v1/collections/{collection}', summary: 'Remove uma coleção', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'collection', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Coleção removida')])]
    public function destroy(ProductCollection $collection): Response
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return response()->noContent();
    }
}
