<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir - WARUNGIN</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> {{-- Fallback Tailwind CDN --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Pastikan ini memuat Tailwind dari proyek Anda --}}
    <style>
        /* Optional: Custom styling if needed beyond Tailwind */
        .wave-background {
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23e0e7ff" fill-opacity="1" d="M0,224L48,208C96,192,192,160,288,160C384,160,480,192,576,218.7C672,245,768,267,864,266.7C960,267,1056,245,1152,213.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
        }
    </style>
</head>
<body class="bg-grey-800 flex items-center justify-center min-h-screen p-4">

<div class="flex bg-white rounded-lg shadow-xl overflow-hidden max-w-4xl w-full">
    <div class="w-1/2 bg-gradient-to-b from-[#0A489B] to-[#1E88EB] text-white p-8 flex flex-col justify-center items-center text-center">
    <h1 class="text-3xl font-bold mb-4">Selamat Datang</h1>
        <img src="{{ asset('image/warungin_logo.png') }}" 
     alt="Logo Warungin" 
     class="mb-4 h-24 w-24 object-contain">
        <h3 class="text-2xl font-semibold mb-2">WARUNGIN</h3>
        <p class="text-sm leading-relaxed max-w-xs">
            Warungin menghidupkan kembali semangat warung dalam bentuk modern —
            menggabungkan nilai lokal dengan teknologi untuk memajukan usaha kecil di Indonesia.
        </p>
    </div>

    <div class="w-1/2 p-8 bg-blue-50 wave-background flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline"> {{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('kasir.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Gmail/Username</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                    Ingat Saya
                </label>
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Log In
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Belum punya akun Kasir?</p>
            <a href="{{ route('kasir.register.form') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>

</body>
</html>