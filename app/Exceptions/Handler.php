<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle API 404
        if ($exception instanceof NotFoundHttpException && $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => 'API route not found'
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
