<?php

declare(strict_types=1);

namespace Modules\Purchases\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Purchases\Actions\ReceivePurchaseOrderAction;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Resources\PurchaseOrderResource;
use OpenApi\Attributes as OA;

class ReceivePurchaseOrderController extends Controller
{
    #[OA\Post(
        path: '/api/v1/purchase-orders/{purchase_order}/receive',
        summary: 'Recebe um pedido de compra enviado, gerando a entrada de estoque de cada item',
        tags: ['Purchases'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'purchase_order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Pedido recebido'),
            new OA\Response(response: 422, description: 'Pedido não está enviado'),
        ]
    )]
    public function __invoke(Request $request, PurchaseOrder $purchaseOrder, ReceivePurchaseOrderAction $action): PurchaseOrderResource
    {
        $this->authorize('receive', $purchaseOrder);

        $purchaseOrder = $action->execute($purchaseOrder, $request->user()->id);

        return new PurchaseOrderResource($purchaseOrder->load('items'));
    }
}
