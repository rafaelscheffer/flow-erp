<?php

declare(strict_types=1);

namespace Modules\Customers\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Customers\Models\Customer;
use Modules\Customers\Requests\StoreCustomerRequest;
use Modules\Customers\Requests\UpdateCustomerRequest;
use Modules\Customers\Resources\CustomerResource;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    #[OA\Get(
        path: '/api/v1/customers',
        summary: 'Lista clientes',
        tags: ['Customers'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 200, description: 'Lista paginada de clientes')]
    )]
    public function index()
    {
        $this->authorize('viewAny', Customer::class);

        return CustomerResource::collection(Customer::query()->paginate());
    }

    #[OA\Get(
        path: '/api/v1/customers/{customer}',
        summary: 'Exibe um cliente',
        tags: ['Customers'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Cliente encontrado'),
            new OA\Response(response: 404, description: 'Cliente não encontrado'),
        ]
    )]
    public function show(Customer $customer): CustomerResource
    {
        $this->authorize('view', $customer);

        return new CustomerResource($customer);
    }

    #[OA\Post(
        path: '/api/v1/customers',
        summary: 'Cria um cliente',
        tags: ['Customers'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 201, description: 'Cliente criado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::query()->create([...$request->validated(), 'is_active' => $request->boolean('is_active', true)]);

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/v1/customers/{customer}',
        summary: 'Atualiza um cliente',
        tags: ['Customers'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Cliente atualizado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer);
    }

    #[OA\Delete(
        path: '/api/v1/customers/{customer}',
        summary: 'Remove um cliente',
        tags: ['Customers'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Cliente removido')]
    )]
    public function destroy(Customer $customer): Response
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return response()->noContent();
    }
}
