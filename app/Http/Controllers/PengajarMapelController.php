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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        Mapel::create([
            'nama_mapel' => $request->nama_mapel,
            'kategori' => $request->kategori
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran berhasil ditambahkan'
        ]);
    }

    public function edit($id)
    {
        $mapel = Mapel::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $mapel
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_mapel' => 'required|string|max:255|unique:mapels,nama_mapel,'.$id,
            'kategori' => 'required|in:Hafalan,Teori,Praktik',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $mapel = Mapel::findOrFail($id);
        $mapel->update([
            'nama_mapel' => $request->nama_mapel,
            'kategori' => $request->kategori
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran berhasil dihapus'
        ]);
    }
}
