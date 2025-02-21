<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Maestroerror\HeicToJpg;
use Illuminate\Support\Facades\Storage;
use Log;

class PresenceController extends Controller
{
    // Menampilkan halaman presensi
    public function index()
    {
        // Mengambil semua data presensi beserta data pengguna
        $presences = Presence::with('user')->whereDate('date', date('Y-m-d'))->get();
        $users = User::where('role', 'pengajar')->get();
        return view('admin.presence-admin', compact('presences', 'users'));
    }

    // Menyimpan data presensi baru
// Menyimpan data presensi baru
    public function store(Request $request)
    {
        // Log untuk debugging
        Log::info('Data diterima:', $request->all());

        // Validasi umum
        $rules = [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:presence,leave',
            'date' => 'required|date',
        ];

        if ($request->type === 'presence') {
            $rules = array_merge($rules, [
                'arrival_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'class' => 'nullable|string',
                'material' => 'nullable|string',
                'issues' => 'nullable|string',
            ]);
        } elseif ($request->type === 'leave') {
            $rules = array_merge($rules, [
                'leave_reason' => 'required|string',
                'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }

        // Validasi request
        try {
            $validatedData = $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }

        // **Cek apakah user sudah melakukan presensi pada hari yang sama**
        $existingPresence = Presence::where('user_id', $request->user_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingPresence) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajar hanya bisa melakukan presensi 1x dalam sehari.',
            ], 422);
        }

        // Simpan bukti izin jika ada
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $proofPath = $file->storeAs(
                'proofs',
                time() . '_' . $file->getClientOriginalName(),
                'public'
            );
        }

        // Simpan data ke database
        $presence = Presence::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'arrival_time' => $request->type === 'presence' ? $request->arrival_time : null,
            'end_time' => $request->type === 'presence' ? $request->end_time : null,
            'class' => $request->type === 'presence' ? $request->class : null,
            'material' => $request->type === 'presence' ? $request->material : null,
            'proof' => $proofPath,
            'leave_reason' => $request->type === 'leave' ? $request->leave_reason : null,
            'issues' => $request->type === 'presence' ? $request->issues : null,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type) . ' berhasil disimpan.',
            'data' => $presence
        ]);
    }


    // Metode untuk mengedit data presensi
    public function edit($id)
    {
        $presence = Presence::findOrFail($id);
        return response()->json($presence);
    }

    // Metode untuk memperbarui data presensi
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'arrival_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'class' => 'nullable|string',
            'material' => 'nullable|string',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic|max:2048',
            'leave_reason' => 'nullable|string',
            'issues' => 'nullable|string',
            'type' => 'required|in:presence,leave',
        ]);
        
        $presence = Presence::findOrFail($id);

        // Simpan bukti baru jika ada
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'heic') {
                // Konversi HEIC ke JPG
                $convertedPath = 'proofs/' . time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.jpg';
                HeicToJpg::convert($file->getPathname())->saveAs(storage_path('app/public/' . $convertedPath));
                $presence->proof = $convertedPath;
            } else {
                // Simpan file non-HEIC seperti biasa
                $presence->proof = $file->storeAs('proofs', time() . '_' . $file->getClientOriginalName(), 'public');
            }
        }

        // Update data presensi
        $presence->update([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'arrival_time' => $request->arrival_time,
            'end_time' => $request->end_time,
            'class' => $request->class,
            'material' => $request->material,
            'leave_reason' => $request->leave_reason,
            'issues' => $request->issues,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil diperbarui.'
        ]);
    }

    // Metode untuk menghapus data presensi
    public function destroy($id)
    {
        $presence = Presence::findOrFail($id);
        $presence->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Presensi berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Presensi berhasil dihapus.');
    }

}
