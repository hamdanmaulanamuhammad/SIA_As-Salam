<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    // Menampilkan profil admin
    public function showAdminProfile()
    {
        $user = Auth::user();
        return view('admin.profile-admin', compact('user'));
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

        // Menyimpan pesan sukses ke session
        return redirect()->route('profile-admin')->with('success', 'Profil berhasil diperbarui.');
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
        return redirect()->route('profile-admin')->with('success', 'Foto profil berhasil diunggah.');
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
    return redirect()->route('profile-admin')->with('success', 'Foto profil berhasil dihapus.');
    }

    
}
