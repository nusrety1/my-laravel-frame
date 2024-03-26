<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return apiNoWrap([
                'message' => $e->getMessage()
            ], 404, ['header' => $e->getHeaders()]);
        }

        if ($e instanceof MethodNotAllowedHttpException){
            return api([
                "message" => $e->getMessage()
            ], 405);
        }

        if ($e instanceof ValidationException){
            return apiNoWrap([
               'message' => $e->getMessage(),
                'errors' => $e->validator->getMessageBag()
            ], 422);
        }

        return parent::render($request, $e);
    }
}
