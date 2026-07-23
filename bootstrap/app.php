<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Percaya header proxy Render (X-Forwarded-Proto: https) agar Laravel
        // tahu request datang lewat HTTPS dan tidak men-generate URL http://.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangani sesi kedaluwarsa (CSRF token mismatch / error 419) secara
        // ramah: alihkan user kembali ke halaman sebelumnya dengan pesan
        // bahasa awam, bukan halaman error "Page Expired".
        // Catatan: Laravel sudah mengubah TokenMismatchException menjadi
        // HttpException(419) sebelum callback ini, jadi kita cek status 419.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null; // biarkan Laravel menangani error lain seperti biasa
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Sesi Anda telah berakhir karena dibiarkan terlalu lama. Silakan coba lagi.');
        });
    })->create();
