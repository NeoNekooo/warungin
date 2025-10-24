<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Kasir - WARUNGIN</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> {{-- Fallback Tailwind CDN --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Pastikan ini memuat Tailwind dari proyek Anda --}}
    <style>
        .wave-background {
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23e0e7ff" fill-opacity="1" d="M0,224L48,208C96,192,192,160,288,160C384,160,480,192,576,218.7C672,245,768,267,864,266.7C960,267,1056,245,1152,213.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
        }
    </style>
</head>
<body class="bg-[#1E88EB]-800 flex items-center justify-center min-h-screen p-4">

<div class="flex bg-white rounded-lg shadow-xl overflow-hidden max-w-4xl w-full">
   <div class="w-1/2 bg-gradient-to-b from-[#1E88EB] to-[#0A489B] text-white p-8 flex flex-col justify-center items-center text-center">
    
    <h1 class="text-3xl font-bold mb-4">Selamat Datang</h1>
    
    <img src="{{ asset('image/warungin_logo.png') }}" 
         alt="Logo Warungin" 
         class="mb-4 rounded-full border border-yellow-400 h-24 w-24 object-contain">
    
    <h3 class="text-2xl font-semibold mb-2">WARUNGIN</h3>
    <p class="text-sm leading-relaxed max-w-xs">
        Daftarkan diri Anda sebagai kasir Warungin dan bantu majukan usaha kecil di Indonesia dengan teknologi modern.
    </p>
    
</div>

    <div class="w-1/2 p-8 bg-blue-50 wave-background flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Daftar Kasir</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('kasir.register.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email (untuk Login)</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                DAFTAR
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Sudah punya akun Kasir?</p>
            <a href="{{ route('kasir.login.form') }}" 
               class="text-green-600 hover:text-green-800 font-medium">
                Login di sini
            </a>
        </div>
    </div>
</div>

</body>
</html>