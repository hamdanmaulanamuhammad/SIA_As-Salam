<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengajarMapelController extends Controller
{
    public function create()
    {
        return view('pengajar.mapel.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_mapel' => 'required|string|max:255|unique:mapels,nama_mapel',
            'kategori' => 'required|in:Hafalan,Teori,Praktik',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mapel.string' => 'Nama mata pelajaran harus berupa teks.',
            'nama_mapel.max' => 'Nama mata pelajaran tidak boleh lebih dari 255 karakter.',
            'nama_mapel.unique' => 'Nama mata pelajaran ini sudah terdaftar. Silakan gunakan nama lain.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori harus salah satu dari: Hafalan, Teori, atau Praktik.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mata pelajaran. Periksa kembali data yang diisi.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Mapel::create([
                'nama_mapel' => $request->nama_mapel,
                'kategori' => $request->kategori
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mata pelajaran. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $mapel = Mapel::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $mapel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran tidak ditemukan.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_mapel' => 'required|string|max:255|unique:mapels,nama_mapel,'.$id,
            'kategori' => 'required|in:Hafalan,Teori,Praktik',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mapel.string' => 'Nama mata pelajaran harus berupa teks.',
            'nama_mapel.max' => 'Nama mata pelajaran tidak boleh lebih dari 255 karakter.',
            'nama_mapel.unique' => 'Nama mata pelajaran ini sudah terdaftar. Silakan gunakan nama lain.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori harus salah satu dari: Hafalan, Teori, atau Praktik.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mata pelajaran. Periksa kembali data yang diisi.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mapel = Mapel::findOrFail($id);
            $mapel->update([
                'nama_mapel' => $request->nama_mapel,
                'kategori' => $request->kategori
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mata pelajaran. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mapel = Mapel::findOrFail($id);
            $mapel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mata pelajaran. Silakan coba lagi nanti.'
            ], 500);
        }
    }
}
