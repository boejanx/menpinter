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
        $middleware->append(\App\Http\Middleware\MinifyHtml::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // tambahkan alias middleware lain di sini jika perlu
        ]);

        // Anda juga bisa mendaftarkan middleware global atau grup di sini
        // $middleware->web(append: [ ... ]);
        // $middleware->api(prepend: [ ... ]);
        // $middleware->group('nama_grup', [ ... ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
