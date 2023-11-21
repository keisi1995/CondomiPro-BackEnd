<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ErrorException;
use Illuminate\Database\QueryException;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $ex, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return ApiResponse::error($ex->getMessage(), Response::HTTP_NOT_FOUND);
            }
        });

        $this->renderable(function (HttpException $ex, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return ApiResponse::error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });

        $this->renderable(function (ErrorException $ex, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return ApiResponse::error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
        
        $this->renderable(function (QueryException $ex, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                $message = (env('APP_DEBUG', false)) ? $ex->getMessage() : 'Code Error Query: ' . ($ex->errorInfo)[1];
                return ApiResponse::error($message, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
