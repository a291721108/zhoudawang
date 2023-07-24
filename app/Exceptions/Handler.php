<?php

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use Throwable;

//use App\Http\Controllers\Api\ApiController;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $e)
    {
        if (Str::contains($request->server('REQUEST_URI'), '/api/')) {
            $api = new ApiController();

            if ($e instanceof AuthenticationException) {
                $response = $api->unAuthorized();
            } else if ($e instanceof ValidationException) {
                $response = $api->badRequest($e->validator->errors()->first());
            } else if ($e instanceof ModelNotFoundException) {
                $response = $api->notFond();
            } else if ($e instanceof NotFoundHttpException) {
                $response = $api->notFond();
            } else if ($e instanceof MethodNotAllowedHttpException) {
                $response = $api->methodNotAllow();
            } else {
                $response = $api->serviceUnavailable($e->getMessage());
            }
            return $response;
        } else {
            return parent::render($request, $e);
        }
    }

}


