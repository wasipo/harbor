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
    ->withMiddleware(function (Middleware $middleware) {
        // グローバルミドルウェア（全リクエストで実行）
        $middleware->append([
            // App\Http\Middleware\TrimStrings::class,
        ]);

        // Webミドルウェアグループ（従来のKernel.phpから移行）
        $middleware->web(append: [
            App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // APIミドルウェアグループ
        $middleware->api(prepend: [
            // 'throttle:api', // 必要に応じて有効化
        ]);

        // ミドルウェアエイリアス（旧Kernel.php $middlewareAliases）
        $middleware->alias([
            'auth' => App\Http\Middleware\Authenticate::class,
            'guest' => App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => Illuminate\Routing\Middleware\ThrottleRequests::class,
            'admin' => App\Http\Middleware\CheckAdminRole::class,
        ]);

        // ミドルウェア優先順位（特定の順序で実行したい場合）
        $middleware->priority([
            Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        $exceptions->render(function (Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->render(function (Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Server Error',
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => app()->environment('production')
                        ? 'Server Error'
                        : $e->getMessage(),
                ], 500);
            }
        });
    })->create();
