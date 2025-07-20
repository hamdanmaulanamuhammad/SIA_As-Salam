<?php

namespace App\Http\Controllers;

use App\Models\AdministrasiBulanan;
use App\Models\PengeluaranBulanan;
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
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
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

        // Validasi yang benar - bulan sebagai string, bukan integer
        $request->validate([
            'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
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

        $administrasi->update($request->only(['bulan', 'tahun']));

        return response()->json([
            'success' => true,
            'message' => 'Administrasi bulanan berhasil diperbarui',
            'data' => $administrasi
        ], 200);
    }

    public function destroy($id)
    {
        $administrasi = AdministrasiBulanan::findOrFail($id);

        // Cek apakah ada pengeluaran yang terkait
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
        $administrasi = AdministrasiBulanan::findOrFail($administrasiBulananId);
        $pengeluaran = PengeluaranBulanan::where('administrasi_bulanan_id', $administrasiBulananId)->paginate(10);
        return view('pengeluaran_bulanan', ['administrasi' => $administrasi, 'pengeluaran' => $pengeluaran]);
    }

    public function storePengeluaranBulanan(Request $request, $administrasiBulananId)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
        ]);

        $pengeluaran = PengeluaranBulanan::create([
            'administrasi_bulanan_id' => $administrasiBulananId,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
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
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
        ]);

        $pengeluaran->update($request->only(['keterangan', 'jumlah']));
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
