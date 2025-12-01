@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold">Buat Transaksi Baru</h2>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded p-6">
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pelanggan (opsional)</label>
                    <select name="pelanggan_id" class="mt-1 block w-full border rounded p-2">
                        <option value="">Umum</option>
                        @foreach($pelanggan as $p)
                            <option value="{{ $p->pelanggan_id }}" {{ old('pelanggan_id') == $p->pelanggan_id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="number" step="0.01" name="total" value="{{ old('total') }}" class="mt-1 block w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Diskon</label>
                    <input type="number" step="0.01" name="diskon" value="{{ old('diskon', 0) }}" class="mt-1 block w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pajak</label>
                    <input type="number" step="0.01" name="pajak" value="{{ old('pajak', 0) }}" class="mt-1 block w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Metode Bayar</label>
                    <select name="metode_bayar" class="mt-1 block w-full border rounded p-2" required>
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS   </option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nominal Bayar</label>
                    <input type="number" step="0.01" name="nominal_bayar" value="{{ old('nominal_bayar', 0) }}" class="mt-1 block w-full border rounded p-2">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('transaksi.index') }}" class="px-4 py-2 border rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
