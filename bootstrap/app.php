<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->encryptCookies(except: [
            'igate_lang',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = 500;
                $message = $e->getMessage() ?: 'An unexpected error occurred';
                $errors = null;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                    $errors = $e->errors();
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                    $message = 'Unauthenticated';
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $status = 403;
                    $message = 'Unauthorized access';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $status = 404;
                    $message = 'Resource or route not found';
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $status = 404;
                    $message = 'Requested resource not found';
                } elseif ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == 23000) {
                    $status = 409;
                    $message = 'Duplicate data entry detected. This record might already exist.';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    $status = 405;
                    $message = 'Method not allowed for this route';
                }

                if (method_exists($e, 'getStatusCode')) {
                    $status = $e->getStatusCode();
                }

                $response = [
                    'status' => 'error',
                    'message' => $message,
                ];

                if ($errors) {
                    $response['errors'] = $errors;
                }

                if (config('app.debug')) {
                    $response['debug'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => array_slice($e->getTrace(), 0, 10),
                    ];
                }

                return response()->json($response, $status);
            }
        });
    })->create();
