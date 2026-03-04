@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .content-wrapper {
        min-height: calc(100vh - 80px);
        padding: 40px 20px;
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 30px;
        max-width: 1150px;
        margin: 0 auto;
    }

    .scanner-card {
        background: #ffffff;
        border-radius: 32px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
    }

    .absensi-header {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        padding: 25px;
        text-align: center;
        color: white;
    }

    .scanner-box-wrapper {
        width: 100%;
        max-width: 320px;
        margin: 0 auto;
    }

    .scanner-container {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #f1f5f9;
        border-radius: 24px;
        overflow: hidden;
        border: 2px dashed #e2e8f0;
    }

    /* Overlay Libur */
    .libur-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        z-index: 50;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        text-align: center;
    }

    .jadwal-info {
        background: #f5f3ff; 
        border-radius: 16px;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd6fe;
    }

    .list-card {
        background: white;
        border-radius: 32px;
        padding: 25px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        max-height: 700px;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 15px;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 40px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
    }

    .user-list-container {
        overflow-y: auto;
        padding-right: 5px;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #f8fafc;
    }

    .status-badge {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: auto;
    }

    .badge-hadir { background: #dcfce7; color: #166534; }
    .badge-terlambat { background: #fef9c3; color: #854d0e; }
    .badge-belum { background: #f1f5f9; color: #64748b; }

    .laser {
        position: absolute;
        width: 100%;
        height: 3px;
        background: #ef4444;
        box-shadow: 0 0 15px #ef4444;
        z-index: 10;
        top: 0;
        display: none;
        animation: scanning 2s infinite linear;
    }
    @keyframes scanning { 0% { top: 0; } 100% { top: 100%; } }

    @media (max-width: 768px) {
        .main-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="content-wrapper">
    <div class="main-grid">
        
        <div class="scanner-card">
            {{-- Tampilan Jika Libur --}}
            @if($isLibur)
            <div class="libur-overlay">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-4 border-2 border-rose-200">
                    <i class="fas fa-store-slash text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">Toko Sedang Libur</h3>
                <p class="text-gray-500 text-sm mt-1">Scanner dinonaktifkan sementara.</p>
            </div>
            @endif

            <div class="absensi-header">
                <h3 class="text-xl font-bold">WARUNGIN PRESENSI</h3>
                <p class="text-xs opacity-80">Gunakan QR Code Karyawan Anda</p>
            </div>
            
            <div class="p-6">
                {{-- Info Jadwal --}}
                <div class="jadwal-info flex items-center justify-around text-center">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">Jadwal Masuk</p>
                        <p class="text-sm font-bold text-indigo-700">
                            <i class="far fa-clock mr-1"></i> 
                            {{ $jadwal ? substr($jadwal->jam_masuk, 0, 5) : '--:--' }}
                        </p>
                    </div>
                    <div class="h-8 w-[1px] bg-indigo-200"></div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">Jadwal Pulang</p>
                        <p class="text-sm font-bold text-indigo-700">
                            <i class="fas fa-sign-out-alt mr-1"></i> 
                            {{ $jadwal ? substr($jadwal->jam_pulang, 0, 5) : '--:--' }}
                        </p>
                    </div>
                </div>

                <div class="scanner-box-wrapper">
                    <div id="scanner-box" class="scanner-container">
                        <div id="laser-line" class="laser"></div>
                        <div id="placeholder" class="flex flex-col items-center justify-center h-full text-slate-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-camera text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest">Kamera Off</span>
                        </div>
                        <div id="reader"></div>
                    </div>
                </div>

                <div class="mt-6">
                    <button id="btn-start" {{ $isLibur ? 'disabled' : '' }} class="w-full py-4 {{ $isLibur ? 'bg-gray-300' : 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200' }} text-white rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg transition-all">
                        <i class="fas fa-power-off"></i> {{ $isLibur ? 'Scanner Terkunci' : 'Aktifkan Scanner' }}
                    </button>
                    <button id="btn-stop" class="hidden w-full py-4 bg-white text-rose-600 border-2 border-rose-100 rounded-2xl font-bold flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-stop-circle"></i> Matikan
                    </button>
                </div>
            </div>
        </div>

        <div class="list-card">
            <div class="search-wrapper">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="userSearch" class="search-input" placeholder="Cari nama kasir...">
            </div>

            <div class="user-list-container" id="user-list">
                @foreach($daftar_absensi as $user)
                    @php 
                        $absen = $user->absensis->first(); 
                    @endphp
                    
                    <div class="user-item" data-nama="{{ strtolower($user->nama) }}">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 mr-3 border border-indigo-100">
                            <i class="fas fa-user-circle text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-700 m-0">{{ $user->nama }}</p>
                            <p class="text-[10px] text-gray-400 m-0">
                                Masuk: {{ $absen ? substr($absen->waktu_scan, 11, 5) : '--' }} | 
                                Pulang: {{ ($absen && $absen->jam_pulang) ? substr($absen->jam_pulang, 0, 5) : '--' }}
                            </p>
                        </div>
                        
                        @if($absen && $absen->jam_pulang)
                            <span class="status-badge bg-blue-50 text-blue-600">Selesai</span>
                        @elseif($absen)
                            <span class="status-badge {{ $absen->status == 'terlambat' ? 'badge-terlambat' : 'badge-hadir' }}">
                                {{ ucfirst($absen->status) }}
                            </span>
                        @else
                            <span class="status-badge badge-belum">Belum</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal Custom Sukses/Gagal --}}
<div id="custom-modal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 text-center transform transition-all">
            <div id="modal-icon-container" class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 id="modal-message" class="text-xl font-bold text-gray-800 mb-2"></h3>
            <p id="modal-user" class="text-sm text-gray-400 mb-8 font-medium"></p>
            <button onclick="closeCustomModal()" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100">Selesai</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5Qr = null;
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');
    const laser = document.getElementById('laser-line');
    const readerDiv = document.getElementById('reader');
    const placeholder = document.getElementById('placeholder');

    // Search Filter
    document.getElementById('userSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const nama = item.getAttribute('data-nama');
            item.style.display = nama.includes(searchTerm) ? 'flex' : 'none';
        });
    });

    btnStart.addEventListener('click', async () => {
        placeholder.classList.add('hidden');
        readerDiv.style.display = 'block';
        laser.style.display = 'block';
        btnStart.classList.add('hidden');
        btnStop.classList.remove('hidden');

        if (!html5Qr) html5Qr = new Html5Qrcode("reader");
        
        try {
            await html5Qr.start(
                { facingMode: "user" }, 
                { 
                    fps: 30,             // Menaikkan FPS agar lebih responsif
                    qrbox: 250,          // Ukuran kotak scan
                    aspectRatio: 1.0,
                    // Taruh di sini untuk memperbaiki layar hijau DroidCam:
                    videoConstraints: {
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                }, 
                onScanSuccess
            );
        } catch (err) {
            console.error(err);
            showModal("Gagal akses kamera. Pastikan DroidCam sudah 'Start' di PC & HP", '', 'error');
            resetUI();
        }
    });

    btnStop.addEventListener('click', () => { 
        if (html5Qr) {
            html5Qr.stop().then(resetUI).catch(err => console.error(err));
        }
    });

    function resetUI() {
        placeholder.classList.remove('hidden');
        readerDiv.style.display = 'none';
        laser.style.display = 'none';
        btnStart.classList.remove('hidden');
        btnStop.classList.add('hidden');
    }

    // 1. Pastikan fungsi playVoice ada di paling atas agar bisa dipanggil
function playVoice(message) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(message);
        utterance.lang = 'id-ID'; 
        utterance.rate = 1.0;     
        utterance.pitch = 1.0;    
        window.speechSynthesis.speak(utterance);
    }
}

