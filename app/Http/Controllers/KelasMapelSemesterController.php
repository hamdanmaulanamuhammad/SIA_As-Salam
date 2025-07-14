<?php

namespace App\Http\Controllers;

use App\Models\KelasSemester;
use App\Models\KelasMapelSemester;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasMapelSemesterController extends Controller
{
    /**
     * Display a listing of mata pelajaran for a specific kelas_semester.
     */
    public function index($kelasSemesterId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester'])->findOrFail($kelasSemesterId);
        $kelasMapelSemesters = KelasMapelSemester::where('kelas_semester_id', $kelasSemesterId)->with('mapel')->paginate(10);
        return view('admin.kelas-mapel-semester', compact('kelasSemester', 'kelasMapelSemesters'));
    }

    /**
     * Show the form for creating a new kelas-mapel-semester relation.
     */
    public function create($kelasSemesterId)
    {
        // Tidak digunakan karena menggunakan modal di index
        return response()->json(['message' => 'Method not used. Use modal in index.']);
    }

    /**
     * Store a newly created kelas-mapel-semester relation in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_semester_id' => 'required|exists:kelas_semester,id',
            'mata_pelajaran_id' => 'required|exists:mapels,id|unique:kelas_mapel_semester,mata_pelajaran_id,NULL,id,kelas_semester_id,' . $request->kelas_semester_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $kelasMapelSemester = KelasMapelSemester::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil ditambahkan ke kelas.',
            'data' => $kelasMapelSemester
        ]);
    }

    /**
     * Display the specified kelas-mapel-semester relation.
     */
    public function show($id)
    {
        try {
            $kelasMapelSemester = KelasMapelSemester::with('mataPelajaran')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $kelasMapelSemester
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mata pelajaran tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified kelas-mapel-semester relation.
     */
    public function edit($id)
    {
        // Tidak digunakan karena menggunakan modal di index
        return response()->json(['message' => 'Method not used. Use modal in index.']);
    }

    /**
     * Update the specified kelas-mapel-semester relation in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'mata_pelajaran_id' => 'required|exists:mapels,id|unique:kelas_mapel_semester,mata_pelajaran_id,' . $id . ',id,kelas_semester_id,' . $request->kelas_semester_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kelasMapelSemester = KelasMapelSemester::findOrFail($id);
            $kelasMapelSemester->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diperbarui.',
                'data' => $kelasMapelSemester
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mata pelajaran tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Remove the specified kelas-mapel-semester relation from storage.
     */
    public function destroy($id)
    {
        try {
            $kelasMapelSemester = KelasMapelSemester::findOrFail($id);
            $kelasMapelSemester->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil dihapus dari kelas.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mata pelajaran tidak ditemukan.'
            ], 404);
        }
    }
}
