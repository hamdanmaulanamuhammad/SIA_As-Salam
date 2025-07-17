<?php

namespace App\Http\Controllers;

use App\Models\KelasMapelSemester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PengajarKelasMapelSemesterController extends Controller
{
    public function __construct()
    {
        Log::info('=== PengajarKelasMapelSemesterController instantiated ===');
    }

    public function store(Request $request)
    {
        Log::info('=== PengajarKelasMapelSemesterController::store dipanggil ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'kelas_semester_id' => 'required|exists:kelas_semester,id',
            'mata_pelajaran_id' => 'required|exists:mapels,id|unique:kelas_mapel_semester,mata_pelajaran_id,NULL,id,kelas_semester_id,' . $request->kelas_semester_id,
        ], [
            'kelas_semester_id.required' => 'Kelas semester wajib diisi.',
            'kelas_semester_id.exists' => 'Kelas semester tidak ditemukan.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib diisi.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak ditemukan.',
            'mata_pelajaran_id.unique' => 'Mata pelajaran ini sudah ditambahkan ke kelas semester.'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in PengajarKelasMapelSemesterController::store', $validator->errors()->toArray());
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

    public function destroy($id)
    {
        Log::info('=== PengajarKelasMapelSemesterController::destroy dipanggil ===', ['id' => $id]);

        try {
            $kelasMapelSemester = KelasMapelSemester::findOrFail($id);
            $kelasMapelSemester->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil dihapus dari kelas.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PengajarKelasMapelSemesterController::destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data mata pelajaran tidak ditemukan.'
            ], 404);
        }
    }
}
