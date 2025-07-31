<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginRegisterController extends Controller
{
    // Menampilkan form registrasi pengguna biasa
    public function register()
    {
        return view('auth.register');
    }

    // Menyimpan data form registrasi pengguna biasa
    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:50|unique:users',
                'full_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:15',
                'university' => 'required|string|max:100',
                'address' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'university' => $request->university,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role' => 'pengajar',
                'accepted' => false,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil! Silakan tunggu persetujuan admin untuk dapat login.',
                'redirect' => route('login')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => implode('\n', array_merge(...array_values($e->errors()))),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'
            ], 500);
        }
    }

    // Menampilkan form registrasi admin
    public function adminRegister()
    {
        return view('auth.register-admin');
    }

    // Menyimpan data form registrasi admin
    public function adminStore(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:50|unique:users',
                'full_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:15',
                'university' => 'required|string|max:100',
                'address' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'university' => $request->university,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'accepted' => true,
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi admin berhasil! Silakan login.',
                'redirect' => route('login')
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => implode('\n', array_merge(...array_values($e->errors()))),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mendaftar admin. Silakan coba lagi.'
            ], 500);
        }
    }

    // Menampilkan form login
    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard-admin');
            } elseif ($user->role === 'pengajar') {
                return redirect()->route('dashboard-pengajar');
            }
        }
        return view('auth.login');
    }

    // Mengautentikasi credential pengguna yang login
    public function authenticate(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                if (!$user->accepted) {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akun Anda belum disetujui oleh admin. Silakan tunggu persetujuan.'
                    ], 403);
                }

                $redirect = $user->role === 'admin' ? route('dashboard-admin') : route('dashboard-pengajar');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Selamat datang, ' . $user->full_name . '!',
                    'redirect' => $redirect
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password yang Anda masukkan salah.'
            ], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => implode('\n', $e->errors()['email'] ?? $e->errors()['password'] ?? ['Validasi gagal.'])
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat login. Silakan coba lagi.'
            ], 500);
        }
    }

    // Menampilkan layar dashboard kepada pengguna yang telah terautentikasi
    public function dashboard()
    {

        return view('dashboard');
    }

    // Melakukan operasi logout oleh pengguna
    public function logout(Request $request)
    {
        try {
            $userName = Auth::user()->full_name;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'Anda berhasil logout. Sampai jumpa, ' . $userName . '!',
                'redirect' => route('login')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat logout. Silakan coba lagi.'
            ], 500);
        }
    }
}
