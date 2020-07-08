<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {

            return response()->json([
                'error' => 'Invalid Token'
            ]);
        } elseif ($exception instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response()->json([
                'error' => 'Token has Expired'
            ]);
        } elseif($exception instanceof Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
            return response()->json([
                'error' => 'Blacklisted Token'
            ]);
            
        } elseif ($exception instanceof Tymon\JWTAuth\Exceptions\JWTException) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        } else {
            if ($exception->getMessage() === 'User Not Found') {

                return response()->json([
                        "error" => "User Not Found",
                    ] );
            }

            // return response()->json([
            //         'error' => 'Authorization Token not found',
            //     ]);
            return parent::render($request, $exception);
        }
    }
}
