<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsKasir
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'Kasir') {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('kasir.login.form')->withErrors([
            'email' => 'Akses tidak sah. Anda harus login sebagai Kasir.',
        ]);
    }
}