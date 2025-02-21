<?php

namespace App\Http\Controllers;

use App\Models\Recap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecapController extends Controller
{
    /**
     * Menampilkan daftar rekap data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil semua data rekap dari database
        $recaps = Recap::all();
        return view('admin.data-recap-admin', compact('recaps'));
    }

    /**
     * Menampilkan form untuk membuat data rekap baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.recap.create');
    }

    /**
     * Menyimpan data rekap baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_rekap' => 'required|string|max:255',
            'periode' => 'required|date',
            'batas_keterlambatan' => 'required|date_format:H:i',
            'mukafaah' => 'required|numeric',
            'bonus' => 'required|numeric',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan data rekap baru ke database
        Recap::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('data-recap-admin')->with('success', 'Rekap data berhasil disimpan!');
    }

    /**
     * Menampilkan detail data rekap.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        // Ambil data rekap berdasarkan ID
        $recap = Recap::findOrFail($id);
        return view('admin.recap.show', compact('recap'));
    }

    /**
     * Menampilkan form untuk mengedit data rekap.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        // Ambil data rekap berdasarkan ID
        $recap = Recap::findOrFail($id);
        return view('admin.recap.edit', compact('recap'));
    }

    /**
     * Memperbarui data rekap di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_rekap' => 'required|string|max:255',
            'periode' => 'required|date',
            'batas_keterlambatan' => 'required|date_format:H:i',
            'mukafaah' => 'required|numeric',
            'bonus' => 'required|numeric',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ambil data rekap berdasarkan ID dan perbarui
        $recap = Recap::findOrFail($id);
        $recap->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('data-recap-admin')->with('success', 'Rekap data berhasil diperbarui!');
    }

    /**
     * Menghapus data rekap dari database.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        // Ambil data rekap berdasarkan ID dan hapus
        $recap = Recap::findOrFail($id);
        $recap->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('data-recap-admin')->with('success', 'Rekap data berhasil dihapus!');
    }
}