function onScanSuccess(decodedText) {
    html5Qr.pause(true); // Stop scanner sementara
    
    fetch("{{ route('absensi.prosesScan') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
        },
        body: JSON.stringify({ qr_data: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        // Di dalam fetch().then(data => { ... })

if (data.status === 'success') {
    playBeep('success'); // Bunyi 'Ting!' dulu
    playVoice("Absensi Berhasil. Selamat bekerja, " + data.nama); // Baru ngomong
    
    showModal(data.pesan, data.nama, data.status_absen);
    updateAttendanceList(data); 
} else {
    playBeep('error'); // Bunyi 'Boop' rendah
    playVoice("Absensi Gagal. " +  (data.message || data.pesan));
    
    showModal(data.message || data.pesan, data.nama || '', 'error');
}
    })
    .catch(err => {
        console.error(err);
        playVoice("Terjadi kesalahan koneksi sistem");
        showModal('Gagal memproses scan. Coba lagi.', '', 'error');
    });
}

// Fungsi baru untuk memperbarui baris list tanpa refresh
function updateAttendanceList(data) {
    // Cari elemen user berdasarkan nama (atribut data-nama yang sudah ada di HTML Anda)
    const userNameKey = data.nama.toLowerCase();
    const userRow = document.querySelector(`.user-item[data-nama="${userNameKey}"]`);
    
    if (userRow) {
        // Update jam masuk/pulang
        const infoText = userRow.querySelector('.flex-1 p:last-child');
        const jamMasuk = data.waktu_masuk ? data.waktu_masuk.substring(0, 5) : '--:--';
        const jamPulang = data.waktu_pulang ? data.waktu_pulang.substring(0, 5) : '--:--';
        
        infoText.innerHTML = `Masuk: ${jamMasuk} | Pulang: ${jamPulang}`;

        // Update Badge Status
        const statusBadge = userRow.querySelector('.status-badge');
        statusBadge.className = 'status-badge'; // reset class

        if (data.waktu_pulang) {
            statusBadge.classList.add('bg-blue-50', 'text-blue-600');
            statusBadge.innerText = 'Selesai';
        } else {
            const badgeClass = data.status_absen === 'terlambat' ? 'badge-terlambat' : 'badge-hadir';
            statusBadge.classList.add(badgeClass);
            statusBadge.innerText = data.status_absen.charAt(0).toUpperCase() + data.status_absen.slice(1);
        }
    }
}

    function showModal(msg, user, status) {
        const iconContainer = document.getElementById('modal-icon-container');
        document.getElementById('modal-message').innerText = msg;
        document.getElementById('modal-user').innerText = user;
        
        if (status === 'terlambat') {
            iconContainer.className = "w-24 h-24 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl";
            iconContainer.innerHTML = '<i class="fas fa-clock"></i>';
        } else if (status === 'error' || status === 'failed') {
            iconContainer.className = "w-24 h-24 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl";
            iconContainer.innerHTML = '<i class="fas fa-times-circle"></i>';
        } else {
            // default success
            iconContainer.className = "w-24 h-24 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl";
            iconContainer.innerHTML = '<i class="fas fa-check-circle"></i>';
        }
        document.getElementById('custom-modal').classList.remove('hidden');
    }

    function closeCustomModal() {
        document.getElementById('custom-modal').classList.add('hidden');
        
        // Melanjutkan scanner tanpa refresh halaman
        if (html5Qr) {
            html5Qr.resume();
        }
    }

    function playVoice(message) {
    // Cek apakah browser mendukung SpeechSynthesis
    if ('speechSynthesis' in window) {
        // Batalkan suara yang sedang berjalan agar tidak tumpang tindih
        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(message);
        utterance.lang = 'id-ID'; // Bahasa Indonesia
        utterance.rate = 1.2;     // Kecepatan (0.1 - 10)
        utterance.pitch = 1.0;    // Nada (0 - 2)
        
        window.speechSynthesis.speak(utterance);
    }
}

