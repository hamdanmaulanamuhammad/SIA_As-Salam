<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class LoginRegisterController extends Controller
{
    // Menampilkan form registrasi
    public function register()
    {
        return view('auth.register'); 
    }

    // Menyimpan data form registrasi
    public function store(Request $request)
    {    
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:15',
            'university' => 'required|string|max:100',
            'address' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
 
        // Membuat pengguna baru dengan accepted default false
        User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'university' => $request->university,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'pengajar',
            'accepted' => false, // Set accepted ke false secara default
        ]);     
 
        // Redirect ke halaman login setelah registrasi berhasil
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }
 
    // Menampilkan form login
    public function login()
    {
        return view('auth.login'); // Pastikan view ini ada
    }

    // Mengautentikasi credential pengguna yang login
    public function authenticate(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba untuk mengautentikasi pengguna
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Periksa apakah pengguna diterima
            if (!$user->accepted) {
                Auth::logout(); // Logout pengguna jika tidak diterima
                throw ValidationException::withMessages([
                    'email' => 'Your registration is not yet accepted by the admin.',
                ]);
            }

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('dashboard-admin');
            } else {
                return redirect()->route('dashboard-pengajar');
            }
        }

        // Jika autentikasi gagal, lemparkan exception dengan pesan kesalahan
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Menampilkan layar dashboard kepada pengguna yang telah terautentikasi
    public function dashboard()
    {
        return view('dashboard'); // Ganti dengan view dashboard yang sesuai
    }

    // Melakukan operasi logout oleh pengguna
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success','You have logged out successfully!');
    }
}
