<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Background putih polos */
        body {
            background-color: #ffffff;
        }

        /* Container untuk ikon berterbangan */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none; /* Agar tidak mengganggu klik pada form */
        }

        /* Styling dasar ikon */
        .bg-animation i {
            position: absolute;
            display: block;
            list-style: none;
            color: rgba(59, 130, 246, 0.15); /* Warna biru muda transparan */
            font-size: 2rem;
            bottom: -150px;
            animation: animate 25s linear infinite;
        }

        /* Keyframes untuk animasi terbang ke atas */
        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                transform: translateY(-1200px) rotate(720deg);
                opacity: 0;
            }
        }

        /* Variasi posisi dan durasi ikon (agar acak) */
        .bg-animation i:nth-child(1) { left: 10%; animation-delay: 0s; }
        .bg-animation i:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 12s; font-size: 3rem; }
        .bg-animation i:nth-child(3) { left: 35%; animation-delay: 4s; }
        .bg-animation i:nth-child(4) { left: 50%; animation-delay: 0s; animation-duration: 18s; }
        .bg-animation i:nth-child(5) { left: 65%; animation-delay: 0s; }
        .bg-animation i:nth-child(6) { left: 80%; animation-delay: 3s; animation-duration: 22s; font-size: 2.5rem; }
        .bg-animation i:nth-child(7) { left: 90%; animation-delay: 7s; }
        .bg-animation i:nth-child(8) { left: 45%; animation-delay: 10s; animation-duration: 15s; }
        .bg-animation i:nth-child(9) { left: 15%; animation-delay: 12s; }
        .bg-animation i:nth-child(10) { left: 75%; animation-delay: 5s; animation-duration: 20s; }
    </style>
</head>

<body class="font-sans antialiased relative">
    
    <div class="bg-animation">
        <i class="fas fa-money-bill-wave"></i>
        <i class="fas fa-shopping-cart"></i>
        <i class="fas fa-box"></i>
        <i class="fas fa-calculator"></i>
        <i class="fas fa-store"></i>
        <i class="fas fa-coins"></i>
        <i class="fas fa-receipt"></i>
        <i class="fas fa-wallet"></i>
        <i class="fas fa-tags"></i>
        <i class="fas fa-cash-register"></i>
    </div>

    <div class="min-h-screen flex items-center justify-center relative z-10 px-4">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>