<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    // Menampilkan profil admin
    public function showProfile()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return view('admin.profile-admin', compact('user'));
        } elseif ($user->role === 'pengajar') {
            return view('pengajar.profile-pengajar', compact('user'));
        }
    }

    // Mengupdate data profil
    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'email' => 'required|email',
        ]);

        $user = Auth::user();
        $user->full_name = $request->full_name;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        // Menentukan route berdasarkan role user
        if ($user->role === 'admin') {
            return redirect()->back()->with('success','berhasil memperbarui profile');
        } elseif ($user->role === 'pengajar') {
            return redirect()->back()->with('success','berhasil memperbarui profile');
        }

        // Jika role tidak dikenali, kirim error
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan dalam memperbarui profil.'
        ], 400);
    }

    // Mengupload foto profil
    public function uploadPhoto(Request $request)
    {
        // Validasi input
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->photo) {
            Storage::delete($user->photo);
        }

        // Simpan foto baru
        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->photo = $path;
        $user->save();

        // Menyimpan pesan sukses ke session
        if ($user->role === 'admin') {
            return redirect()->route('profile.admin.index')->with('success', 'Foto profil berhasil diunggah.');
        } elseif ($user->role === 'pengajar') {
            return redirect()->route('profile.pengajar.index')->with('success', 'Foto profil berhasil diunggah.');
        }
    }

    // Menghapus foto profil
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        // Hapus foto dari storage
        if ($user->photo) {
            Storage::delete($user->photo);
            $user->photo = null; // Set foto ke null
            $user->save();
        }

        // Menyimpan pesan sukses ke session
        if ($user->role === 'admin') {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus.',
                'redirect' => route('profile.admin.index')
            ]);
        } elseif ($user->role === 'pengajar') {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus.',
                'redirect' => route('profile.pengajar.index')
            ]);
        }
    }

    // Mengupload tanda tangan
    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        $user = Auth::user();

        // Hapus tanda tangan lama jika ada
        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
        }

        // Proses cropping dan simpan tanda tangan baru
        $file = $request->file('signature');
        $tempPath = $file->store('temp', 'public');

        // Buat direktori jika belum ada
        $signatureDir = 'rapor/ttd';
        if (!Storage::disk('public')->exists($signatureDir)) {
            Storage::disk('public')->makeDirectory($signatureDir);
        }

        // Inisialisasi Image Manager dengan GD driver
        $manager = new ImageManager(new Driver());

        // Baca dan crop gambar
        $image = $manager->read(Storage::disk('public')->path($tempPath));

        // Crop gambar sesuai parameter
        $image->crop(
            (int)$request->width,
            (int)$request->height,
            (int)$request->x,
            (int)$request->y
        );

        // Resize ke ukuran yang diinginkan
        $image->resize(912, 462);

        // Simpan dengan nama file yang unik
        $filename = 'signature_' . $user->id . '_' . time() . '.png';
        $signaturePath = $signatureDir . '/' . $filename;

        // Simpan gambar
        $image->save(Storage::disk('public')->path($signaturePath));

        // Hapus file temporary
        Storage::disk('public')->delete($tempPath);

        // Update database
        $user->signature = $signaturePath;
        $user->save();

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('profile.admin.index')->with('success', 'Tanda tangan berhasil diunggah.');
        } elseif ($user->role === 'pengajar') {
            return redirect()->route('profile.pengajar.index')->with('success', 'Tanda tangan berhasil diunggah.');
        }
    }

    // Menghapus tanda tangan
    public function deleteSignature(Request $request)
    {
        $user = Auth::user();

        // Hapus tanda tangan dari storage
        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
            $user->signature = null;
            $user->save();
        }

        // Menyimpan pesan sukses ke session
        if ($user->role === 'admin') {
            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil dihapus.',
                'redirect' => route('profile.admin.index')
            ]);
        } elseif ($user->role === 'pengajar') {
            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil dihapus.',
                'redirect' => route('profile.pengajar.index')
            ]);
        }
    }
}
