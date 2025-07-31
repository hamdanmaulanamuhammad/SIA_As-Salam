<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $user = Auth::user();
        if (!$user->accepted) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda belum disetujui oleh admin. Silakan tunggu persetujuan.');
        }

        if ($user->role !== $role) {
            if ($user->role === 'admin') {
                return redirect()->route('dashboard-admin')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            } elseif ($user->role === 'pengajar') {
                return redirect()->route('dashboard-pengajar')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            }
        }

        return $next($request);
    }
}
