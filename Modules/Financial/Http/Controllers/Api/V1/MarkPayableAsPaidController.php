<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;
use Modules\Financial\Requests\MarkPayableAsPaidRequest;
use Modules\Financial\Resources\PayableResource;
use OpenApi\Attributes as OA;

class MarkPayableAsPaidController extends Controller
{
    #[OA\Post(
        path: '/api/v1/payables/{payable}/mark-as-paid',
        summary: 'Marca uma conta a pagar pendente como paga',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'payable', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Conta a pagar marcada como paga'),
            new OA\Response(response: 422, description: 'Conta não está pendente'),
        ]
    )]
    public function __invoke(MarkPayableAsPaidRequest $request, Payable $payable): PayableResource
    {
        if ($payable->status !== PayableStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Apenas contas a pagar pendentes podem ser marcadas como pagas.',
            ]);
        }

        $payable->update([
            'status' => PayableStatus::Paid,
            'paid_at' => now(),
            'payment_method' => $request->validated('payment_method'),
        ]);

        return new PayableResource($payable);
    }
}
