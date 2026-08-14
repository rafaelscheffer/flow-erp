<?php

declare(strict_types=1);

namespace Modules\Products\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Products\Models\Brand;
use Modules\Products\Requests\StoreBrandRequest;
use Modules\Products\Requests\UpdateBrandRequest;
use Modules\Products\Resources\BrandResource;
use OpenApi\Attributes as OA;

class BrandController extends Controller
{
    #[OA\Get(path: '/api/v1/brands', summary: 'Lista marcas', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de marcas')])]
    public function index()
    {
        $this->authorize('viewAny', Brand::class);

        return BrandResource::collection(Brand::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/brands/{brand}', summary: 'Exibe uma marca', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'brand', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Marca encontrada')])]
    public function show(Brand $brand): BrandResource
    {
        $this->authorize('view', $brand);

        return new BrandResource($brand);
    }

    #[OA\Post(path: '/api/v1/brands', summary: 'Cria uma marca', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Marca criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = Brand::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new BrandResource($brand))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/brands/{brand}', summary: 'Atualiza uma marca', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'brand', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Marca atualizada')])]
    public function update(UpdateBrandRequest $request, Brand $brand): BrandResource
    {
        $brand->update($request->validated());

        return new BrandResource($brand);
    }

    #[OA\Delete(path: '/api/v1/brands/{brand}', summary: 'Remove uma marca', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'brand', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Marca removida')])]
    public function destroy(Brand $brand): Response
    {
        $this->authorize('delete', $brand);

        $brand->delete();

        return response()->noContent();
    }
}
