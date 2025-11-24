<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:191',
            'code' => 'nullable|string|max:64',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'percent' => 'nullable|integer|min:0|max:100',
            'type' => 'nullable|string|max:32',
            'active' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $data['active'] = $request->has('active') ? (bool) $request->input('active') : true;
        Promo::create($data);

        return redirect()->route('promos.index')->with('success', 'Promo berhasil dibuat');
    }

    public function edit(Promo $promo)
    {
        return view('admin.promos.create', compact('promo'));
    }

    public function update(Request $request, Promo $promo)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:191',
            'code' => 'nullable|string|max:64',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'percent' => 'nullable|integer|min:0|max:100',
            'type' => 'nullable|string|max:32',
            'active' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $data['active'] = $request->has('active') ? (bool) $request->input('active') : $promo->active;
        $promo->update($data);

        return redirect()->route('promos.index')->with('success', 'Promo diperbarui');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('promos.index')->with('success', 'Promo dihapus');
    }
}
