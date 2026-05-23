<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JwtAuthenticate::class,
            'custom.jwt' => \App\Http\Middleware\JwtAuthenticate::class,
            'role' => \App\Http\Middleware\AuthorizeRole::class,
            'jwt.token_version' => \App\Http\Middleware\ValidateTokenVersion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception) {
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422));
        });

        $exceptions->render(function (HttpExceptionInterface $exception) {
            if ($exception->getStatusCode() === 401) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ($exception->getStatusCode() === 403) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            if ($exception->getPrevious() instanceof HttpExceptionInterface) {
                return null;
            }

            return response()->json([
                'message' => 'Not found',
            ], 404);
        });
    })->create();
