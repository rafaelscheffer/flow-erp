<?php

declare(strict_types=1);

namespace Modules\Sales\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sales\Actions\ConfirmSaleAction;
use Modules\Sales\Models\Order;
use Modules\Sales\Resources\OrderResource;
use OpenApi\Attributes as OA;

class ConfirmOrderController extends Controller
{
    #[OA\Post(
        path: '/api/v1/orders/{order}/confirm',
        summary: 'Confirma um pedido de venda em rascunho, gerando a saída de estoque de cada item',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Pedido confirmado'),
            new OA\Response(response: 422, description: 'Pedido não está em rascunho'),
        ]
    )]
    public function __invoke(Request $request, Order $order, ConfirmSaleAction $action): OrderResource
    {
        $this->authorize('confirm', $order);

        $order = $action->execute($order, $request->user()->id);

        return new OrderResource($order->load('items'));
    }
}
