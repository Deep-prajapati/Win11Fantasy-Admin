<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;


use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:600,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => FALSE, 'status' => config('constant.STATUS_UNAUTHORIZED'), 'message' => __("message.UNAUTHORIZED_ACCESS")], config('constant.STATUS_UNAUTHORIZED'));
            }
        });
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {

            if ($request->is('api/*')) {
                return response()->json(['success' => FALSE, 'status' =>  config('constant.STATUS_NOT_FOUND'), 'message' => __("message.PAGE_NOT_FOUND")], config('constant.STATUS_NOT_FOUND'));
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => FALSE, 'status' => config('constant.TOO_MANY_REQUESTS'), 'message' => __("message.TOO_MANY_REQUESTS")],  config('constant.TOO_MANY_REQUESTS'));
            }
        });


        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => FALSE, 'status' => config('constant.STATUS_NOT_FOUND'), 'message' => __("message.PAGE_NOT_FOUND")], config('constant.STATUS_NOT_FOUND'));
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => FALSE, 'status' => config('constant.STATUS_METHOD_NOT_ALLOWED'), 'message' => __("message.METHOD_NOT_ALLOWED")], config('constant.STATUS_METHOD_NOT_ALLOWED'));
            }
        });
    })->create();
