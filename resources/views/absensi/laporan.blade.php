@extends('layouts.app')

@section('title', 'Laporan Absensi Karyawan')

@section('content')
<div class="space-y-8 relative min-h-screen pb-10">

    {{-- Notifikasi Toast --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Halaman --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-calendar-check-line text-3xl text-indigo-500 mr-2"></i>
                Laporan Absensi
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-user-follow-fill mr-1.5 text-sm"></i> Monitoring Kehadiran
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Mencatat riwayat masuk, izin, dan sakit seluruh karyawan.</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            {{-- Tombol Atur Jadwal --}}
            <button onclick="openModal('modalJadwal')" class="flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition duration-200 shadow-lg shadow-amber-100">
                <i class="ri-time-line mr-2"></i> Atur Jadwal
            </button>

            {{-- Tombol Input Manual --}}
            <button onclick="openModal('modalAbsenManual')" class="flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition duration-200 shadow-lg shadow-indigo-100">
                <i class="ri-add-box-line mr-2"></i> Input Manual
            </button>

            {{-- Tombol Export --}}
            <a href="{{ route('absensi.export', request()->all()) }}" class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition duration-200 shadow-lg shadow-green-100 uppercase tracking-tighter">
                <i class="ri-file-excel-2-fill mr-2"></i> Export Excel
            </a>
        </div>
    </div>

    @if($errors->has('kunci'))
        <div class="w-full bg-red-50 text-red-700 font-bold px-4 py-3 rounded-lg shadow-md border border-red-200 flex items-center gap-2">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                {{ $errors->first('kunci') }}
            </div>
        </div>
    @endif

    {{-- Filter Section --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form id="filterForm" action="{{ route('absensi.laporan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">
                <i class="ri-calendar-line mr-1"></i> Dari Tanggal
            </label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition shadow-sm bg-gray-50 focus:bg-white">
        </div>

        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">
                <i class="ri-calendar-event-line mr-1"></i> Sampai Tanggal
            </label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition shadow-sm bg-gray-50 focus:bg-white">
        </div>

        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">
                <i class="ri-filter-2-line mr-1"></i> Status Kehadiran
            </label>
            <select name="status" id="status_filter" 
                class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition shadow-sm bg-gray-50 focus:bg-white">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
        </div>

        <div class="md:col-span-3 flex gap-2">
            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl transition duration-200 flex items-center justify-center shadow-md shadow-indigo-100">
                <i class="ri-search-line mr-2"></i> Cari
            </button>
            <a href="{{ route('absensi.laporan') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl transition duration-200 flex items-center justify-center" title="Reset Filter">
                <i class="ri-refresh-line"></i>
            </a>
        </div>
    </form>
</div>

    {{-- Tabel Daftar Absensi --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left">
            <thead>
                <tr class="bg-indigo-600 text-white font-bold">
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">No</th>
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider">Karyawan</th>
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider">Waktu Scan</th>
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider text-center">Status</th>
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider">Keterangan</th>
                    <th class="px-5 py-4 font-semibold text-xs uppercase tracking-wider rounded-tr-2xl">Petugas Scan</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100 font-medium">
                @forelse($absensis as $absen)
                <tr class="hover:bg-indigo-50/50 transition duration-150 ease-in-out">
                    <td class="px-5 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                    
                    {{-- Nama Karyawan --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3 shadow-sm">
                                {{ strtoupper(substr($absen->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $absen->user->nama }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $absen->user->role ?? 'Staff' }}</div>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Tanggal & Jam --}}
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-800 font-bold">
                                {{ \Carbon\Carbon::parse($absen->created_at)->isoFormat('D MMMM YYYY') }}
                            </span>
                            <span class="text-[11px] text-indigo-500 font-semibold flex items-center">
                                <i class="ri-time-line mr-1"></i> {{ \Carbon\Carbon::parse($absen->created_at)->format('H:i:s') }} WIB
                            </span>
                        </div>
                    </td>
                    
                    {{-- Status Badge --}}
                    <td class="px-5 py-4 text-center">
                        @if($absen->status === 'hadir')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold bg-green-100 text-green-700 ring-1 ring-green-400/30">
                                <i class="ri-checkbox-circle-fill mr-1 text-sm"></i> HADIR
                            </span>
                        @elseif($absen->status === 'terlambat')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold bg-orange-100 text-orange-700 ring-1 ring-orange-400/30">
                                <i class="ri-alarm-warning-fill mr-1 text-sm"></i> TERLAMBAT
                            </span>
                        @elseif($absen->status === 'izin')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold bg-amber-100 text-amber-700 ring-1 ring-amber-400/30">
                                <i class="ri-information-fill mr-1 text-sm"></i> IZIN
                            </span>
                        @elseif($absen->status === 'sakit')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold bg-rose-100 text-rose-700 ring-1 ring-rose-400/30">
                                <i class="ri-heart-pulse-fill mr-1 text-sm"></i> SAKIT
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold bg-gray-100 text-gray-700 ring-1 ring-gray-400/30">
                                <i class="ri-close-circle-fill mr-1 text-sm"></i> ALPA
                            </span>
                        @endif
                    </td>
                    
                    {{-- Keterangan --}}
                    <td class="px-5 py-4">
                        <div class="text-sm text-gray-600 italic">
                            {{ $absen->keterangan ?? 'Masuk Tepat Waktu' }}
                        </div>
                    </td>
                    
                    {{-- Admin/Sistem --}}
                    <td class="px-5 py-4 text-sm text-gray-500">
                        <div class="flex items-center font-semibold">
                            <i class="ri-shield-user-fill text-gray-400 mr-2 text-lg"></i>
                            {{ $absen->admin->name ?? 'Sistem QR' }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-5 py-16 text-center text-gray-400 italic" colspan="6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-50 p-6 rounded-full mb-4 shadow-inner">
                                <i class="ri-calendar-event-line text-6xl text-gray-200"></i>
                            </div>
                            <p class="text-xl font-extrabold text-gray-600">Belum Ada Riwayat Absensi</p>
                            <p class="text-sm mt-1 text-gray-400">Data scan kehadiran akan muncul di sini setelah karyawan melakukan scan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modalJadwal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="backdropJadwal" onclick="closeModal('modalJadwal')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelJadwal" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 px-6 py-5 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white leading-tight">Pengaturan Jadwal</h3>
                            <p class="text-xs text-indigo-100 italic">Atur jam kerja & hari operasional</p>
                        </div>
                    </div>
                    <button onclick="closeModal('modalJadwal')" class="text-white/70 hover:text-white hover:bg-white/10 rounded-full p-2 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('absensi.jadwalStore') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        
                        <div>
                            <label class="flex items-center text-sm font-bold text-slate-700 mb-3">
                                <i class="fas fa-clock mr-2 text-indigo-500"></i> Jam Operasional Toko
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase ml-1">Jam Masuk</span>
                                    <input type="time" name="jam_masuk" 
                                           value="{{ $jadwal ? substr($jadwal->jam_masuk, 0, 5) : '08:00' }}" 
                                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-semibold text-slate-700">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase ml-1">Jam Pulang</span>
                                    <input type="time" name="jam_pulang" 
                                           value="{{ $jadwal ? substr($jadwal->jam_pulang, 0, 5) : '17:00' }}" 
                                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-semibold text-slate-700">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center text-sm font-bold text-slate-700 mb-3">
                                <i class="fas fa-calendar-check mr-2 text-indigo-500"></i> Hari Operasional
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @php $hariTerpilih = json_decode($jadwal->hari_kerja ?? '[]', true) ?: []; @endphp
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <label class="group relative flex items-center p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                                    <input type="checkbox" name="hari_kerja[]" value="{{ $hari }}" 
                                        {{ in_array($hari, $hariTerpilih) ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 rounded-lg border-slate-300 focus:ring-indigo-500">
                                    <span class="ml-3 text-xs font-bold text-slate-600 group-hover:text-indigo-700 transition-colors">{{ $hari }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-5 bg-rose-50 rounded-2xl border border-rose-100 space-y-3">
                            <label class="flex items-center text-sm font-bold text-rose-700">
                                <i class="fas fa-calendar-times mr-2"></i> Libur Khusus / Tanggal Merah
                            </label>
                            <textarea name="tanggal_libur" rows="2" 
                                      class="w-full px-4 py-3 rounded-xl border border-rose-200 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-400 text-sm outline-none placeholder:text-rose-300" 
                                      placeholder="Contoh: 2026-08-17, 2026-12-25">{{ $jadwal->tanggal_libur }}</textarea>
                            <div class="flex gap-2">
                                <i class="fas fa-info-circle text-rose-400 text-[10px] mt-0.5"></i>
                                <p class="text-[10px] text-rose-500 italic leading-relaxed">Pisahkan dengan koma. Format: YYYY-MM-DD. Scanner otomatis mati pada tanggal ini.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-[2] bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeModal('modalJadwal')" class="flex-1 bg-slate-100 text-slate-500 font-bold py-4 rounded-2xl hover:bg-slate-200 transition-all active:scale-[0.98]">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <div id="modalAbsenManual" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" id="backdropAbsen" onclick="closeModal('modalAbsenManual')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="panelAbsen" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="ri-user-add-fill mr-2 text-xl text-indigo-200"></i> Input Absensi Manual
                        </h3>
                        <button onclick="closeModal('modalAbsenManual')" class="text-indigo-100 hover:text-white rounded-full p-1 transition">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('absensi.storeManual') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 py-6 space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Karyawan</label>
                                <select name="user_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all" required>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->user_id }}">{{ $karyawan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="jam_manual" class="block text-gray-700 text-sm font-bold mb-2">Jam Kehadiran</label>
                            <input type="text" name="jam_manual" value="{{ date('H:i:s') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all" required placeholder="contoh: 08:30:00">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all" required>
                                        <option value="hadir">Hadir (Koreksi)</option>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="terlambat">Terlambat</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Alasan / Keterangan</label>
                                <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Contoh: Izin urusan keluarga..." required></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl hover:bg-indigo-700 font-bold shadow-md shadow-indigo-100 transition-all">
                                Simpan Absensi
                            </button>
                            <button type="button" onclick="closeModal('modalAbsenManual')" class="text-gray-600 font-semibold px-4">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if(isset($absensis) && method_exists($absensis, 'hasPages') && $absensis->hasPages())
    <div class="px-6 py-4 bg-white rounded-2xl border border-gray-100 flex justify-center md:justify-end shadow-sm">
        {{ $absensis->links() }} 
    </div>
    @endif
    
</div>

<script>
    function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    
    // Trigger animasi setelah hidden hilang
    setTimeout(() => {
        const backdrop = modal.querySelector('[id^="backdrop"]');
        const panel = modal.querySelector('[id^="panel"]');
        
        backdrop.classList.replace('opacity-0', 'opacity-100');
        panel.classList.replace('opacity-0', 'opacity-100');
        panel.classList.replace('scale-95', 'scale-100');
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const backdrop = modal.querySelector('[id^="backdrop"]');
    const panel = modal.querySelector('[id^="panel"]');
    
    backdrop.classList.replace('opacity-100', 'opacity-0');
    panel.classList.replace('opacity-100', 'opacity-0');
    panel.classList.replace('scale-100', 'scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
@endsection