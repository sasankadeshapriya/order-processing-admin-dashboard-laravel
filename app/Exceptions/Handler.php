<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

// Import RouteNotFoundException

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if (
            $exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException ||
            $exception instanceof RouteNotFoundException
        ) {
            $errorCode = 404;
            $errorMessage = 'The page you are looking for does not exist.';
        } else {
            $errorCode = 500;
            $errorMessage = 'An internal server error has occurred.';
        }

        return response()->view('pages.error', ['errorCode' => $errorCode, 'errorMessage' => $errorMessage], $errorCode);
    }

}
