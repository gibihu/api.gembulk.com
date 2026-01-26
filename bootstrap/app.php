<?php

use App\Http\Middleware\V1\AuthV1Middleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.v1' => AuthV1Middleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

            // ถ้าเป็น API request
            if ($request->is('api/*')) {

                $status = 500;

                if (method_exists($e, 'getStatusCode')) {
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => $status,
                ], $status);
            }

        });
    })->create();
