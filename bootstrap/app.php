<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Heroku等のプロキシ配下では、HTTPSの処理をプロキシが行い、Laravelには HTTP として届く。
        // プロキシが付ける X-Forwarded-Proto などのヘッダーを信用して、元のリクエストが
        // HTTPSだったかを判断させる。プロキシのない自前のサーバー(Nginx)ではヘッダーが付かないので、
        // 実際にアクセスされたスキーム(http/https)のままURLが作られる。
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
