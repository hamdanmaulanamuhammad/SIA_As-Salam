<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        if (Auth::user()->role !== $role) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard-admin')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            } elseif ($user->role === 'pengajar') {
                return redirect()->route('dashboard-pengajar')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            }
        }

        return $next($request);
    }
}

