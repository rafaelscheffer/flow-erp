<?php

declare(strict_types=1);

namespace Modules\Sales\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Models\Order;
use Modules\Sales\Requests\StoreOrderRequest;
use Modules\Sales\Requests\UpdateOrderRequest;
use Modules\Sales\Resources\OrderResource;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(path: '/api/v1/orders', summary: 'Lista pedidos de venda', tags: ['Sales'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de pedidos')])]
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        return OrderResource::collection(Order::query()->with('items')->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/orders/{order}', summary: 'Exibe um pedido de venda', tags: ['Sales'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Pedido encontrado')])]
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load('items'));
    }

    #[OA\Post(
        path: '/api/v1/orders',
        summary: 'Cria um pedido de venda (rascunho) com seus itens',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 201, description: 'Pedido criado'), new OA\Response(response: 422, description: 'Erro de validação')]
    )]
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $order = DB::transaction(function () use ($data, $items, $request): Order {
            $order = Order::query()->create([
                ...$data,
                'order_date' => $data['order_date'] ?? now()->toDateString(),
                'status' => OrderStatus::Draft,
                'created_by' => $request->user()->id,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        return (new OrderResource($order->load('items')))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/v1/orders/{order}',
        summary: 'Atualiza um pedido de venda em rascunho (e, opcionalmente, substitui seus itens)',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Pedido atualizado'),
            new OA\Response(response: 422, description: 'Pedido não está mais em rascunho ou erro de validação'),
        ]
    )]
    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->ensureIsDraft($order, 'editados');

        $data = $request->validated();
        $items = $data['items'] ?? null;
        unset($data['items']);

        DB::transaction(function () use ($order, $data, $items): void {
            $order->update($data);

            if ($items !== null) {
                $order->items()->delete();

                foreach ($items as $item) {
                    $order->items()->create($item);
                }
            }
        });

        return new OrderResource($order->load('items'));
    }

    #[OA\Delete(
        path: '/api/v1/orders/{order}',
        summary: 'Remove um pedido de venda em rascunho',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Pedido removido'),
            new OA\Response(response: 422, description: 'Pedido não está mais em rascunho'),
        ]
    )]
    public function destroy(Order $order): Response
    {
        $this->authorize('delete', $order);
        $this->ensureIsDraft($order, 'removidos');

        $order->delete();

        return response()->noContent();
    }

    private function ensureIsDraft(Order $order, string $action): void
    {
        if ($order->status !== OrderStatus::Draft) {
            throw ValidationException::withMessages([
                'status' => "Apenas pedidos em rascunho podem ser {$action}.",
            ]);
        }
    }
}
