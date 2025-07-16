<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\KelasSemester;
use App\Models\Santri;
use App\Models\SantriKelasSemester;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KelasSemesterController extends Controller
{
    public function __construct()
    {
        Log::info('=== KelasSemesterController instantiated ===');
    }
    public function store(Request $request, $semesterId)
    {
        Log::info('=== KelasSemesterController::store called ===', [
            'url' => $request->url(),
            'method' => $request->method(),
            'semester_id' => $semesterId,
            'data' => $request->all()
        ]);

        // Prevent misrouting
        if ($request->url() === url('akademik/kelas-semester/mapel')) {
            Log::error('Request to mapel route incorrectly handled by KelasSemesterController');
            return response()->json([
                'success' => false,
                'message' => 'Invalid route: This should be handled by KelasMapelSemesterController.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
            'mudir_id' => 'required|exists:users,id',
        ], [
            'kelas_id.required' => 'Nama kelas wajib diisi.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'wali_kelas_id.required' => 'Wali kelas wajib diisi.',
            'wali_kelas_id.exists' => 'Wali kelas tidak ditemukan.',
            'mudir_id.required' => 'Mudir wajib diisi.',
            'mudir_id.exists' => 'Mudir tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in KelasSemesterController::store', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = KelasSemester::where('semester_id', $semesterId)
            ->where('kelas_id', $request->kelas_id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas ini sudah ditambahkan ke semester ini.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $kelasSemester = KelasSemester::create([
                'kelas_id' => $request->kelas_id,
                'semester_id' => $semesterId,
                'wali_kelas_id' => $request->wali_kelas_id,
                'mudir_id' => $request->mudir_id,
            ]);

            $santri = Santri::where('kelas_id', $request->kelas_id)
                ->where('status', 'Aktif')
                ->get();

            foreach ($santri as $s) {
                // Prevent duplicate entries
                if (!SantriKelasSemester::where('santri_id', $s->id)
                    ->where('kelas_semester_id', $kelasSemester->id)
                    ->exists()) {
                    SantriKelasSemester::create([
                        'santri_id' => $s->id,
                        'kelas_semester_id' => $kelasSemester->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kelas semester berhasil dibuat dan santri ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KelasSemesterController::store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], 500);
        }
    }

    public function index($semesterId)
    {
        Log::info('=== KelasSemesterController::index dipanggil ===');
        Log::info('Semester ID: ' . $semesterId);

        $semester = Semester::findOrFail($semesterId);
        $kelasSemesters = KelasSemester::where('semester_id', $semesterId)
            ->with(['kelas', 'waliKelas', 'mudir', 'mapels.mataPelajaran'])
            ->paginate(9);

        return view('admin.kelas-semester', compact('semester', 'kelasSemesters'));
    }

    public function edit($id)
    {
        Log::info('=== KelasSemesterController::edit dipanggil ===');
        Log::info('KelasSemester ID: ' . $id);

        $kelasSemester = KelasSemester::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $kelasSemester->id,
                'kelas_id' => $kelasSemester->kelas_id,
                'wali_kelas_id' => $kelasSemester->wali_kelas_id,
                'mudir_id' => $kelasSemester->mudir_id,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::info('=== KelasSemesterController::update dipanggil ===');
        Log::info('KelasSemester ID: ' . $id);
        Log::info('Request Data: ', $request->all());

        $kelasSemester = KelasSemester::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
            'mudir_id' => 'required|exists:users,id',
        ], [
            'kelas_id.required' => 'Nama kelas wajib diisi.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'wali_kelas_id.required' => 'Wali kelas wajib diisi.',
            'wali_kelas_id.exists' => 'Wali kelas tidak ditemukan.',
            'mudir_id.required' => 'Mudir wajib diisi.',
            'mudir_id.exists' => 'Mudir tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal pada KelasSemesterController::update: ', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = KelasSemester::where('semester_id', $kelasSemester->semester_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas ini sudah ditambahkan ke semester ini.'
            ], 422);
        }

        $kelasSemester->update([
            'kelas_id' => $request->kelas_id,
            'wali_kelas_id' => $request->wali_kelas_id,
            'mudir_id' => $request->mudir_id,
        ]);

        if ($kelasSemester->wasChanged('kelas_id')) {
            SantriKelasSemester::where('kelas_semester_id', $kelasSemester->id)->delete();
            $santri = Santri::where('kelas_id', $request->kelas_id)
                ->where('status', 'Aktif')
                ->get();

            foreach ($santri as $s) {
                SantriKelasSemester::create([
                    'santri_id' => $s->id,
                    'kelas_semester_id' => $kelasSemester->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kelas semester berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        Log::info('=== KelasSemesterController::destroy dipanggil ===');
        Log::info('KelasSemester ID: ' . $id);

        $kelasSemester = KelasSemester::findOrFail($id);
        $kelasSemester->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas semester berhasil dihapus.'
        ]);
    }
}
