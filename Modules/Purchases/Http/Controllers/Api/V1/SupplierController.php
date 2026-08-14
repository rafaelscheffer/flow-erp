<?php

declare(strict_types=1);

namespace Modules\Purchases\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Purchases\Models\Supplier;
use Modules\Purchases\Requests\StoreSupplierRequest;
use Modules\Purchases\Requests\UpdateSupplierRequest;
use Modules\Purchases\Resources\SupplierResource;
use OpenApi\Attributes as OA;

class SupplierController extends Controller
{
    #[OA\Get(path: '/api/v1/suppliers', summary: 'Lista fornecedores', tags: ['Purchases'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de fornecedores')])]
    public function index()
    {
        $this->authorize('viewAny', Supplier::class);

        return SupplierResource::collection(Supplier::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/suppliers/{supplier}', summary: 'Exibe um fornecedor', tags: ['Purchases'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Fornecedor encontrado')])]
    public function show(Supplier $supplier): SupplierResource
    {
        $this->authorize('view', $supplier);

        return new SupplierResource($supplier);
    }

    #[OA\Post(path: '/api/v1/suppliers', summary: 'Cria um fornecedor', tags: ['Purchases'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Fornecedor criado'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new SupplierResource($supplier))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/suppliers/{supplier}', summary: 'Atualiza um fornecedor', tags: ['Purchases'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Fornecedor atualizado')])]
    public function update(UpdateSupplierRequest $request, Supplier $supplier): SupplierResource
    {
        $supplier->update($request->validated());

        return new SupplierResource($supplier);
    }

    #[OA\Delete(path: '/api/v1/suppliers/{supplier}', summary: 'Remove um fornecedor', tags: ['Purchases'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Fornecedor removido')])]
    public function destroy(Supplier $supplier): Response
    {
        $this->authorize('delete', $supplier);

        $supplier->delete();

        return response()->noContent();
    }
}
