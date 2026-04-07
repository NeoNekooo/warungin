@extends('layouts.app')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="space-y-8 relative min-h-screen">

    {{-- Notifikasi Toast (jika ada) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (Diubah agar konsisten) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-wallet-3-fill text-3xl text-indigo-500 mr-2"></i>
                Daftar Pembayaran
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-bank-card-line mr-1.5 text-sm"></i> Semua Metode Pembayaran
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Semua pembayaran *split* atau *gateway* yang tercatat.</p>
            </div>
        </div>

        {{-- Tempat untuk tombol Aksi (misalnya: Filter/Export) --}}
        <div class="w-full md:w-auto flex justify-end gap-3">
            @if(request()->anyFilled(['metode', 'date_start', 'date_end', 'min_amount', 'max_amount', 'search']))
                <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center px-4 py-3 bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition shadow-sm font-semibold text-sm">
                    <i class="ri-refresh-line mr-2"></i> Reset
                </a>
            @endif
            <button onclick="openModal('modalFilter')" class="group bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-blue-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-filter-3-line mr-2 text-xl group-hover:rotate-12 transition-transform"></i> Filter Pembayaran
            </button>
        </div>
    </div>

    {{-- Konten Utama: Tabel Daftar Pembayaran --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left text-sm">
            <thead>
                {{-- Header Tabel dengan warna Indigo --}}
                <tr class="bg-indigo-600 text-white">
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">ID Bayar</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">ID Transaksi</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Metode Pembayaran</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah Dibayar</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Referensi / Bank</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Tanggal Transaksi</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
                @forelse($pembayarans ?? [] as $pay)
                <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                    {{-- ID Pembayaran --}}
                    <td class="px-5 py-3.5 text-sm text-gray-500 font-mono">#{{ $pay->pembayaran_id }}</td>
                    
                    {{-- Transaksi ID --}}
                    <td class="px-5 py-3.5 text-center font-mono font-medium text-gray-700">
                        {{ $pay->transaksi_id }}
                    </td>
                    
                    {{-- Metode --}}
                    <td class="px-5 py-3.5">
                        @php
                            $metode = strtolower($pay->metode);
                            $class = '';
                            $icon = '';
                            if ($metode === 'tunai') {
                                $class = 'bg-green-100 text-green-700 border-green-300';
                                $icon = 'ri-money-line';
                            } elseif ($metode === 'qris') {
                                $class = 'bg-blue-100 text-blue-700 border-blue-300';
                                $icon = 'ri-qr-code-line';
                            } else {
                                $class = 'bg-purple-100 text-purple-700 border-purple-300';
                                $icon = 'ri-bank-card-line';
                            }
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $class }} whitespace-nowrap">
                            <i class="{{ $icon }} mr-1"></i> {{ ucfirst($pay->metode) }}
                        </span>
                    </td>

                    {{-- Jumlah --}}
                    <td class="px-5 py-3.5 text-right font-extrabold text-indigo-700 text-base font-mono">
                        Rp {{ number_format($pay->jumlah,0,',','.') }}
                    </td>

                    {{-- Referensi --}}
                    <td class="px-5 py-3.5 text-gray-600 font-mono text-xs">
                        <span class="inline-block max-w-[150px] truncate" title="{{ $pay->referensi ?? 'Tidak ada referensi' }}">
                            {{ $pay->referensi ?? '-' }}
                        </span>
                    </td>
                    
                    {{-- Tanggal --}}
                    <td class="px-5 py-3.5 text-gray-600 text-center text-xs font-medium whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($pay->created_at)->isoFormat('D MMM YYYY') }}<br>
                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($pay->created_at)->isoFormat('HH:mm:ss') }}</span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3.5 text-center">
                        <a href="{{ route('pembayaran.show', $pay->pembayaran_id) }}"
                           class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl hover:bg-indigo-200 transition duration-150 shadow-sm">
                            <i class="ri-eye-line mr-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 italic bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-5 rounded-full mb-3">
                                <i class="ri-money-dollar-circle-line text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-600">Tidak Ada Data Pembayaran</p>
                            <p class="text-sm mt-1 text-gray-500">Belum ada transaksi pembayaran yang tercatat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{ $pembayarans->links() }}
    </div>

</div>

{{-- MODAL FILTER PEMBAYARAN --}}
<div id="modalFilter" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropFilter" onclick="closeModal('modalFilter')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelFilter" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-filter-3-fill mr-2 text-xl text-blue-200"></i> Filter Pembayaran
                    </h3>
                    <button onclick="closeModal('modalFilter')" class="text-blue-200 hover:text-white hover:bg-blue-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('pembayaran.index') }}" method="GET">
                    <div class="bg-white px-6 py-6 space-y-5">
                        {{-- Search ID / Ref --}}
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Cari ID / Referensi</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="ID Transaksi, Bank, atau Ref...">
                        </div>

                        {{-- Metode --}}
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                            <select name="metode" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Metode</option>
                                <option value="Tunai" {{ request('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="Qris" {{ request('metode') == 'Qris' ? 'selected' : '' }}>Qris</option>
                                <option value="Transfer" {{ request('metode') == 'Transfer' ? 'selected' : '' }}>Transfer / Bank</option>
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Dari Tanggal</label>
                                <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Sampai Tanggal</label>
                                <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        {{-- Range Nominal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Min. Jumlah</label>
                                <input type="number" name="min_amount" value="{{ request('min_amount') }}" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Max. Jumlah</label>
                                <input type="number" name="max_amount" value="{{ request('max_amount') }}" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="999jt...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md">
                            <i class="ri-search-eye-line mr-2"></i> Terapkan Filter
                        </button>
                        <button type="button" onclick="closeModal('modalFilter')" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --- FUNGSI MODAL ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');
        
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            if(backdrop) backdrop.classList.remove('opacity-0');
            if(panel) {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }
        });
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');

        if(backdrop) backdrop.classList.add('opacity-0');
        if(panel) {
            panel.classList.remove('opacity-100', 'scale-100');
            panel.classList.add('opacity-0', 'scale-95');
        }

        setTimeout(() => {
            if(modal) modal.classList.add('hidden');
        }, 300);
    }

    // Tampilkan Toast jika ada session sukses
    @if(session('success'))
        // logic toast jika diperlukan, tapi Pembayaran list biasanya readonly
    @endif
</script>
@endsection