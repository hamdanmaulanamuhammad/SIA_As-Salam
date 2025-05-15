<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan filter
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);

        // Query dasar
        $query = Santri::query();

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kelas
        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        // Filter berdasarkan status
        if ($status) {
            $query->where('status', $status);
        }

        // Urutkan berdasarkan yang terbaru
        $query->orderBy('created_at', 'desc');

        // Paginasi
        $santri = $query->paginate($perPage);

        // Tambahkan parameter query ke pagination links
        $santri->appends([
            'search' => $search,
            'kelas' => $kelas,
            'status' => $status,
            'per_page' => $perPage
        ]);

        return view('admin.data-santri-admin', compact('santri'));
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            // Identitas Santri
            'nis' => 'required|unique:santri|max:20',
            'nama_lengkap' => 'required|max:100',
            'nama_panggilan' => 'nullable|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|max:50',
            'tanggal_lahir' => 'required|date',
            'hobi' => 'nullable|max:100',
            'riwayat_penyakit' => 'nullable|max:255',
            'alamat' => 'required|max:255',

            // Akademik
            'sekolah' => 'nullable|max:100',
            'kelas' => 'required|max:50',
            'jilid_juz' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Tidak Aktif',

            // Orang Tua/Wali
            'nama_ayah' => 'required|max:100',
            'nama_ibu' => 'required|max:100',
            'pekerjaan_ayah' => 'nullable|max:50',
            'pekerjaan_ibu' => 'nullable|max:50',
            'no_hp_ayah' => 'nullable|max:20',
            'no_hp_ibu' => 'nullable|max:20',
            'nama_wali' => 'nullable|max:100',
            'pekerjaan_wali' => 'nullable|max:50',
            'no_hp_wali' => 'nullable|max:20',

            // Dokumen
            'pas_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'akta' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Hitung umur
            $tanggalLahir = new \DateTime($request->tanggal_lahir);
            $today = new \DateTime();
            $umur = $today->diff($tanggalLahir)->y;

            // Upload pas foto
            $pasFotoPath = $this->uploadFile($request->file('pas_foto'), 'pas_foto');

            // Upload akta
            $aktaPath = $this->uploadFile($request->file('akta'), 'akta');

            // Simpan data santri
            $santri = Santri::create([
                // Identitas Santri
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'umur' => $umur,
                'hobi' => $request->hobi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'alamat' => $request->alamat,

                // Akademik
                'sekolah' => $request->sekolah,
                'kelas' => $request->kelas,
                'jilid_juz' => $request->jilid_juz,
                'status' => $request->status,

                // Orang Tua/Wali
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'no_hp_ayah' => $request->no_hp_ayah,
                'no_hp_ibu' => $request->no_hp_ibu,
                'nama_wali' => $request->nama_wali,
                'pekerjaan_wali' => $request->pekerjaan_wali,
                'no_hp_wali' => $request->no_hp_wali,

                // Dokumen
                'pas_foto_path' => $pasFotoPath,
                'akta_path' => $aktaPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil disimpan',
                'data' => $santri
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $santri = Santri::findOrFail($id);
        return view('admin.detail-santri-admin', compact('santri'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $santri = Santri::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $santri
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        // Validasi data
        $validator = Validator::make($request->all(), [
            // Identitas Santri
            'nis' => 'required|max:20|unique:santri,nis,' . $id,
            'nama_lengkap' => 'required|max:100',
            'nama_panggilan' => 'nullable|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|max:50',
            'tanggal_lahir' => 'required|date',
            'hobi' => 'nullable|max:100',
            'riwayat_penyakit' => 'nullable|max:255',
            'alamat' => 'required|max:255',

            // Akademik
            'sekolah' => 'nullable|max:100',
            'kelas' => 'required|max:50',
            'jilid_juz' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Tidak Aktif',

            // Orang Tua/Wali
            'nama_ayah' => 'required|max:100',
            'nama_ibu' => 'required|max:100',
            'pekerjaan_ayah' => 'nullable|max:50',
            'pekerjaan_ibu' => 'nullable|max:50',
            'no_hp_ayah' => 'nullable|max:20',
            'no_hp_ibu' => 'nullable|max:20',
            'nama_wali' => 'nullable|max:100',
            'pekerjaan_wali' => 'nullable|max:50',
            'no_hp_wali' => 'nullable|max:20',

            // Dokumen
            'pas_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'akta' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Hitung umur jika tanggal lahir berubah
            if ($request->tanggal_lahir != $santri->tanggal_lahir) {
                $tanggalLahir = new \DateTime($request->tanggal_lahir);
                $today = new \DateTime();
                $umur = $today->diff($tanggalLahir)->y;
            } else {
                $umur = $santri->umur;
            }

            // Handle file uploads
            $pasFotoPath = $santri->pas_foto_path;
            if ($request->hasFile('pas_foto')) {
                // Hapus file lama jika ada
                if ($pasFotoPath) {
                    Storage::delete($pasFotoPath);
                }
                $pasFotoPath = $this->uploadFile($request->file('pas_foto'), 'pas_foto');
            }

            $aktaPath = $santri->akta_path;
            if ($request->hasFile('akta')) {
                // Hapus file lama jika ada
                if ($aktaPath) {
                    Storage::delete($aktaPath);
                }
                $aktaPath = $this->uploadFile($request->file('akta'), 'akta');
            }

            // Update data santri
            $santri->update([
                // Identitas Santri
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'umur' => $umur,
                'hobi' => $request->hobi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'alamat' => $request->alamat,

                // Akademik
                'sekolah' => $request->sekolah,
                'kelas' => $request->kelas,
                'jilid_juz' => $request->jilid_juz,
                'status' => $request->status,

                // Orang Tua/Wali
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'no_hp_ayah' => $request->no_hp_ayah,
                'no_hp_ibu' => $request->no_hp_ibu,
                'nama_wali' => $request->nama_wali,
                'pekerjaan_wali' => $request->pekerjaan_wali,
                'no_hp_wali' => $request->no_hp_wali,

                // Dokumen
                'pas_foto_path' => $pasFotoPath,
                'akta_path' => $aktaPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil diperbarui',
                'data' => $santri
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $santri = Santri::findOrFail($id);

            // Hapus file terkait
            if ($santri->pas_foto_path) {
                Storage::delete($santri->pas_foto_path);
            }
            if ($santri->akta_path) {
                Storage::delete($santri->akta_path);
            }

            $santri->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function untuk upload file
     */
    private function uploadFile($file, $type)
    {
        if (!$file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension();
        $filename = $type . '_' . Str::random(20) . '.' . $extension;
        $path = $file->storeAs('santri_documents/' . $type, $filename, 'public');

        return $path;
    }

    public function downloadAkta($id)
    {
        $santri = Santri::findOrFail($id);
        if ($santri->akta_path) {
            return Storage::download($santri->akta_path, basename($santri->akta_path));
        }
        return redirect()->back()->with('error', 'File akta tidak ditemukan.');
    }
}
