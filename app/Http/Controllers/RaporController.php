<?php

namespace App\Http\Controllers;

use App\Models\Catatan;
use App\Models\Kelas;
use App\Models\KelasSemester;
use App\Models\KelasMapelSemester;
use App\Models\NilaiRapor;
use App\Models\Santri;
use App\Models\SantriKelasSemester;
use App\Models\CatatanRapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RaporController extends Controller
{
    public function index($kelasSemesterId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'santriKelas.santri'])
            ->findOrFail($kelasSemesterId);

        // Ambil santri dari santri_kelas_semester
        $santriList = $kelasSemester->santriKelas;

        $hasMapel = KelasMapelSemester::where('kelas_semester_id', $kelasSemesterId)->exists();

        return view('admin.list-santri-rapor', compact('kelasSemester', 'santriList', 'hasMapel'));
    }

    public function storeSantri(Request $request, $kelasSemesterId)
    {
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);

        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = SantriKelasSemester::where('santri_id', $request->santri_id)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Santri sudah terdaftar di kelas ini.'
            ], 422);
        }

        SantriKelasSemester::create([
            'santri_id' => $request->santri_id,
            'kelas_semester_id' => $kelasSemesterId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Santri berhasil ditambahkan ke kelas.'
        ]);
    }

    public function edit($kelasSemesterId, $santriKelasSemesterId)
    {
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);
        $santriKelasSemester = SantriKelasSemester::with('santri')->findOrFail($santriKelasSemesterId);

        // Ambil santri yang belum terdaftar di kelas_semester ini
        $santriList = Santri::whereDoesntHave('kelasSemesters', function ($query) use ($kelasSemesterId) {
            $query->where('kelas_semester_id', $kelasSemesterId);
        })->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $santriKelasSemester->id,
                'santri_id' => $santriKelasSemester->santri_id,
                'santri_name' => $santriKelasSemester->santri->nama_lengkap,
                'santri_nis' => $santriKelasSemester->santri->nis,
                'santri_list' => $santriList->map(function ($santri) {
                    return [
                        'id' => $santri->id,
                        'name' => $santri->nama_lengkap,
                        'nis' => $santri->nis,
                    ];
                })->toArray(),
            ]
        ]);
    }

    public function update(Request $request, $kelasSemesterId, $santriKelasSemesterId)
    {
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);
        $santriKelasSemester = SantriKelasSemester::findOrFail($santriKelasSemesterId);

        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = SantriKelasSemester::where('santri_id', $request->santri_id)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->where('id', '!=', $santriKelasSemesterId)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Santri sudah terdaftar di kelas ini.'
            ], 422);
        }

        $santriKelasSemester->update([
            'santri_id' => $request->santri_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Santri berhasil diperbarui.'
        ]);
    }

    public function destroy($kelasSemesterId, $santriKelasSemesterId)
    {
        $santriKelasSemester = SantriKelasSemester::findOrFail($santriKelasSemesterId);
        $santriKelasSemester->delete();

        return response()->json([
            'success' => true,
            'message' => 'Santri berhasil dihapus dari kelas.'
        ]);
    }

    public function show($kelasSemesterId, $santriId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])
            ->findOrFail($kelasSemesterId);
        $santri = Santri::findOrFail($santriId);
        $kelasMapelSemesters = KelasMapelSemester::where('kelas_semester_id', $kelasSemesterId)
            ->with(['mapel', 'nilaiRapor' => function ($query) use ($santriId) {
                $query->where('santri_id', $santriId);
            }])
            ->get()
            ->groupBy('mapel.kategori');
        $santriKelasSemester = $santri->kelasSemesters()->where('kelas_semester_id', $kelasSemesterId)->first();

        if (!$santriKelasSemester) {
            return redirect()->back()->with('error', 'Santri tidak terdaftar di kelas ini.');
        }

        $nilaiRapor = NilaiRapor::where('santri_id', $santriId)
            ->whereIn('kelas_mapel_semester_id', $kelasMapelSemesters->pluck('id')->flatten())
            ->get();
        $averageNilai = $nilaiRapor->avg('nilai') ?? 0;
        $predikat = $this->calculatePredikat($averageNilai);

        $catatanRapor = CatatanRapor::where('santri_id', $santriId)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->first();
        $catatan = $catatanRapor ? $catatanRapor->catatan : '';
        $keputusan = $catatanRapor ? $catatanRapor->keputusan_kelas_id : null;
        $kelasList = Kelas::all();

        Log::info('Show Rapor:', [
            'santri_id' => $santriId,
            'kelas_semester_id' => $kelasSemesterId,
            'nilai_rapor' => $nilaiRapor->toArray(),
            'average_nilai' => $averageNilai,
            'predikat' => $predikat,
            'catatan' => $catatan,
            'keputusan' => $keputusan,
        ]);

        return view('admin.rapor', compact('kelasSemester', 'santri', 'kelasMapelSemesters', 'averageNilai', 'predikat', 'kelasList', 'catatan', 'keputusan'));
    }

    public function store(Request $request, $kelasSemesterId, $santriId)
    {
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);
        $santri = Santri::findOrFail($santriId);
        $santriKelasSemester = $santri->kelasSemesters()->where('kelas_semester_id', $kelasSemesterId)->first();

        if (!$santriKelasSemester) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak terdaftar di kelas ini.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'keputusan' => 'nullable|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $nilaiData = $request->input('nilai');
        $catatan = $request->input('catatan');
        $keputusan = $request->input('keputusan');

        $totalNilai = array_sum($nilaiData);
        $countNilai = count($nilaiData);
        $averageNilai = $countNilai > 0 ? $totalNilai / $countNilai : 0;
        $predikat = $this->calculatePredikat($averageNilai);

        foreach ($nilaiData as $kelasMapelSemesterId => $nilai) {
            $kelasMapelSemester = KelasMapelSemester::where('kelas_semester_id', $kelasSemesterId)
                ->findOrFail($kelasMapelSemesterId);

            NilaiRapor::updateOrCreate(
                [
                    'santri_id' => $santriId,
                    'kelas_mapel_semester_id' => $kelasMapelSemesterId,
                ],
                [
                    'nilai' => $nilai,
                    'predikat' => $predikat,
                ]
            );
        }

        Catatan::updateOrCreate(
            [
                'santri_id' => $santriId,
                'kelas_semester_id' => $kelasSemesterId,
            ],
            [
                'catatan' => $catatan,
                'keputusan_kelas_id' => $keputusan,
            ]
        );

        Log::info('Store Rapor:', [
            'santri_id' => $santriId,
            'kelas_semester_id' => $kelasSemesterId,
            'nilai_data' => $nilaiData,
            'average_nilai' => $averageNilai,
            'predikat' => $predikat,
            'catatan' => $catatan,
            'keputusan' => $keputusan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rapor berhasil disimpan.'
        ]);
    }

    private function calculatePredikat($nilai)
    {
        if ($nilai >= 90) {
            return 'MUMTAZ';
        } elseif ($nilai >= 80) {
            return 'JAYYID JIDDAN';
        } elseif ($nilai >= 70) {
            return 'JAYYID';
        } else {
            return 'MAQBUL';
        }
    }
}
?>
