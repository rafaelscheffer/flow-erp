<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        // Não existe rota web "login" nesta app (o Filament usa seu próprio
        // login de painel) — sem isso, um guest não autenticado bateria em
        // route('login') e derrubaria a request com 500 em vez de 401/403.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A app não tem rota web "login" (o Filament cuida do próprio login) —
        // sem isso, um guest sem header Accept: application/json batendo numa
        // rota api/* dispararia RouteNotFoundException(login) em vez de 401.
        $exceptions->shouldRenderJsonWhen(
            fn ($request, Throwable $e): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
