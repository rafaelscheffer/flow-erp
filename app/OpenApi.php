<?php

declare(strict_types=1);

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'FlowERP API',
    description: 'API REST v1 do FlowERP — cobre o núcleo de Clientes, Produtos, Estoque e Vendas. Autenticação via token pessoal (Sanctum).'
)]
#[OA\Server(url: '/', description: 'Servidor atual')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    description: 'Token pessoal emitido por POST /api/v1/auth/token. Informe apenas o token — o prefixo "Bearer" é adicionado automaticamente pelo Swagger UI.'
)]
class OpenApi
{
    //
}
