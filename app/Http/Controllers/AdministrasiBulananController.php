<?php

namespace App\Http\Controllers;

use App\Models\AdditionalMukafaah;
use App\Models\AdministrasiBulanan;
use App\Models\PengeluaranBulanan;
use App\Models\Presence;
use App\Models\Recap;
use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class AdministrasiBulananController extends Controller
{
    public function index()
    {
        $administrasiBulanan = AdministrasiBulanan::with('pengeluaranBulanan')->paginate(10);
        return response()->json(['success' => true, 'data' => $administrasiBulanan], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        // Cek apakah kombinasi bulan dan tahun sudah ada
        $exists = AdministrasiBulanan::where('bulan', $request->bulan)
                                   ->where('tahun', $request->tahun)
                                   ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data administrasi untuk bulan dan tahun tersebut sudah ada.'
            ], 422);
        }

        $administrasi = AdministrasiBulanan::create([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'bank_account_id' => $request->bank_account_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Administrasi bulanan berhasil dibuat',
            'data' => $administrasi
        ], 201);
    }

    public function edit($id)
    {
        $administrasi = AdministrasiBulanan::findOrFail($id);
        return response()->json(['success' => true, 'data' => $administrasi], 200);
    }

    public function update(Request $request, $id)
    {
        $administrasi = AdministrasiBulanan::findOrFail($id);

        $request->validate([
            'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        // Cek apakah kombinasi bulan dan tahun sudah ada (kecuali untuk record yang sedang diupdate)
        $exists = AdministrasiBulanan::where('bulan', $request->bulan)
                                   ->where('tahun', $request->tahun)
                                   ->where('id', '!=', $id)
                                   ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data administrasi untuk bulan dan tahun tersebut sudah ada.'
            ], 422);
        }

        $administrasi->update($request->only(['bulan', 'tahun', 'bank_account_id']));

        return response()->json([
            'success' => true,
            'message' => 'Administrasi bulanan berhasil diperbarui',
            'data' => $administrasi
        ], 200);
    }

    public function destroy($id)
    {
        $administrasi = AdministrasiBulanan::findOrFail($id);

        if ($administrasi->pengeluaranBulanan()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus administrasi yang masih memiliki data pengeluaran.'
            ], 422);
        }

        $administrasi->delete();
        return response()->json([
            'success' => true,
            'message' => 'Administrasi bulanan berhasil dihapus'
        ], 200);
    }

    public function indexPengeluaranBulanan($administrasiBulananId)
    {
        $administrasi = AdministrasiBulanan::with('bankAccount')->findOrFail($administrasiBulananId);
        $pengeluaran = PengeluaranBulanan::where('administrasi_bulanan_id', $administrasiBulananId)
            ->with('bankAccount')
            ->paginate(10);

        // Konversi bulan ke format numerik untuk pencocokan periode
        $bulanMap = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        ];
        $periode = $administrasi->tahun . '-' . $bulanMap[$administrasi->bulan];

        // Ambil data Recap dan terkait
        $recap = Recap::where('periode', $periode)->first();
        $pengajars = User::where('role', 'pengajar')->where('accepted', '1')->get();
        $dates = $recap ? json_decode($recap->dates, true) : [];
        $presences = collect();
        $additionalMukafaahs = collect();

        if ($recap) {
            $presences = Presence::whereIn('date', $dates)
                ->whereIn('user_id', $pengajars->pluck('id'))
                ->get()
                ->groupBy('user_id');
            $additionalMukafaahs = AdditionalMukafaah::where('recap_id', $recap->id)->get();
        }

        return view('admin.detail-administrasi-bulanan', [
            'administrasi' => $administrasi,
            'pengeluaran' => $pengeluaran,
            'recap' => $recap,
            'pengajars' => $pengajars,
            'dates' => $dates,
            'presences' => $presences,
            'additionalMukafaahs' => $additionalMukafaahs
        ]);
    }

    public function storePengeluaranBulanan(Request $request, $administrasiBulananId)
    {
        $request->validate([
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pengeluaran = PengeluaranBulanan::create([
            'administrasi_bulanan_id' => $administrasiBulananId,
            'nama_pengeluaran' => $request->nama_pengeluaran,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'bank_account_id' => AdministrasiBulanan::findOrFail($administrasiBulananId)->bank_account_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran bulanan berhasil dicatat',
            'data' => $pengeluaran
        ], 201);
    }

    public function editPengeluaranBulanan($administrasiBulananId, $id)
    {
        $pengeluaran = PengeluaranBulanan::where('administrasi_bulanan_id', $administrasiBulananId)->findOrFail($id);
        return response()->json(['success' => true, 'data' => $pengeluaran], 200);
    }

    public function updatePengeluaranBulanan(Request $request, $administrasiBulananId, $id)
    {
        $pengeluaran = PengeluaranBulanan::where('administrasi_bulanan_id', $administrasiBulananId)->findOrFail($id);

        $request->validate([
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pengeluaran->update([
            'nama_pengeluaran' => $request->nama_pengeluaran,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'bank_account_id' => AdministrasiBulanan::findOrFail($administrasiBulananId)->bank_account_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran bulanan berhasil diperbarui',
            'data' => $pengeluaran
        ], 200);
    }

    public function destroyPengeluaranBulanan($administrasiBulananId, $id)
    {
        $pengeluaran = PengeluaranBulanan::where('administrasi_bulanan_id', $administrasiBulananId)->findOrFail($id);
        $pengeluaran->delete();
        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran bulanan berhasil dihapus'
        ], 200);
    }
}
