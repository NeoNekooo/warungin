@extends('layouts.app')

@section('title', 'My QR')

@section('content')
<style>
    /* CSS hanya akan berdampak pada SVG yang ada di dalam .qr-wrapper */
    .qr-wrapper svg {
        width: 100% !important;
        height: auto !important;
        display: block;
    }
</style>

<script>
    // Auto refresh halaman setiap 30 detik agar QR diperbarui
    setTimeout(function(){
       location.reload();
    }, 30000);
</script>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm">
        
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="pt-10 pb-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 mb-4">
                    <i class="ri-qr-code-line text-3xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">QR Presensi</h2>
                <p class="text-sm text-gray-400 mt-1">Tunjukkan kode ini ke petugas</p>
            </div>

            <div class="px-10 pb-10 flex flex-col items-center">
                <div class="relative p-2 bg-white border border-gray-100 rounded-3xl shadow-sm mb-8">
                    <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-indigo-500 rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-indigo-500 rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-indigo-500 rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-indigo-500 rounded-br-lg"></div>
                    
                    @php
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::user()->user_id;
    // Rounding 30 detik untuk stabilitas
    $secondsToRound = 30;
    $timestamp = floor(time() / $secondsToRound) * $secondsToRound;

    // Buat Signature sederhana agar data tidak bisa ditembak manual
    // Gunakan APP_KEY sebagai salt agar orang luar tidak bisa meniru hash-nya
    $rawPayload = $userId . '|' . $timestamp;
    $signature = hash_hmac('sha256', $rawPayload, config('app.key'));
    
    // Ambil 8 karakter pertama dari hash saja agar data tetap pendek
    $shortSignature = substr($signature, 0, 8);
    
    // Hasil akhirnya jauh lebih pendek dibanding Crypt::encryptString
    $qrData = $rawPayload . '|' . $shortSignature;
@endphp

<div class="qr-wrapper w-48 h-48 sm:w-56 sm:h-56 flex items-center justify-center p-2">
    {!! QrCode::size(300)
        ->color(0, 0, 0) // Gunakan warna hitam pekat agar kontras tinggi (Sangat membantu kamera kentang)
        ->margin(1)
        ->errorCorrection('M') // Level M (Medium) sudah cukup dan membuat QR lebih renggang dibanding H
        ->generate($qrData) 
    !!}
</div>
                </div>

                <div class="w-full pt-6 border-t border-gray-50 text-center">
                    <div class="flex items-center justify-center gap-3 mb-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-sm font-semibold text-gray-700">{{ Auth::user()->nama }}</span>
                    </div>
                    <p class="text-xs text-gray-400 font-medium tracking-widest uppercase">
                        ID: {{ Auth::user()->user_id }} • {{ Auth::user()->role ?? 'Staff' }}
                    </p>
                </div>
            </div>
        </div>

        <p class="text-center mt-8 text-xs text-gray-400 tracking-wide">
            Kode diperbarui secara otomatis
        </p>
    </div>
</div>
@endsection