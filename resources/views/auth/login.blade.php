@extends('layouts.guest')

@section('content')

<div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

    <!-- KIRI -->
    <div class="hidden md:flex w-1/2 bg-blue-600 text-white flex-col items-center justify-center p-10">
        <img src="/image/warungin_logo.png" class="h-28 w-28 rounded-full shadow-lg border-4 border-white mb-6">
        
        <h2 class="text-3xl font-bold tracking-wide">WARUNGIN</h2>
        <p class="mt-3 text-center text-blue-100 max-w-xs leading-relaxed">
            Sistem kasir modern untuk usaha warung Indonesia — cepat, mudah dan efisien.
        </p>
    </div>

    <!-- KANAN -->
    <div class="w-full md:w-1/2 p-10 bg-white">

        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">
            Login
        </h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label class="font-semibold text-gray-700 text-sm">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                    required
                >
            </div>

            <div>
                <label class="font-semibold text-gray-700 text-sm">Password</label>
                <input 
                    type="password" 
                    name="password"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                    required
                >
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Remember me</span>
                </label>

                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    Forgot Password?
                </a>
            </div>

            <button 
                type="submit"
                class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition"
            >
                Log In
            </button>

        </form>

    </div>
</div>

@endsection
