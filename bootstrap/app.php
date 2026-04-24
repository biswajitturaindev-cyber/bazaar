<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/memberapi.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'member.api' => \App\Http\Middleware\VerifyMemberApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ Handle API 404 (Route not found)
        $exceptions->render(function (NotFoundHttpException $e, $request) {

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'API route not found'
                ], 404);
            }
        });

        // ✅ Handle all other API errors
        $exceptions->render(function (Throwable $e, $request) {

            if ($request->is('api/*')) {

                $status = 500;

                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'status' => false,
                    'message' => $status == 500
                        ? 'Something went wrong'
                        : $e->getMessage()
                ], $status);
            }
        });
    })->create();
