<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KasirAuthController extends Controller
{
    // METHOD UNTUK MENAMPILKAN FORM LOGIN
    public function loginForm()
    {
        return view('kasir.login');
    }

    // ----------------------------------------------------
    // LOGIKA REGISTER
    // ----------------------------------------------------
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'Kasir',
            'password' => Hash::make($request->password),
        ]);

        // 3. Otentikasi dan Arahkan ke Dashboard
        Auth::login($user);

        return redirect()->intended(route('kasir.dashboard'));
    }

    // ----------------------------------------------------
    // LOGIKA LOGIN
    // ----------------------------------------------------
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Otentikasi
        if (Auth::attempt($credentials)) {
            
            // 3. Pengecekan Role
            $user = Auth::user();

            if ($user->role !== 'Kasir') { // Pastikan pengecekan huruf kecil
                // Jika role BUKAN 'kasir', batalkan login
                Auth::logout(); 
                
                return back()->withErrors([
                    'email' => 'Akses ditolak. Akun ini tidak terdaftar sebagai Kasir.',
                ])->onlyInput('email');
            }

            // 4. Jika role adalah 'kasir', lanjutkan
            $request->session()->regenerate();
            
            return redirect()->intended(route('kasir.dashboard')); 
        }

        // 5. Gagal Login (Password/Email salah)
        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // ----------------------------------------------------
    // LOGIKA LOGOUT
    // ----------------------------------------------------
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); 
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan kembali ke form login kasir
        return redirect()->route('kasir.login.form'); 
    }
}