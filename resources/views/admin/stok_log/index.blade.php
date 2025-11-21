@extends('layouts.app')

@section('content')
    <main class="flex-1 p-6">
        <div class="space-y-6">
            
            <!-- Header -->
            <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Stok Log</h2>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg shadow-lg bg-white p-4">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-3 border-b">No</th>
                            <th class="px-4 py-3 border-b">Produk</th>
                            <th class="px-4 py-3 border-b">Tanggal</th>
                            <th class="px-4 py-3 border-b">Tipe</th>
                            <th class="px-4 py-3 border-b">Jumlah</th>
                            <th class="px-4 py-3 border-b">Sumber</th>
                            <th class="px-4 py-3 border-b">Keterangan</th>
                            <th class="px-4 py-3 border-b">User</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700">
                        @forelse($stokLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 border-b">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->produk->nama_produk ?? 'Produk dihapus' }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->tanggal }}</td>
                            <td class="px-4 py-3 border-b capitalize">{{ $log->tipe }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->jumlah }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->sumber }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->keterangan }}</td>
                            <td class="px-4 py-3 border-b">{{ $log->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-4 py-3 border-b text-center" colspan="8">Tidak ada data stok log</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection