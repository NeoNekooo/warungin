@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-xl font-bold mb-2">Detail Pembayaran #{{ $pembayaran->pembayaran_id }}</h2>
        <p class="text-sm text-gray-500 mb-4">Rincian pembayaran terkait transaksi <strong>#{{ $pembayaran->transaksi_id }}</strong></p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600">Metode</div>
                <div class="font-medium">{{ $pembayaran->metode }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600">Jumlah</div>
                <div class="font-medium">{{ number_format($pembayaran->jumlah,0,',','.') }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600">Referensi</div>
                <div class="font-medium">{{ $pembayaran->referensi ?? '-' }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600">Tanggal</div>
                <div class="font-medium">{{ $pembayaran->created_at }}</div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('pembayaran.index') }}" class="text-indigo-600 hover:underline">Kembali ke daftar pembayaran</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openReconcile() {
        const modal = document.createElement('div');
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white rounded p-4 w-full max-w-md">
                    <h3 class="font-bold mb-2">Rekonsiliasi Pembayaran</h3>
                    <form method="POST" action="{{ route('pembayaran.reconcile', $pembayaran->pembayaran_id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="block text-sm">Jumlah</label>
                            <input name="jumlah" type="number" step="0.01" class="w-full border rounded px-2 py-1" value="{{ $pembayaran->jumlah }}">
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm">Referensi</label>
                            <input name="referensi" class="w-full border rounded px-2 py-1" value="{{ $pembayaran->referensi }}">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="modal.remove()" class="px-3 py-1 border rounded">Batal</button>
                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>`;
        document.body.appendChild(modal);
    }
</script>
@endsection
