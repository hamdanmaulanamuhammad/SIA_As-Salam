<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\User;
use Auth;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
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
    public function indexPengajar()
    {
        $user_id = Auth::id();

        $presences = Presence::where('user_id', $user_id)
                            ->whereDate('date', date('Y-m-d'))
                            ->get();

        $recentPresences = Presence::where('user_id', $user_id)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

        return view('pengajar.dashboard-pengajar', compact('presences', 'recentPresences'));
    }


    // Menyimpan data presensi baru
    public function store(Request $request)
    {
        // Log untuk debugging
        Log::info('Data diterima:', $request->all());

        // Cek route untuk menentukan apakah request dari admin atau pengajar
        $route = $request->route()->getName();
        $isFromPengajar = strpos($route, 'pengajar') !== false;

        if ($isFromPengajar) {
            $request->merge(['user_id' => Auth::id()]);
        }

        // Validasi request
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'arrival_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'class' => 'required|string',
                'material' => 'required|string',
                'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'issues' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }

        // Cek apakah user sudah melakukan presensi pada hari yang sama
        $existingPresence = Presence::where('user_id', $request->user_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingPresence) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajar hanya bisa melakukan presensi 1x dalam sehari.',
            ], 422);
        }

        // Simpan bukti presensi jika ada
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $proofPath = $file->storeAs(
                'proofs',
                time() . '_' . $file->getClientOriginalName(),
                'public'
            );
        }

        // Tentukan hari berdasarkan tanggal
        $day = date('l', strtotime($request->date));

        // Simpan data ke database
        $presence = new Presence();
        $presence->user_id = $request->user_id;
        $presence->date = $request->date;
        $presence->day = $day;
        $presence->arrival_time = $request->arrival_time;
        $presence->end_time = $request->end_time;
        $presence->class = $request->class;
        $presence->material = $request->material;
        $presence->proof = $proofPath;
        $presence->issues = $request->issues;
        $presence->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan.',
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
            'arrival_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'class' => 'required|string',
            'material' => 'required|string',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic|max:2048',
            'issues' => 'nullable|string',
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

        // Tentukan hari berdasarkan tanggal
        $day = date('l', strtotime($request->date));

        // Update data presensi
        $presence->user_id = $request->user_id;
        $presence->date = $request->date;
        $presence->day = $day;
        $presence->arrival_time = $request->arrival_time;
        $presence->end_time = $request->end_time;
        $presence->class = $request->class;
        $presence->material = $request->material;
        $presence->issues = $request->issues;
        $presence->save();

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
