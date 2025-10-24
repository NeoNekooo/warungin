<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class EnsureUserIsKasir
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sedang login DAN role-nya adalah 'kasir'
        if (Auth::check() && Auth::user()->role === 'kasir') {
            return $next($request); // Jika cocok, izinkan akses
        }

        // 2. Jika tidak cocok, tolak akses
        Auth::logout(); // Log out sesi yang tidak sah
        
        // Arahkan kembali ke form login dengan pesan error
        return redirect()->route('kasir.login.form')->withErrors([
            'email' => 'Akses tidak sah. Anda harus login sebagai Kasir.',
        ]);
    }
}