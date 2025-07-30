<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
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

        // Query dasar dengan eager loading
        $query = Santri::with('kelasRelation');

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhere('nis', 'like', '%' . $search . '%')
                ->orWhere('nama_panggilan', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kelas_id
        if ($kelas) {
            $query->where('kelas_id', $kelas);
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

        // Ambil data kelas untuk dropdown
        $kelasList = Kelas::select('id', 'nama_kelas')->get();

        // Tentukan view berdasarkan route
        $view = $request->route()->getName() === 'pengajar.santri.index'
                ? 'pengajar.data-santri-pengajar'
                : 'admin.data-santri-admin';

        // Jika request adalah AJAX, return partial view atau JSON
        if ($request->ajax()) {
            return view($view, compact('santri', 'kelasList'));
        }

        return view($view, compact('santri', 'kelasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data dengan perbaikan untuk PDF
        $validator = Validator::make($request->all(), [
            // Identitas Santri
            'nama_lengkap' => 'required|max:100',
            'nama_panggilan' => 'nullable|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|max:50',
            'tanggal_lahir' => 'required|date',
            'tahun_bergabung' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'hobi' => 'nullable|max:100',
            'riwayat_penyakit' => 'nullable|max:255',
            'alamat' => 'required|max:255',

            // Akademik
            'sekolah' => 'nullable|max:100',
            'kelas' => 'required|max:50',
            'jilid_juz' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'kelas_id' => 'nullable|exists:kelas,id',
            'kelas_awal_id' => 'nullable|exists:kelas,id',

            // Wali
            'nama_wali' => 'nullable|max:100',
            'pekerjaan_wali' => 'nullable|max:50',
            'no_hp_wali' => 'nullable|max:20',

            // Dokumen - PERBAIKAN VALIDASI
            'pas_foto' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
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

            // Generate NIS
            $tahun = substr($request->tahun_bergabung, -2);
            $prefix = $request->jenis_kelamin === 'Perempuan' ? 'SAA' : 'SIA';
            $lastSantri = Santri::where('nis', 'like', "$prefix$tahun%")
                ->orderBy('nis', 'desc')
                ->first();
            $lastNumber = $lastSantri ? (int) substr($lastSantri->nis, -3) : 0;
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $nis = $prefix . $tahun . $newNumber;

            // Upload pas foto
            $pasFotoPath = $this->uploadFile($request->file('pas_foto'), 'pas_foto');
            if (!$pasFotoPath) {
                throw new \Exception('Gagal upload pas foto');
            }

            // Upload akta
            $aktaPath = $this->uploadFile($request->file('akta'), 'akta');
            if (!$aktaPath) {
                throw new \Exception('Gagal upload akta');
            }

            // Simpan data santri
            $santri = Santri::create([
                // Identitas Santri
                'nis' => $nis,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tahun_bergabung' => $request->tahun_bergabung,
                'umur' => $umur,
                'hobi' => $request->hobi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'alamat' => $request->alamat,

                // Akademik
                'sekolah' => $request->sekolah,
                'kelas' => $request->kelas,
                'jilid_juz' => $request->jilid_juz,
                'status' => $request->status,
                'kelas_awal_id' => $request->kelas_awal_id,
                'kelas_id' => $request->kelas_id,

                // Wali
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
        $santri = Santri::with('kelasRelation')->findOrFail($id);
        $kelasList = Kelas::select('id', 'nama_kelas')->get();

        // Tentukan view berdasarkan route
        $view = request()->route()->getName() === 'pengajar.santri.show'
                ? 'pengajar.detail-santri-pengajar'
                : 'admin.detail-santri-admin';

        return view($view, compact('santri', 'kelasList'));
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

    private function formatAktaFileName($santri)
    {
        if (!$santri->akta_path) {
            return '-';
        }

        // Bersihkan nama santri dari karakter yang tidak diinginkan
        $namaSantri = str_replace(' ', '_', $santri->nama_lengkap);
        $namaSantri = preg_replace('/[^A-Za-z0-9_]/', '', $namaSantri);

        // Ambil ekstensi file
        $extension = pathinfo($santri->akta_path, PATHINFO_EXTENSION);

        return "Akta_{$namaSantri}.{$extension}";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        // Validasi data dengan perbaikan untuk PDF
        $validator = Validator::make($request->all(), [
            // Identitas Santri
            'nama_lengkap' => 'required|max:100',
            'nama_panggilan' => 'nullable|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|max:50',
            'tanggal_lahir' => 'required|date',
            'tahun_bergabung' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'hobi' => 'nullable|max:100',
            'riwayat_penyakit' => 'nullable|max:255',
            'alamat' => 'required|max:255',

            // Akademik
            'sekolah' => 'nullable|max:100',
            'kelas' => 'required|max:50',
            'jilid_juz' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'kelas_id' => 'nullable|exists:kelas,id',
            'kelas_awal_id' => 'nullable|exists:kelas,id',

            // Wali
            'nama_wali' => 'nullable|max:100',
            'pekerjaan_wali' => 'nullable|max:50',
            'no_hp_wali' => 'nullable|max:20',

            'pas_foto' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
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
                if ($pasFotoPath && Storage::disk('public')->exists($pasFotoPath)) {
                    Storage::disk('public')->delete($pasFotoPath);
                }

                $pasFotoPath = $this->uploadFile($request->file('pas_foto'), 'pas_foto');
                if (!$pasFotoPath) {
                    throw new \Exception('Gagal upload pas foto');
                }
            } elseif ($request->has('pas_foto_existing')) {
                // Gunakan file existing jika ada
                $pasFotoPath = $request->pas_foto_existing;
            }

            $aktaPath = $santri->akta_path;
            if ($request->hasFile('akta')) {
                // Hapus file lama jika ada
                if ($aktaPath && Storage::disk('public')->exists($aktaPath)) {
                    Storage::disk('public')->delete($aktaPath);
                }

                $aktaPath = $this->uploadFile($request->file('akta'), 'akta');
                if (!$aktaPath) {
                    throw new \Exception('Gagal upload akta');
                }
            } elseif ($request->has('akta_existing')) {
                // Gunakan file existing jika ada
                $aktaPath = $request->akta_existing;
            }

            // Update data santri
            $santri->update([
                // Identitas Santri
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tahun_bergabung' => $request->tahun_bergabung,
                'umur' => $umur,
                'hobi' => $request->hobi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'alamat' => $request->alamat,

                // Akademik
                'sekolah' => $request->sekolah,
                'kelas' => $request->kelas,
                'jilid_juz' => $request->jilid_juz,
                'status' => $request->status,
                'kelas_id' => $request->kelas_id,

                // Orang Tua/Wali
                'nama_wali' => $request->nama_wali,
                'pekerjaan_wali' => $request->pekerjaan_wali,
                'no_hp_wali' => $request->no_hp_wali,

                // Dokumen - hanya update jika ada file baru
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
            if ($santri->pas_foto_path && Storage::disk('public')->exists($santri->pas_foto_path)) {
                Storage::disk('public')->delete($santri->pas_foto_path);
            }
            if ($santri->akta_path && Storage::disk('public')->exists($santri->akta_path)) {
                Storage::disk('public')->delete($santri->akta_path);
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
     * Helper function untuk upload file - DIPERBAIKI
     */
    private function uploadFile($file, $directory)
    {
        try {
            if (!$file || !$file->isValid()) {
                return null;
            }

            // Validasi file
            $maxSize = $directory === 'pas_foto' ? 2048 : 5120; // KB
            if ($file->getSize() > $maxSize * 1024) {
                return null;
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;

            // Upload file
            $path = $file->storeAs($directory, $filename, 'public');

            if ($path) {
                return $path;
            } else {
                return null;
            }

        } catch (\Exception $e) {
            return null;
        }
    }

    public function downloadAkta($id)
    {
        try {
            $santri = Santri::findOrFail($id);

            if (!$santri->akta_path) {
                return redirect()->back()->with('error', 'File akta tidak ditemukan.');
            }

            $fullPath = storage_path('app/public/' . $santri->akta_path);

            if (!file_exists($fullPath)) {
                return redirect()->back()->with('error', 'File akta tidak ditemukan di server.');
            }

            // Format nama file download
            $downloadName = $this->formatAktaFileName($santri);

            return response()->download($fullPath, $downloadName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }
}
