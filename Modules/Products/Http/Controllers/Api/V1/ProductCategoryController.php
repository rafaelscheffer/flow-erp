<?php

declare(strict_types=1);

namespace Modules\Products\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Products\Models\ProductCategory;
use Modules\Products\Requests\StoreProductCategoryRequest;
use Modules\Products\Requests\UpdateProductCategoryRequest;
use Modules\Products\Resources\ProductCategoryResource;
use OpenApi\Attributes as OA;

class ProductCategoryController extends Controller
{
    #[OA\Get(path: '/api/v1/categories', summary: 'Lista categorias', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de categorias')])]
    public function index()
    {
        $this->authorize('viewAny', ProductCategory::class);

        return ProductCategoryResource::collection(ProductCategory::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/categories/{category}', summary: 'Exibe uma categoria', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Categoria encontrada')])]
    public function show(ProductCategory $category): ProductCategoryResource
    {
        $this->authorize('view', $category);

        return new ProductCategoryResource($category);
    }

    #[OA\Post(path: '/api/v1/categories', summary: 'Cria uma categoria', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Categoria criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $category = ProductCategory::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new ProductCategoryResource($category))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/categories/{category}', summary: 'Atualiza uma categoria', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Categoria atualizada')])]
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category): ProductCategoryResource
    {
        $category->update($request->validated());

        return new ProductCategoryResource($category);
    }

    #[OA\Delete(path: '/api/v1/categories/{category}', summary: 'Remove uma categoria', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Categoria removida')])]
    public function destroy(ProductCategory $category): Response
    {
        $this->authorize('delete', $category);

        $category->delete();

        return response()->noContent();
    }
}
