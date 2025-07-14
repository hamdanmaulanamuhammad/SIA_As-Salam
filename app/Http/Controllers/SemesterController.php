<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::paginate(10);
        return view('admin-akademik', compact('semesters'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_semester' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        Semester::create([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil ditambahkan'
        ]);
    }

    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $semester
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_semester' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $semester = Semester::findOrFail($id);
        $semester->update([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil dihapus'
        ]);
    }

    public function kelasSemester($id)
    {
        // This would need to be implemented based on your kelas-semester view requirements
        return view('admin.kelas-semester', compact('id'));
    }
}
