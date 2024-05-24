<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    protected function unauthenticated($request, AuthenticationException $exception)
    {

        // dd($exception->getMessage());
        // return $request->expectsJson()
        // ? response()->json(['message' => $exception->getMessage()], 401)
        // : redirect()->guest($exception->redirectTo() ?? route('index'));


        if ($request->expectsJson()) {
            return response()->json(['status'    => "failed",
                'message'    => "Unauthenticated! token not found",], 401);
        }
    
        // $response = [
        //     'status'    => "failed",
        //     'message'    => "Unauthenticated! token not found",
        // ];
        // return response($response, 201);
        return redirect()->guest(route('login'));
    }


    public function render($request, Throwable $exception)
    {
        // dd($request);
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return redirect()->back()->with('permission','User have not permission for this page access.');
          
        }
        // return view('home');  
        return parent::render($request, $exception);
    }
    public function register()
    {
        //
    }
}
