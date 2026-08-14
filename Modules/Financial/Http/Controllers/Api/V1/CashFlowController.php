<?php

declare(strict_types=1);

namespace Modules\Financial\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Financial\Services\CashFlowCalculator;
use OpenApi\Attributes as OA;

class CashFlowController extends Controller
{
    #[OA\Get(
        path: '/api/v1/cash-flow',
        summary: 'Resumo do fluxo de caixa (recebido/pago/saldo no período) e totais dos últimos meses',
        tags: ['Financial'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'start', in: 'query', description: 'Início do período (Y-m-d). Padrão: início do mês atual.', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end', in: 'query', description: 'Fim do período (Y-m-d). Padrão: fim do mês atual.', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'months', in: 'query', description: 'Quantidade de meses para os totais mensais. Padrão: 6.', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Resumo e totais mensais do fluxo de caixa')]
    )]
    public function __invoke(Request $request, CashFlowCalculator $calculator): JsonResponse
    {
        abort_unless($request->user()->can('cash-flow.view'), 403);

        $start = $request->filled('start') ? CarbonImmutable::parse($request->string('start')->toString()) : CarbonImmutable::now()->startOfMonth();
        $end = $request->filled('end') ? CarbonImmutable::parse($request->string('end')->toString()) : CarbonImmutable::now()->endOfMonth();
        $months = (int) $request->integer('months', 6);

        return response()->json([
            'summary' => $calculator->summary($start, $end),
            'monthly_totals' => $calculator->monthlyTotals($months),
        ]);
    }
}
