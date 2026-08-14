<?php

declare(strict_types=1);

namespace Modules\Administration\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Administration\Requests\IssueTokenRequest;
use Modules\Administration\Resources\UserResource;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/token',
        summary: 'Autentica com e-mail/senha e emite um token pessoal',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'device_name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@flowerp.test'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'meu-integrador'),
                    new OA\Property(
                        property: 'abilities',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        description: 'Subconjunto opcional das permissões do usuário para restringir o token (ex.: token só-leitura). Omitido = todas as permissões do usuário.'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token emitido',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'token', type: 'string'),
                    new OA\Property(property: 'abilities', type: 'array', items: new OA\Items(type: 'string')),
                ])
            ),
            new OA\Response(response: 422, description: 'Credenciais inválidas'),
        ]
    )]
    public function token(IssueTokenRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        $userAbilities = $user->getAllPermissions()->pluck('name')->all();
        $requested = $request->input('abilities');
        $abilities = $requested === null ? $userAbilities : array_values(array_intersect($userAbilities, $requested));

        $token = $user->createToken($request->string('device_name')->toString(), $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            'abilities' => $abilities,
        ]);
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Revoga o token pessoal usado na requisição',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 204, description: 'Token revogado')]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/v1/auth/me',
        summary: 'Retorna o usuário autenticado, seus papéis e permissões',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 200, description: 'Usuário autenticado')]
    )]
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
