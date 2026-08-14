<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;
use Modules\Financial\Requests\MarkReceivableAsPaidRequest;
use Modules\Financial\Resources\ReceivableResource;
use OpenApi\Attributes as OA;

class MarkReceivableAsPaidController extends Controller
{
    #[OA\Post(
        path: '/api/v1/receivables/{receivable}/mark-as-paid',
        summary: 'Marca uma conta a receber pendente como paga',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'receivable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Conta a receber marcada como paga'),
            new OA\Response(response: 422, description: 'Conta não está pendente'),
        ]
    )]
    public function __invoke(MarkReceivableAsPaidRequest $request, Receivable $receivable): ReceivableResource
    {
        if ($receivable->status !== ReceivableStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Apenas contas a receber pendentes podem ser marcadas como pagas.',
            ]);
        }

        $receivable->update([
            'status' => ReceivableStatus::Paid,
            'paid_at' => now(),
            'payment_method' => $request->validated('payment_method'),
        ]);

        return new ReceivableResource($receivable);
    }
}
