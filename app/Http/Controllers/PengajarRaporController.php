<?php

namespace App\Http\Controllers;

use App\Models\KelasSemester;
use App\Models\SantriKelasSemester;
use App\Models\Santri;
use App\Models\KelasMapelSemester;
use App\Models\NilaiRapor;
use App\Models\Catatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PengajarRaporController extends Controller
{
    public function index($kelasSemesterId)
    {
        Log::info('=== PengajarRaporController::index dipanggil ===', ['kelas_semester_id' => $kelasSemesterId]);

        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])
            ->findOrFail($kelasSemesterId);

        // Validasi bahwa pengajar adalah wali kelas
        if ($kelasSemester->wali_kelas_id !== Auth::id()) {
            Log::warning('Akses ditolak: Pengajar bukan wali kelas', [
                'user_id' => Auth::id(),
                'wali_kelas_id' => $kelasSemester->wali_kelas_id
            ]);
            return redirect()->route('pengajar.kelas-semester', $kelasSemester->semester_id)
                ->with('error', 'Anda hanya dapat mengakses rapor untuk kelas yang Anda wali.');
        }

        $santriList = SantriKelasSemester::with('santri')
            ->where('kelas_semester_id', $kelasSemesterId)
            ->paginate(10);

        return view('pengajar.list-santri-rapor', compact('kelasSemester', 'santriList'));
    }

    public function show($kelasSemesterId, $santriId)
    {
        Log::info('=== PengajarRaporController::show dipanggil ===', [
            'kelas_semester_id' => $kelasSemesterId,
            'santri_id' => $santriId
        ]);

        $kelasSemester = KelasSemester::with(['kelas', 'semester'])
            ->findOrFail($kelasSemesterId);

        // Validasi bahwa pengajar adalah wali kelas
        if ($kelasSemester->wali_kelas_id !== Auth::id()) {
            Log::warning('Akses ditolak: Pengajar bukan wali kelas', [
                'user_id' => Auth::id(),
                'wali_kelas_id' => $kelasSemester->wali_kelas_id
            ]);
            return redirect()->route('pengajar.kelas-semester', $kelasSemester->semester_id)
                ->with('error', 'Anda hanya dapat mengakses rapor untuk kelas yang Anda wali.');
        }

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

        return view('pengajar.input-rapor', compact(
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
        Log::info('=== PengajarRaporController::update dipanggil ===', [
            'kelas_semester_id' => $kelasSemesterId,
            'santri_id' => $santriId,
            'data' => $request->all()
        ]);

        $kelasSemester = KelasSemester::findOrFail($kelasSemesterId);

        // Validasi bahwa pengajar adalah wali kelas
        if ($kelasSemester->wali_kelas_id !== Auth::id()) {
            Log::warning('Akses ditolak: Pengajar bukan wali kelas', [
                'user_id' => Auth::id(),
                'wali_kelas_id' => $kelasSemester->wali_kelas_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat mengedit rapor untuk kelas yang Anda wali.'
            ], 403);
        }

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
        if ($request->filled('keputusan_kelas_id')) {
            $santri->update(['kelas_id' => $request->keputusan_kelas_id]);
            $message = 'Rapor berhasil disimpan dan santri naik ke kelas baru.';
        } else {
            $santri->update(['kelas_id' => $kelasSemester->kelas_id]);
            $message = 'Rapor berhasil disimpan. Santri tidak naik kelas.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function previewRapor($kelasSemesterId, $santriId)
    {
        Log::info('=== PengajarRaporController::previewRapor dipanggil ===', [
            'kelas_semester_id' => $kelasSemesterId,
            'santri_id' => $santriId
        ]);

        $kelasSemester = KelasSemester::with(['kelas', 'semester', 'waliKelas', 'mudir'])
            ->findOrFail($kelasSemesterId);

        // Validasi bahwa pengajar adalah wali kelas
        if ($kelasSemester->wali_kelas_id !== Auth::id()) {
            Log::warning('Akses ditolak: Pengajar bukan wali kelas', [
                'user_id' => Auth::id(),
                'wali_kelas_id' => $kelasSemester->wali_kelas_id
            ]);
            return redirect()->route('pengajar.kelas-semester', $kelasSemester->semester_id)
                ->with('error', 'Anda hanya dapat mengakses rapor untuk kelas yang Anda wali.');
        }

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

        $nilaiRaporDataWithTerbilang = $nilaiRaporData->map(function ($item) {
            $item->terbilang = $this->numberToWords($item->nilai);
            return $item;
        });
        $jumlahNilaiTerbilang = $jumlahNilai ? $this->numberToWords($jumlahNilai) : null;

        return view('pengajar.rapor-template', compact(
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

        // Hitung jumlah nilai
        $jumlahNilai = $nilaiRaporData->sum('nilai');
        $jumlahNilaiTerbilang = $jumlahNilai ? $this->numberToWords($jumlahNilai) : '-';
        $rataRata = $nilaiRaporData->count() > 0 ? $jumlahNilai / $nilaiRaporData->count() : null;
        $predikat = $rataRata ? $this->calculatePredikat($rataRata) : null;
        $terbilang = $rataRata ? $this->numberToWords(round($rataRata)) : null;

        // Tambahkan terbilang untuk setiap nilai
        $nilaiRaporDataWithTerbilang = $nilaiRaporData->map(function ($item) {
            $item->terbilang = $this->numberToWords($item->nilai);
            return $item;
        });

        $catatanRapor = Catatan::where('santri_id', $santriId)
            ->where('kelas_semester_id', $kelasSemesterId)
            ->first();

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
        $thousands = ['', 'Ribu', 'Juta', 'M encourage', 'Triliun'];

        if ($number == 0) return 'Nol';
        if ($number < 0) return 'Minus ' . self::numberToWords(abs($number));

        if ($number < 10) return $units[$number];
        if ($number < 20) return $teens[$number - 10];
        if ($number < 100) {
            $unit = $number % 10;
            $ten = floor($number / 10);
            return $tens[$ten] . ($unit ? ' ' . $units[$unit] : '');
        }
        if ($number == 100) return 'Seratus';
        if ($number < 1000) {
            $hundreds = floor($number / 100);
            $remainder = $number % 100;
            return $units[$hundreds] . ' Ratus' . ($remainder ? ' ' . self::numberToWords($remainder) : '');
        }
        if ($number < 1000000) {
            $thousands_count = floor($number / 1000);
            $remainder = $number % 1000;
            return self::numberToWords($thousands_count) . ' Ribu' . ($remainder ? ' ' . self::numberToWords($remainder) : '');
        }

        return 'Angka terlalu besar'; // Tambahkan batasan untuk angka yang sangat besar
    }
}
