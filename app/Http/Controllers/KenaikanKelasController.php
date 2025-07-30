<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasSemester;
use App\Models\Santri;
use App\Models\SantriKelasSemester;
use App\Models\CatatanRapor;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KenaikanKelasController extends Controller
{
    /**
     * Display the kenaikan kelas page with a list of kelas_semester.
     */
    public function index()
    {
        $kelasSemesters = KelasSemester::with(['kelas', 'semester'])
            ->where('sudah_diproses', false)
            ->get();

        return view('admin.kenaikan-kelas.index', compact('kelasSemesters'));
    }

    /**
     * Show santri list for a specific kelas_semester.
     */
    public function show($kelasSemesterId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'santriKelas.santri'])
            ->findOrFail($kelasSemesterId);

        if ($kelasSemester->sudah_diproses) {
            return redirect()->route('admin.kenaikan-kelas.index')
                ->with('error', 'Kelas semester ini sudah diproses.');
        }

        $santriList = $kelasSemester->santriKelas->map(function ($santriKelas) {
            $catatanRapor = CatatanRapor::where('santri_id', $santriKelas->santri_id)
                ->where('kelas_semester_id', $santriKelas->kelas_semester_id)
                ->first();
            return [
                'santri_id' => $santriKelas->santri_id,
                'nis' => $santriKelas->santri->nis,
                'nama_lengkap' => $santriKelas->santri->nama_lengkap,
                'keputusan_kelas_id' => $catatanRapor ? $catatanRapor->keputusan_kelas_id : null,
                'keputusan_kelas_nama' => $catatanRapor && $catatanRapor->keputusan_kelas_id
                    ? Kelas::find($catatanRapor->keputusan_kelas_id)->nama
                    : 'Belum diputuskan',
            ];
        });

        return view('admin.kenaikan-kelas.show', compact('kelasSemester', 'santriList'));
    }

    /**
     * Process kenaikan kelas for a specific kelas_semester.
     */
    public function process(Request $request, $kelasSemesterId)
    {
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);

        if ($kelasSemester->sudah_diproses) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas semester ini sudah diproses.'
            ], 403);
        }

        // Get next semester
        $currentSemester = $kelasSemester->semester;
        $nextSemester = Semester::where('tahun_ajaran', '>', $currentSemester->tahun_ajaran)
            ->where('nama_semester', 'Semester Ganjil')
            ->first();

        if (!$nextSemester) {
            return response()->json([
                'success' => false,
                'message' => 'Semester berikutnya belum tersedia. Silakan buat semester baru.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $santriKelasSemesters = SantriKelasSemester::where('kelas_semester_id', $kelasSemesterId)->get();

            foreach ($santriKelasSemesters as $santriKelas) {
                $catatanRapor = CatatanRapor::where('santri_id', $santriKelas->santri_id)
                    ->where('kelas_semester_id', $kelasSemesterId)
                    ->first();

                if ($catatanRapor && $catatanRapor->keputusan_kelas_id) {
                    // Update kelas_awal_id in santri table
                    Santri::where('id', $santriKelas->santri_id)
                        ->update(['kelas_awal_id' => $catatanRapor->keputusan_kelas_id]);

                    // Find or create kelas_semester for the next semester
                    $nextKelasSemester = KelasSemester::where('kelas_id', $catatanRapor->keputusan_kelas_id)
                        ->where('semester_id', $nextSemester->id)
                        ->first();

                    if (!$nextKelasSemester) {
                        $nextKelasSemester = KelasSemester::create([
                            'kelas_id' => $catatanRapor->keputusan_kelas_id,
                            'semester_id' => $nextSemester->id,
                            'wali_kelas_id' => $kelasSemester->wali_kelas_id, // Use same wali_kelas for now
                            'mudir_id' => $kelasSemester->mudir_id, // Use same mudir for now
                            'sudah_diproses' => false,
                        ]);
                    }

                    // Create new entry in santri_kelas_semester for next semester
                    SantriKelasSemester::create([
                        'santri_id' => $santriKelas->santri_id,
                        'kelas_semester_id' => $nextKelasSemester->id,
                    ]);
                } elseif (!$catatanRapor || !$catatanRapor->keputusan_kelas_id) {
                    // If keputusan_kelas_id is null, mark santri as inactive
                    Santri::where('id', $santriKelas->santri_id)
                        ->update(['status' => 'Tidak Aktif']);
                }
            }

            // Mark kelas_semester as processed
            $kelasSemester->update(['sudah_diproses' => true]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Proses kenaikan kelas berhasil.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses kenaikan kelas.'
            ], 500);
        }
    }
}
