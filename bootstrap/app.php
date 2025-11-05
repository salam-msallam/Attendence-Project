<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\UnauthenticatedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'code' => 401,
                    'message' => $e->getMessage(), 
                ],401);
            }
        });

        // $exceptions->render(function (Throwable $e, Request $request){
        //     if($request ->is('api/*')){
        //         return response()->json([
        //             'code'=>401,
        //             'message'=>'There was a problem make sure you are logged in',
        //             'error'=>$e->getMessage()
        //         ]);
        //     }
        // });
    })->create();
