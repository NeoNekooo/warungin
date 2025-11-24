@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ isset($promo) ? 'Edit Promo' : 'Buat Promo' }}</h1>
        <a href="{{ route('promos.index') }}" class="px-3 py-1 text-sm text-gray-600">Kembali</a>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <form action="{{ isset($promo) ? route('promos.update', $promo) : route('promos.store') }}" method="POST">
            @csrf
            @if(isset($promo)) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-4">
                <input type="text" name="name" placeholder="Nama promo" value="{{ old('name', $promo->name ?? '') }}" class="border p-2 rounded">
                <input type="text" name="code" placeholder="Kode promo" value="{{ old('code', $promo->code ?? '') }}" class="border p-2 rounded">
                <textarea name="description" rows="3" placeholder="Deskripsi" class="border p-2 rounded">{{ old('description', $promo->description ?? '') }}</textarea>
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" step="0.01" name="discount" placeholder="Diskon (nominal)" value="{{ old('discount', $promo->discount ?? '') }}" class="border p-2 rounded">
                    <input type="number" name="percent" placeholder="Diskon (%)" value="{{ old('percent', $promo->percent ?? '') }}" class="border p-2 rounded">
                </div>
                <div class="flex items-center space-x-3">
                    <label class="flex items-center"><input type="checkbox" name="active" value="1" {{ old('active', $promo->active ?? true) ? 'checked' : '' }} class="mr-2"> Aktif</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', isset($promo->starts_at) ? $promo->starts_at->format('Y-m-d') : '') }}" class="border p-2 rounded">
                    <input type="date" name="ends_at" value="{{ old('ends_at', isset($promo->ends_at) ? $promo->ends_at->format('Y-m-d') : '') }}" class="border p-2 rounded">
                </div>

                <div class="flex items-center justify-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
