<?php

declare(strict_types=1);

namespace Modules\Inventory\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Inventory\Models\StockReservation;
use Modules\Inventory\Requests\StoreStockReservationRequest;
use Modules\Inventory\Requests\UpdateStockReservationRequest;
use Modules\Inventory\Resources\StockReservationResource;
use OpenApi\Attributes as OA;

/**
 * Sem destroy — reservas só transicionam de status (Ativa/Liberada/Atendida),
 * nunca são apagadas (ver StockReservationPolicy).
 */
class StockReservationController extends Controller
{
    #[OA\Get(path: '/api/v1/reservations', summary: 'Lista reservas de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Lista paginada de reservas')])]
    public function index()
    {
        $this->authorize('viewAny', StockReservation::class);

        return StockReservationResource::collection(StockReservation::query()->paginate());
    }

    #[OA\Get(path: '/api/v1/reservations/{reservation}', summary: 'Exibe uma reserva de estoque', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'reservation', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Reserva encontrada')])]
    public function show(StockReservation $reservation): StockReservationResource
    {
        $this->authorize('view', $reservation);

        return new StockReservationResource($reservation);
    }

    #[OA\Post(path: '/api/v1/reservations', summary: 'Cria uma reserva de estoque', tags: ['Inventory'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Reserva criada'), new OA\Response(response: 422, description: 'Erro de validação')])]
    public function store(StoreStockReservationRequest $request): JsonResponse
    {
        $reservation = StockReservation::query()->create($request->validated());

        return (new StockReservationResource($reservation))->response()->setStatusCode(201);
    }

    #[OA\Put(path: '/api/v1/reservations/{reservation}', summary: 'Atualiza uma reserva de estoque (quantidade ou status)', tags: ['Inventory'], security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'reservation', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Reserva atualizada')])]
    public function update(UpdateStockReservationRequest $request, StockReservation $reservation): StockReservationResource
    {
        $reservation->update($request->validated());

        return new StockReservationResource($reservation);
    }
}
