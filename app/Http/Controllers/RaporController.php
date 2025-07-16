<?php

namespace App\Http\Controllers;

use App\Models\KelasSemester;
use App\Models\SantriKelasSemester;
use App\Models\Santri;
use App\Models\KelasMapelSemester;
use App\Models\NilaiRapor;
use App\Models\Catatan;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    public function index($kelasSemesterId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])->findOrFail($kelasSemesterId);
        $santriList = SantriKelasSemester::with('santri')
            ->where('kelas_semester_id', $kelasSemesterId)
            ->paginate(10);
        return view('admin.list-santri-rapor', compact('kelasSemester', 'santriList'));
    }

    public function show($kelasSemesterId, $santriId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester'])->findOrFail($kelasSemesterId);
        $santri = Santri::findOrFail($santriId);
        $mapels = KelasMapelSemester::with('mataPelajaran')
            ->where('kelas_semester_id', $kelasSemesterId)
            ->get();
        $mapelsByCategory = $mapels->groupBy('mataPelajaran.kategori');
        $nilaiRaporData = NilaiRapor::where('santri_id', $santriId)
            ->whereIn('kelas_mapel_semester_id', $mapels->pluck('id'))
            ->get();
        $catatanRapor = Catatan::where('santri_id', $santriId)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->first();

        $jumlahNilai = $nilaiRaporData->sum('nilai');
        $rataRata = $nilaiRaporData->count() > 0 ? $jumlahNilai / $nilaiRaporData->count() : null;
        $predikat = $rataRata ? $this->calculatePredikat($rataRata) : null;
        $terbilang = $rataRata ? $this->numberToWords(round($rataRata)) : null;

        return view('admin.input-rapor', compact(
            'kelasSemester',
            'santri',
            'mapelsByCategory',
            'nilaiRaporData',
            'catatanRapor',
            'jumlahNilai',
            'rataRata',
            'predikat',
            'terbilang',
            'kelasSemesterId',
            'santriId'
        ));
    }

    public function update(Request $request, $kelasSemesterId, $santriId)
    {
        $request->validate([
            'nilai.*' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
            'keputusan_kelas_id' => 'nullable|exists:kelas,id',
        ]);

        // Simpan nilai rapor
        $totalNilai = 0;
        $countNilai = 0;
        foreach ($request->input('nilai', []) as $kelasMapelSemesterId => $nilai) {
            NilaiRapor::updateOrCreate(
                [
                    'santri_id' => $santriId,
                    'kelas_mapel_semester_id' => $kelasMapelSemesterId,
                ],
                [
                    'nilai' => $nilai,
                ]
            );
            $totalNilai += $nilai;
            $countNilai++;
        }

        // Hitung rata-rata dan predikat
        $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : null;
        $predikat = $rataRata ? $this->calculatePredikat($rataRata) : null;

        // Simpan catatan, keputusan kelas, dan predikat
        Catatan::updateOrCreate(
            [
                'santri_id' => $santriId,
                'kelas_semester_id' => $kelasSemesterId,
            ],
            [
                'catatan' => $request->catatan,
                'keputusan_kelas_id' => $request->keputusan_kelas_id,
                'predikat' => $predikat,
            ]
        );

        // Update kelas_id di tabel santri berdasarkan keputusan
        $santri = Santri::findOrFail($santriId);
        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);

        if ($request->filled('keputusan_kelas_id')) {
            // Jika ada keputusan naik kelas, update ke kelas tujuan
            $santri->update([
                'kelas_id' => $request->keputusan_kelas_id
            ]);
            $message = 'Rapor berhasil disimpan dan santri naik ke kelas baru.';
        } else {
            // Jika tidak naik kelas, tetap di kelas sekarang
            $santri->update([
                'kelas_id' => $kelasSemester->kelas_id
            ]);
            $message = 'Rapor berhasil disimpan. Santri tidak naik kelas.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    private function calculatePredikat($rataRata)
    {
        if ($rataRata > 90) return 'MUMTAZ';
        if ($rataRata > 80) return 'JAYYID JIDDAN';
        if ($rataRata > 70) return 'JAYYID';
        return 'MAQBUL';
    }

    public static function numberToWords($number)
    {
        $units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $tens = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];

        if ($number == 0) return 'Nol';
        if ($number < 10) return $units[$number];
        if ($number < 20) return $teens[$number - 10];
        if ($number < 100) {
            $unit = $number % 10;
            $ten = floor($number / 10);
            return $tens[$ten] . ($unit ? ' ' . $units[$unit] : '');
        }
        if ($number == 100) return 'Seratus';
        return '';
    }

    public function generatePdf($kelasSemesterId, $santriId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])->findOrFail($kelasSemesterId);
        $santri = Santri::findOrFail($santriId);
        $mapels = KelasMapelSemester::with('mataPelajaran')
            ->where('kelas_semester_id', $kelasSemesterId)
            ->get();
        $mapelsByCategory = $mapels->groupBy('mataPelajaran.kategori');
        $nilaiRaporData = NilaiRapor::where('santri_id', $santriId)
            ->whereIn('kelas_mapel_semester_id', $mapels->pluck('id'))
            ->get();
        $catatanRapor = Catatan::where('santri_id', $santriId)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->first();

        $jumlahNilai = $nilaiRaporData->sum('nilai');
        $rataRata = $nilaiRaporData->count() > 0 ? $jumlahNilai / $nilaiRaporData->count() : null;
        $predikat = $rataRata ? $this->calculatePredikat($rataRata) : null;
        $terbilang = $rataRata ? $this->numberToWords(round($rataRata)) : null;

        // Tambahkan terbilang untuk setiap nilai
        $nilaiRaporDataWithTerbilang = $nilaiRaporData->map(function ($item) {
            $item->terbilang = $this->numberToWords($item->nilai);
            return $item;
        });
        $jumlahNilaiTerbilang = $jumlahNilai ? $this->numberToWords($jumlahNilai) : null;

        return view('admin.rapor-template', compact(
            'kelasSemester',
            'santri',
            'mapelsByCategory',
            'nilaiRaporDataWithTerbilang',
            'catatanRapor',
            'jumlahNilai',
            'rataRata',
            'predikat',
            'terbilang',
            'jumlahNilaiTerbilang'
        ));
    }

    public function previewRapor($kelasSemesterId, $santriId)
    {
        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])->findOrFail($kelasSemesterId);
        $santri = Santri::findOrFail($santriId);
        $mapels = KelasMapelSemester::with('mataPelajaran')
            ->where('kelas_semester_id', $kelasSemesterId)
            ->get();
        $mapelsByCategory = $mapels->groupBy('mataPelajaran.kategori');
        $nilaiRaporData = NilaiRapor::where('santri_id', $santriId)
            ->whereIn('kelas_mapel_semester_id', $mapels->pluck('id'))
            ->get();
        $catatanRapor = Catatan::where('santri_id', $santriId)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->first();

        $jumlahNilai = $nilaiRaporData->sum('nilai');
        $rataRata = $nilaiRaporData->count() > 0 ? $jumlahNilai / $nilaiRaporData->count() : null;
        $predikat = $rataRata ? $this->calculatePredikat($rataRata) : null;
        $terbilang = $rataRata ? $this->numberToWords(round($rataRata)) : null;

        // Tambahkan terbilang untuk setiap nilai
        $nilaiRaporDataWithTerbilang = $nilaiRaporData->map(function ($item) {
            $item->terbilang = $this->numberToWords($item->nilai);
            return $item;
        });
        $jumlahNilaiTerbilang = $jumlahNilai ? $this->numberToWords($jumlahNilai) : null;

        return view('admin.rapor-template', compact(
            'kelasSemester',
            'santri',
            'mapelsByCategory',
            'nilaiRaporDataWithTerbilang',
            'catatanRapor',
            'jumlahNilai',
            'rataRata',
            'predikat',
            'terbilang',
            'jumlahNilaiTerbilang'
        ));
    }
}
