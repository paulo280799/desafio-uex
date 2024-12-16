<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
        \Log::info($exception->getMessage());
        if ($exception instanceof HttpException) {
            return response()->json(
                ['success' => false, 'message' => $exception->getMessage()],
                $exception->getStatusCode()
            );
        } elseif ($exception instanceof ValidationException) {
            return response()->json(
                ['success' => false, 'message' => $exception->validator->errors()->first()],
                422
            );
        } elseif ($exception instanceof AuthenticationException) {
            return response()->json(
                ['success' => false, 'message' => 'Unauthenticated.'],
                401
            );
        } else {
            return response()->json(
                ['success' => false, 'message' => 'Internal error'],
                500
            );
        }
    }
}
