<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokLog;

class StokLogController extends Controller
{
     public function index()
    {
        $stokLogs = StokLog::orderBy('tanggal', 'desc')->paginate(30);
        return view('admin.stok_log.index', compact('stokLogs'));
    }

    public function __construct()
    {
        // stok log is admin-only
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !in_array($user->role, ['admin', 'owner', 'kasir'])) return abort(403);
            return $next($request);
        });
    }
}
