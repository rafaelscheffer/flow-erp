<?php

declare(strict_types=1);

namespace Modules\Products\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Products\Models\Product;
use Modules\Products\Requests\StoreProductRequest;
use Modules\Products\Requests\UpdateProductRequest;
use Modules\Products\Resources\ProductResource;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(path: '/api/v1/products', summary: 'Lista produtos', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de produtos')])]
    public function index()
    {
        $this->authorize('viewAny', Product::class);

        return ProductResource::collection(Product::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/products/{product}', summary: 'Exibe um produto', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Produto encontrado')])]
    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    #[OA\Post(path: '/api/v1/products', summary: 'Cria um produto', tags: ['Products'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Produto criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::query()->create($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/products/{product}', summary: 'Atualiza um produto', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Produto atualizado')])]
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    #[OA\Delete(path: '/api/v1/products/{product}', summary: 'Remove um produto', tags: ['Products'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Produto removido')])]
    public function destroy(Product $product): Response
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->noContent();
    }
}
