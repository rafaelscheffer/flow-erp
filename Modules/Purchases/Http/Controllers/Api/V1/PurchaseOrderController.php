<?php

declare(strict_types=1);

namespace Modules\Purchases\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Requests\StorePurchaseOrderRequest;
use Modules\Purchases\Requests\UpdatePurchaseOrderRequest;
use Modules\Purchases\Resources\PurchaseOrderResource;
use OpenApi\Attributes as OA;

class PurchaseOrderController extends Controller
{
    #[OA\Get(path: '/api/v1/purchase-orders', summary: 'Lista pedidos de compra', tags: ['Purchases'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de pedidos de compra')])]
    public function index()
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        return PurchaseOrderResource::collection(PurchaseOrder::query()->with('items')->latest()->paginate());
    }

    #[OA\Get(path: '/api/v1/purchase-orders/{purchase_order}', summary: 'Exibe um pedido de compra', tags: ['Purchases'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'purchase_order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Pedido encontrado')])]
    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        $this->authorize('view', $purchaseOrder);

        return new PurchaseOrderResource($purchaseOrder->load('items'));
    }

    #[OA\Post(
        path: '/api/v1/purchase-orders',
        summary: 'Cria um pedido de compra (rascunho) com seus itens',
        tags: ['Purchases'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 201, description: 'Pedido criado'), new OA\Response(response: 422, description: 'Erro de validação')]
    )]
    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $purchaseOrder = DB::transaction(function () use ($data, $items, $request): PurchaseOrder {
            $purchaseOrder = PurchaseOrder::query()->create([
                ...$data,
                'order_date' => $data['order_date'] ?? now()->toDateString(),
                'status' => PurchaseOrderStatus::Draft,
                'created_by' => $request->user()->id,
            ]);

            foreach ($items as $item) {
                $purchaseOrder->items()->create($item);
            }

            return $purchaseOrder;
        });

        return (new PurchaseOrderResource($purchaseOrder->load('items')))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/v1/purchase-orders/{purchase_order}',
        summary: 'Atualiza um pedido de compra em rascunho (e, opcionalmente, substitui seus itens)',
        tags: ['Purchases'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'purchase_order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Pedido atualizado'),
            new OA\Response(response: 422, description: 'Pedido não está mais em rascunho ou erro de validação'),
        ]
    )]
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        $this->ensureIsDraft($purchaseOrder, 'editados');

        $data = $request->validated();
        $items = $data['items'] ?? null;
        unset($data['items']);

        DB::transaction(function () use ($purchaseOrder, $data, $items): void {
            $purchaseOrder->update($data);

            if ($items !== null) {
                $purchaseOrder->items()->delete();

                foreach ($items as $item) {
                    $purchaseOrder->items()->create($item);
                }
            }
        });

        return new PurchaseOrderResource($purchaseOrder->load('items'));
    }

    #[OA\Delete(
        path: '/api/v1/purchase-orders/{purchase_order}',
        summary: 'Remove um pedido de compra em rascunho',
        tags: ['Purchases'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'purchase_order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Pedido removido'),
            new OA\Response(response: 422, description: 'Pedido não está mais em rascunho'),
        ]
    )]
    public function destroy(PurchaseOrder $purchaseOrder): Response
    {
        $this->authorize('delete', $purchaseOrder);
        $this->ensureIsDraft($purchaseOrder, 'removidos');

        $purchaseOrder->delete();

        return response()->noContent();
    }

    private function ensureIsDraft(PurchaseOrder $purchaseOrder, string $action): void
    {
        if ($purchaseOrder->status !== PurchaseOrderStatus::Draft) {
            throw ValidationException::withMessages([
                'status' => "Apenas pedidos em rascunho podem ser {$action}.",
            ]);
        }
    }
}
