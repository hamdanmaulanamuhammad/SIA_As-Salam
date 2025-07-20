<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuKasController extends Controller
{
    public function index()
    {
        $bukuKas = BukuKas::with('transaksiKas')->paginate(10);
        return response()->json(['success' => true, 'data' => $bukuKas], 200);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun' => 'required|integer|unique:buku_kas_tahunans,tahun'
            ], [
                'tahun.required' => 'Tahun harus diisi',
                'tahun.integer' => 'Tahun harus berupa angka',
                'tahun.unique' => 'Tahun sudah ada'
            ]);

            $bukuKas = BukuKas::create([
                'tahun' => $request->tahun
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Buku kas berhasil dibuat',
                'data' => $bukuKas
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $bukuKas = BukuKas::findOrFail($id);
            return response()->json(['success' => true, 'data' => $bukuKas], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $bukuKas = BukuKas::findOrFail($id);

            $request->validate([
                'tahun' => 'required|integer|unique:buku_kas_tahunans,tahun,' . $id
            ], [
                'tahun.required' => 'Tahun harus diisi',
                'tahun.integer' => 'Tahun harus berupa angka',
                'tahun.unique' => 'Tahun sudah ada'
            ]);

            $bukuKas->update(['tahun' => $request->tahun]);

            return response()->json([
                'success' => true,
                'message' => 'Buku kas berhasil diperbarui',
                'data' => $bukuKas
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bukuKas = BukuKas::findOrFail($id);

            if ($bukuKas->transaksiKas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus buku kas yang memiliki transaksi'
                ], 400);
            }

            $bukuKas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Buku kas berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    public function indexTransaksiKas($bukuKasId)
    {
        try {
            $bukuKas = BukuKas::findOrFail($bukuKasId);
            $transaksiKas = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->paginate(10);
            $totalDebet = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->where('jenis', 'debet')->sum('jumlah');
            $totalKredit = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->where('jenis', 'kredit')->sum('jumlah');

            return view('admin.detail-kas', [
                'bukuKas' => $bukuKas,
                'transaksiKas' => $transaksiKas,
                'totalDebet' => $totalDebet,
                'totalKredit' => $totalKredit
            ]);
        } catch (\Exception $e) {
            return redirect()->route('keuangan.buku-kas.index')->with('error', 'Buku kas tidak ditemukan.');
        }
    }

    public function storeTransaksiKas(Request $request, $bukuKasId)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'jumlah' => 'required|integer|min:0',
                'jenis' => 'required|in:debet,kredit',
                'sumber' => 'nullable|string|max:255',
                'tujuan' => 'nullable|string|max:255',
                'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
            ], [
                'tanggal.required' => 'Tanggal harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
                'jumlah.required' => 'Jumlah harus diisi',
                'jumlah.integer' => 'Jumlah harus berupa angka',
                'jumlah.min' => 'Jumlah tidak boleh negatif',
                'jenis.required' => 'Jenis transaksi harus dipilih',
                'bukti.mimes' => 'Bukti harus berupa file JPG, JPEG, PNG, atau PDF',
                'bukti.max' => 'Ukuran file bukti maksimal 2MB'
            ]);

            $data = [
                'buku_kas_tahunan_id' => $bukuKasId,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'jumlah' => $request->jumlah,
                'jenis' => $request->jenis,
                'sumber' => $request->sumber,
                'tujuan' => $request->tujuan,
            ];

            if ($request->hasFile('bukti')) {
                // Store the file in public/bukti-kas
                $data['bukti'] = $request->file('bukti')->store('bukti-kas', 'public');
            }

            $transaksiKas = TransaksiKas::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi kas berhasil dicatat',
                'data' => $transaksiKas
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editTransaksiKas($bukuKasId, $id)
    {
        try {
            $transaksiKas = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->findOrFail($id);
            return response()->json(['success' => true, 'data' => $transaksiKas], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi kas tidak ditemukan'
            ], 404);
        }
    }

    public function updateTransaksiKas(Request $request, $bukuKasId, $id)
    {
        try {
            $transaksiKas = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->findOrFail($id);

            $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'jumlah' => 'required|integer|min:0',
                'jenis' => 'required|in:debet,kredit',
                'sumber' => 'nullable|string|max:255',
                'tujuan' => 'nullable|string|max:255',
                'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
            ], [
                'tanggal.required' => 'Tanggal harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
                'jumlah.required' => 'Jumlah harus diisi',
                'jumlah.integer' => 'Jumlah harus berupa angka',
                'jumlah.min' => 'Jumlah tidak boleh negatif',
                'jenis.required' => 'Jenis transaksi harus dipilih',
                'bukti.mimes' => 'Bukti harus berupa file JPG, JPEG, PNG, atau PDF',
                'bukti.max' => 'Ukuran file bukti maksimal 2MB'
            ]);

            $data = $request->only(['tanggal', 'keterangan', 'jumlah', 'jenis', 'sumber', 'tujuan']);

            if ($request->hasFile('bukti')) {
                // Delete old file if exists
                if ($transaksiKas->bukti) {
                    Storage::disk('public')->delete($transaksiKas->bukti);
                }
                // Store the new file in public/bukti-kas
                $data['bukti'] = $request->file('bukti')->store('bukti-kas', 'public');
            }

            $transaksiKas->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi kas berhasil diperbarui',
                'data' => $transaksiKas
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyTransaksiKas($bukuKasId, $id)
    {
        try {
            $transaksiKas = TransaksiKas::where('buku_kas_tahunan_id', $bukuKasId)->findOrFail($id);
            if ($transaksiKas->bukti) {
                // Delete the file from public/bukti-kas
                Storage::disk('public')->delete($transaksiKas->bukti);
            }
            $transaksiKas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi kas berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus transaksi kas'
            ], 500);
        }
    }
}
