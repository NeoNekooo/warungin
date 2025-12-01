<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // If the CSRF token is missing or expired, redirect back with a helpful message
        if ($e instanceof TokenMismatchException) {
            // Redirect to login (or previous) with a message so users don't see the 419 page
            return redirect()->guest(route('login'))
                ->withErrors(['message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.']);
        }

        return parent::render($request, $e);
    }
}
