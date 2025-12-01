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
}
