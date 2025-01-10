<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PengajarController extends Controller
{
    // Menampilkan permintaan registrasi pengajar
    public function showRegistrationRequests()
    {
        $users = User::where('accepted', false)->paginate(10);
        return view('admin.registration-request-admin', compact('users'));
    }

    // Menerima pendaftaran pengajar
    public function acceptRegistration($id)
    {
        $user = User::findOrFail($id);
        $user->accepted = true;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Pendaftaran diterima.']);
    }

    // Menolak pendaftaran pengajar
    public function rejectRegistration($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Pendaftaran ditolak.']);
    }

    // Menampilkan daftar pengajar
    public function showTeacherDetails()
    {
        // Ambil data pengajar yang diterima
        $teachers = User::where('role', 'pengajar')->where('accepted', true)->get();
        return view('admin.teacher-details-admin', compact('teachers'));
    }


    // Menghapus pengajar
    public function deleteTeacher($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Pengajar berhasil dihapus.']);
    }
}
