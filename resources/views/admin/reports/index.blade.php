@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <div class="text-sm text-gray-500">Total Transaksi</div>
            <div class="text-2xl font-bold mt-2">{{ number_format($totalTransactions,0,',','.') }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <div class="text-sm text-gray-500">Total Pendapatan</div>
            <div class="text-2xl font-bold mt-2">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm flex items-center justify-end">
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('reports.index') }}">
                    <select name="period" class="border rounded px-3 py-2 text-sm">
                        <option value="">Semua Bulan & Tahun</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->key }}" {{ (isset($period) && $period == $p->key) ? 'selected' : '' }}>{{ $p->label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="ml-2 px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
                </form>

                <a href="{{ route('reports.pdf', request()->query()) }}" class="px-3 py-2 bg-blue-600 text-white rounded">Eksport ke PDF</a>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="text-lg font-bold mb-4">Daftar Laporan</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Bulan & Tahun</th>
                        <th class="px-4 py-3">Total Transaksi</th>
                        <th class="px-4 py-3">Pendapatan Perbulan</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($rows as $row)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $row->period }}</td>
                            <td class="px-4 py-3">{{ number_format($row->count,0,',','.') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($row->total,0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center" colspan="3">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
