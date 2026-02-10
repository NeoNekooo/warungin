@extends('layouts.guest')

@section('content')

<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
    <div class="absolute text-blue-100 animate-float" style="left: 10%; top: 20%; font-size: 4rem;"><i class="fas fa-money-bill-wave"></i></div>
    <div class="absolute text-blue-50/50 animate-float-slow" style="left: 80%; top: 10%; font-size: 5rem;"><i class="fas fa-shopping-basket"></i></div>
    <div class="absolute text-blue-100/60 animate-float-delayed" style="left: 15%; top: 70%; font-size: 3rem;"><i class="fas fa-box"></i></div>
    <div class="absolute text-blue-50 animate-float" style="left: 85%; top: 75%; font-size: 4.5rem;"><i class="fas fa-cash-register"></i></div>
    <div class="absolute text-blue-100/40 animate-float-slow" style="left: 50%; top: 5%; font-size: 3.5rem;"><i class="fas fa-calculator"></i></div>
    <div class="absolute text-blue-50/80 animate-float-delayed" style="left: 40%; top: 85%; font-size: 4rem;"><i class="fas fa-receipt"></i></div>
</div>

<style>
    /* Animasi Floating Ikon Background */
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); opacity: 0.4; }
        50% { transform: translateY(-20px) rotate(5deg); opacity: 0.8; }
        100% { transform: translateY(0px) rotate(0deg); opacity: 0.4; }
    }

    /* Animasi Tangan Melambai */
    @keyframes wave {
        0% { transform: rotate( 0.0deg) }
       10% { transform: rotate(14.0deg) }
       20% { transform: rotate(-8.0deg) }
       30% { transform: rotate(14.0deg) }
       40% { transform: rotate(-4.0deg) }
       50% { transform: rotate(10.0deg) }
       60% { transform: rotate( 0.0deg) }
      100% { transform: rotate( 0.0deg) }
    }

    .animate-wave {
        animation: wave 2.5s infinite;
        transform-origin: 70% 70%;
        display: inline-block;
    }

    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-slow { animation: float 9s ease-in-out infinite; }
    .animate-float-delayed { animation: float 7s ease-in-out infinite; animation-delay: 2s; }
</style>

<div class="relative z-10 bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-gray-100">

    <div class="hidden md:flex w-1/2 bg-blue-600 text-white flex-col items-center justify-center p-10 relative overflow-hidden">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-500 rounded-full opacity-50"></div>
        <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-blue-700 rounded-full opacity-50"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <img src="/image/warungin_logo.png" class="h-28 w-28 rounded-full shadow-lg border-4 border-white mb-6 bg-white">
            <h2 class="text-3xl font-bold tracking-wide">WARUNGIN</h2>
            <div class="h-1 w-16 bg-blue-300 my-4 rounded-full"></div>
            <p class="mt-2 text-center text-blue-50 max-w-xs leading-relaxed font-light">
                Sistem kasir modern untuk usaha warung Indonesia — cepat, mudah dan efisien.
            </p>
        </div>
    </div>

    <div class="w-full md:w-1/2 p-10 bg-white">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-2 text-center">
            Selamat Datang <span class="animate-wave">👋</span>
        </h1>
        <p class="text-gray-500 text-center mb-8">Silahkan masuk ke akun Anda</p>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 text-sm flex items-center shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="font-semibold text-gray-700 text-sm ml-1">Email</label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white" 
                        placeholder="nama@email.com"
                        required
                    >
                </div>
            </div>

            <div>
                <label class="font-semibold text-gray-700 text-sm ml-1">Password</label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        id="password"
                        type="password" 
                        name="password"
                        class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white" 
                        placeholder="••••••••"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors focus:outline-none"
                    >
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between py-2">
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600 transition-colors">Ingat saya</span>
                </label>
            </div>

            <button 
                type="submit"
                class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 active:scale-[0.98] transition-all shadow-lg shadow-blue-200"
            >
                Masuk ke Dashboard
            </button>
        </form>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOpenPath = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />`;
        const eyeClosedPath = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = eyeClosedPath;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = eyeOpenPath;
        }
    }
</script>

@endsection