$.ajax({
    url: "{{ route('absensi.prosesScan') }}",
    type: 'POST',
    data: { /* data kamu */ },
    success: function(response) {
        if (response.status === 'success') {
            // Suara: Berhasil. Selamat bekerja, [Nama]!
            playVoice("Berhasil. Selamat bekerja, " + response.nama);
            
            // ... panggil fungsi update table & modal kamu ...
        } else {
            // Suara: Gagal. [Alasan Gagal]
            playVoice("Gagal. " + response.message);
            
            // ... panggil modal error kamu ...
        }
    },
    error: function() {
        playVoice("Terjadi kesalahan koneksi sistem");
    }
});

function playBeep(type = 'success') {
    const context = new (window.AudioContext || window.webkitAudioContext)();
    
    if (type === 'success') {
        // SUARA BERHASIL: Dua nada pendek (Ting-Ting!)
        const playTone = (freq, start, duration) => {
            const osc = context.createOscillator();
            const gain = context.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            osc.connect(gain);
            gain.connect(context.destination);
            gain.gain.setValueAtTime(0, start);
            gain.gain.linearRampToValueAtTime(0.1, start + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
            osc.start(start);
            osc.stop(start + duration);
        };

        playTone(880, context.currentTime, 0.1);      // Nada pertama
        playTone(1100, context.currentTime + 0.1, 0.1); // Nada kedua lebih tinggi
        
    } else {
        // SUARA GAGAL: Nada rendah bergetar (Buzzer Error)
        const osc = context.createOscillator();
        const gain = context.createGain();
        
        osc.type = 'sawtooth'; // Pake sawtooth biar suaranya agak "kasar" kayak buzzer
        osc.frequency.value = 110; // Nada rendah banget
        
        osc.connect(gain);
        gain.connect(context.destination);
        
        gain.gain.setValueAtTime(0, context.currentTime);
        gain.gain.linearRampToValueAtTime(0.2, context.currentTime + 0.05);
        gain.gain.linearRampToValueAtTime(0, context.currentTime + 0.5); // Bunyi agak lama
        
        osc.start(context.currentTime);
        osc.stop(context.currentTime + 0.5);
    }
}
</script>
@endsection