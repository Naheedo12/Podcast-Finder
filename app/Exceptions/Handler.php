<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Ressource non trouvée.'
            ], 404);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Endpoint non trouvé.'
            ], 404);
        }

        return parent::render($request, $exception);
    }
}