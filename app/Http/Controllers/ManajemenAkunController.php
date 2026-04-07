<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManajemenAkunController extends Controller
{
    public function __construct()
    {
        // Only admin can manage accounts
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') return abort(403);
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::orderBy('nama')->paginate(20);
        return view('admin.manajemen_akun.index', compact('users'));
    }

    public function create()
    {
        return view('admin.manajemen_akun.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,owner,kasir',
            'no_hp' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'username' => $validatedData['username'],
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'no_hp' => $validatedData['no_hp'],
            'status' => $validatedData['status'],
            'password' => \Illuminate\Support\Facades\Hash::make($validatedData['password']),
        ]);

        return redirect()->route('manajemen_akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->user_id.',user_id',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'role' => 'required|in:admin,owner,kasir',
            'no_hp' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'username' => $validatedData['username'],
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'no_hp' => $validatedData['no_hp'],
            'status' => $validatedData['status'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        return redirect()->route('manajemen_akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->user_id === auth()->id()) {
            return redirect()->route('manajemen_akun.index')->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('manajemen_akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}
