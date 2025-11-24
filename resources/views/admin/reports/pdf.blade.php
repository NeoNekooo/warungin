@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto bg-white print:p-0 print:shadow-none" style="font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;">
    <h1 class="text-2xl font-bold mb-4">Laporan Harian</h1>

    <table class="w-full text-sm border-collapse">
        <thead>
            <tr>
                <th class="text-left border-b py-2">Tanggal</th>
                <th class="text-right border-b py-2">Jumlah Transaksi</th>
                <th class="text-right border-b py-2">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td class="py-2">{{ $row->tanggal }}</td>
                <td class="py-2 text-right">{{ $row->count }}</td>
                <td class="py-2 text-right">{{ number_format($row->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        <a href="{{ route('reports.pdf') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Download PDF</a>
        <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded ml-2">Print</button>
    </div>
</div>
@endsection
