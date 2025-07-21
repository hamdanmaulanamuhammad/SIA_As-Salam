<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // Menampilkan profil
    public function showProfile()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return view('admin.profile-admin', compact('user'));
        } elseif ($user->role === 'pengajar') {
            return view('pengajar.profile-pengajar', compact('user'));
        }
        return response()->json([
            'success' => false,
            'message' => 'Role tidak dikenali.'
        ], 400);
    }

    // Mengupdate data profil
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:users,username,' . Auth::id(),
                'phone' => 'required|string|max:15',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
            ], [
                'full_name.required' => 'Nama lengkap wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan.',
                'phone.required' => 'No HP wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.'
            ]);

            $user = Auth::user();
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->save();

            Log::info('Profile updated successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in updateProfile', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()[array_key_first($e->errors())][0]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating profile', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil.'
            ], 500);
        }
    }

    // Mengupload foto profil
    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'photo.required' => 'File foto wajib diisi.',
                'photo.image' => 'File harus berupa gambar.',
                'photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
                'photo.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            $user = Auth::user();

            if ($user->photo) {
                Storage::delete($user->photo);
            }

            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
            $user->save();

            Log::info('Photo uploaded successfully', ['user_id' => $user->id, 'path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diunggah.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in uploadPhoto', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()[array_key_first($e->errors())][0]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading photo', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah foto.'
            ], 500);
        }
    }

    // Menghapus foto profil
    public function deletePhoto(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->photo) {
                Storage::delete($user->photo);
                $user->photo = null;
                $user->save();

                Log::info('Photo deleted successfully', ['user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting photo', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto.'
            ], 500);
        }
    }

    // Mengupload tanda tangan
    public function uploadSignature(Request $request)
    {
        try {
            $request->validate([
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'x' => 'required|numeric',
                'y' => 'required|numeric',
                'width' => 'required|numeric',
                'height' => 'required|numeric',
            ], [
                'signature.required' => 'File tanda tangan wajib diisi.',
                'signature.image' => 'File harus berupa gambar.',
                'signature.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
                'signature.max' => 'Ukuran gambar maksimal 2MB.',
                'x.required' => 'Koordinat X wajib diisi.',
                'y.required' => 'Koordinat Y wajib diisi.',
                'width.required' => 'Lebar wajib diisi.',
                'height.required' => 'Tinggi wajib diisi.'
            ]);

            $user = Auth::user();

            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }

            $file = $request->file('signature');
            $tempPath = $file->store('temp', 'public');
            $signatureDir = 'rapor/ttd';
            if (!Storage::disk('public')->exists($signatureDir)) {
                Storage::disk('public')->makeDirectory($signatureDir);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read(Storage::disk('public')->path($tempPath));
            $image->crop(
                (int)$request->width,
                (int)$request->height,
                (int)$request->x,
                (int)$request->y
            );
            $image->resize(912, 462);
            $filename = 'signature_' . $user->id . '_' . time() . '.png';
            $signaturePath = $signatureDir . '/' . $filename;
            $image->save(Storage::disk('public')->path($signaturePath));
            Storage::disk('public')->delete($tempPath);

            $user->signature = $signaturePath;
            $user->save();

            Log::info('Signature uploaded successfully', ['user_id' => $user->id, 'path' => $signaturePath]);

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil diunggah.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in uploadSignature', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()[array_key_first($e->errors())][0]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading signature', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah tanda tangan.'
            ], 500);
        }
    }

    // Menghapus tanda tangan
    public function deleteSignature(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
                $user->signature = null;
                $user->save();

                Log::info('Signature deleted successfully', ['user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting signature', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus tanda tangan.'
            ], 500);
        }
    }
}
