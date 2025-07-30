<?php

namespace App\Http\Controllers;

use App\Models\InfaqTahunan;
use App\Models\InfaqSantri;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\AdministrasiBulanan;
use App\Models\BukuKas;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InfaqController extends Controller
{
    public function index(Request $request)
    {
        $infaqTahunan = InfaqTahunan::with('infaqSantris.santri')->get();
        $administrasiBulanan = AdministrasiBulanan::with('pengeluaranBulanan')->get();
        $bukuKas = BukuKas::with('transaksiKas')->get();

        // Hitung total infaq dan kekurangan untuk santri aktif
        $santriAktif = Santri::where('status', 'Aktif')->with(['infaqSantris'])->get();
        $totalInfaqWajib = $santriAktif->sum(function ($santri) {
            return $santri->infaqSantris->sum('infaq_wajib');
        });
        $totalInfaqSukarela = $santriAktif->sum(function ($santri) {
            return $santri->infaqSantris->sum('infaq_sukarela');
        });
        $totalInfaq = $totalInfaqWajib + $totalInfaqSukarela;
        $totalKekurangan = $santriAktif->count() * 12 * 10000 - $totalInfaqWajib;

        return view('admin.keuangan', compact(
            'infaqTahunan',
            'administrasiBulanan',
            'bukuKas',
            'totalInfaqWajib',
            'totalInfaqSukarela',
            'totalInfaq',
            'totalKekurangan'
        ));
    }

    // === Infaq Tahunan ===
    public function indexInfaqTahunan()
    {
        $infaqTahunan = InfaqTahunan::with('infaqSantris.santri')->get();
        return response()->json(['data' => $infaqTahunan], 200);
    }

    public function storeInfaqTahunan(Request $request)
    {
        $request->validate(['tahun' => 'required|integer|unique:infaq_tahunans,tahun']);
        $infaqTahunan = InfaqTahunan::create(['tahun' => $request->tahun]);
        return response()->json([
            'success' => true,
            'message' => 'Infaq tahunan berhasil dibuat',
            'data' => $infaqTahunan
        ], 201);
    }

    public function editInfaqTahunan($id)
    {
        $infaqTahunan = InfaqTahunan::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $infaqTahunan
        ], 200);
    }

    public function updateInfaqTahunan(Request $request, $id)
    {
        $infaqTahunan = InfaqTahunan::findOrFail($id);
        $request->validate([
            'tahun' => 'required|integer|unique:infaq_tahunans,tahun,' . $id
        ]);
        $infaqTahunan->update(['tahun' => $request->tahun]);
        return response()->json([
            'success' => true,
            'message' => 'Infaq tahunan berhasil diperbarui',
            'data' => $infaqTahunan
        ], 200);
    }

    public function destroyInfaqTahunan($id)
    {
        $infaqTahunan = InfaqTahunan::findOrFail($id);
        $infaqTahunan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Infaq tahunan berhasil dihapus'
        ], 200);
    }

    // === Infaq Santri ===
    public function showInfaqSantri(Request $request, $infaqTahunanId)
    {
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $perPage = $request->input('per_page', 10);

        $query = Santri::with(['kelasRelation', 'infaqSantris' => function ($query) use ($infaqTahunanId) {
            $query->where('infaq_tahunan_id', $infaqTahunanId);
        }])
        ->where('status', 'Aktif'); // Filter hanya santri aktif

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhere('nis', 'like', '%' . $search . '%')
                ->orWhere('nama_panggilan', 'like', '%' . $search . '%');
            });
        }

        if ($kelas) {
            $query->where('kelas_id', $kelas);
        }

        $query->orderBy('created_at', 'desc');
        $santri = $query->paginate($perPage);

        $santri->appends([
            'search' => $search,
            'kelas' => $kelas,
            'per_page' => $perPage
        ]);

        $kelasList = Kelas::select('id', 'nama_kelas')->get();
        $infaqTahunan = InfaqTahunan::findOrFail($infaqTahunanId);

        // HITUNG TOTAL KESELURUHAN (TANPA FILTER) - INI YANG DIPERBAIKI
        $totalKeseluruhanQuery = DB::table('santri')
            ->leftJoin('infaq_santris', function($join) use ($infaqTahunanId) {
                $join->on('santri.id', '=', 'infaq_santris.santri_id')
                    ->where('infaq_santris.infaq_tahunan_id', '=', $infaqTahunanId);
            })
            ->where('santri.status', 'Aktif'); // HANYA filter status aktif, TIDAK ada filter lain

        // Hitung total keseluruhan tanpa filter search dan kelas
        $totalsKeseluruhan = $totalKeseluruhanQuery->select([
            DB::raw('COALESCE(SUM(infaq_santris.infaq_wajib), 0) as total_infaq_wajib'),
            DB::raw('COALESCE(SUM(infaq_santris.infaq_sukarela), 0) as total_infaq_sukarela'),
            DB::raw('COUNT(DISTINCT santri.id) as total_santri')
        ])->first();

        $totalInfaqWajib = $totalsKeseluruhan->total_infaq_wajib;
        $totalInfaqSukarela = $totalsKeseluruhan->total_infaq_sukarela;
        $totalInfaq = $totalInfaqWajib + $totalInfaqSukarela;
        $totalKekurangan = $totalsKeseluruhan->total_santri * 12 * 10000 - $totalInfaqWajib;

        if ($request->ajax()) {
            return view('admin.detail-infaq', compact(
                'santri',
                'kelasList',
                'infaqTahunan',
                'totalInfaqWajib',
                'totalInfaqSukarela',
                'totalInfaq',
                'totalKekurangan'
            ));
        }

        return view('admin.detail-infaq', compact(
            'santri',
            'kelasList',
            'infaqTahunan',
            'totalInfaqWajib',
            'totalInfaqSukarela',
            'totalInfaq',
            'totalKekurangan'
        ));
    }

    public function storeInfaqSantri(Request $request, $infaqTahunanId)
    {
        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
            'bulan' => 'required|array|min:1',
            'bulan.*' => 'integer|between:1,12',
            'infaq_wajib' => 'nullable|integer|min:10000',
            'infaq_sukarela' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek duplikasi bulan secara manual
        $existingMonths = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)
            ->where('santri_id', $request->santri_id)
            ->whereIn('bulan', $request->bulan)
            ->pluck('bulan')
            ->toArray();

        if (!empty($existingMonths)) {
            $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $existingMonthNames = array_map(function ($month) use ($monthNames) {
                return $monthNames[$month - 1];
            }, $existingMonths);
            return response()->json([
                'success' => false,
                'message' => 'Bulan berikut sudah terdaftar: ' . implode(', ', $existingMonthNames)
            ], 422);
        }

        // Simpan data infaq untuk setiap bulan
        $createdRecords = [];
        foreach ($request->bulan as $bulan) {
            $infaqSantri = InfaqSantri::create([
                'infaq_tahunan_id' => $infaqTahunanId,
                'santri_id' => $request->santri_id,
                'bulan' => $bulan,
                'infaq_wajib' => $request->infaq_wajib ?? 10000,
                'infaq_sukarela' => $request->infaq_sukarela ?? 0,
            ]);
            $createdRecords[] = $infaqSantri;
        }

        return response()->json([
            'success' => true,
            'message' => 'Infaq santri berhasil dicatat untuk ' . count($createdRecords) . ' bulan',
            'data' => $createdRecords
        ], 201);
    }

    public function editInfaqSantri($infaqTahunanId, $id)
    {
        // Jika $id dalam format "santri_id-bulan"
        if (strpos($id, '-') !== false) {
            [$santriId, $bulan] = explode('-', $id);
            $infaqSantri = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)
                ->where('santri_id', $santriId)
                ->where('bulan', $bulan)
                ->firstOrFail();
        } else {
            $infaqSantri = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)->findOrFail($id);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $infaqSantri->id,
                'santri_id' => $infaqSantri->santri_id,
                'bulan' => $infaqSantri->bulan,
                'infaq_wajib' => $infaqSantri->infaq_wajib,
                'infaq_sukarela' => $infaqSantri->infaq_sukarela,
            ]
        ], 200);
    }

    public function updateInfaqSantri(Request $request, $infaqTahunanId, $id)
    {
        $infaqSantri = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
            'bulan' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('infaq_santris')->where(function ($query) use ($request, $infaqTahunanId, $id) {
                    return $query->where('infaq_tahunan_id', $infaqTahunanId)
                                 ->where('santri_id', $request->santri_id)
                                 ->where('bulan', $request->bulan)
                                 ->where('id', '!=', $id);
                })
            ],
            'infaq_wajib' => 'nullable|integer|min:10000',
            'infaq_sukarela' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $infaqSantri->update([
            'santri_id' => $request->santri_id,
            'bulan' => $request->bulan,
            'infaq_wajib' => $request->infaq_wajib ?? 10000,
            'infaq_sukarela' => $request->infaq_sukarela ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Infaq santri berhasil diperbarui',
            'data' => $infaqSantri
        ], 200);
    }

    public function destroyInfaqSantri($infaqTahunanId, $id)
    {
        if (strpos($id, '-') !== false) {
            [$santriId, $bulan] = explode('-', $id);
            $infaqSantri = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)
                ->where('santri_id', $santriId)
                ->where('bulan', $bulan)
                ->firstOrFail();
        } else {
            $infaqSantri = InfaqSantri::where('infaq_tahunan_id', $infaqTahunanId)->findOrFail($id);
        }

        $infaqSantri->delete();
        return response()->json([
            'success' => true,
            'message' => 'Infaq santri berhasil dihapus'
        ], 200);
    }
}
