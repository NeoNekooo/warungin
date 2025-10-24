<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        // bootstrap/app.php

// ...
        $middleware->alias([
            // JANGAN HAPUS ALIAS LAIN YANG SUDAH ADA DI SINI

            // TAMBAHKAN ALIAS 'kasir' DI BAWAH INI
            'kasir' => EnsureUserIsKasir::class, // <-- Tambahkan baris ini
        ]);
        
        // ... kode middleware lainnya ...
    })
// ...
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
