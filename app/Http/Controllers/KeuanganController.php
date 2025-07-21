<?php

namespace App\Http\Controllers;

use App\Models\InfaqTahunan;
use App\Models\AdministrasiBulanan;
use App\Models\BukuKas;
use App\Models\Santri;
use App\Models\BankAccount; // Add this import
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $infaqTahunan = InfaqTahunan::with('infaqSantris')->paginate(10);
        $administrasiBulanan = AdministrasiBulanan::with('pengeluaranBulanan')->paginate(10);
        $bukuKas = BukuKas::with('transaksiKas')->paginate(10);
        $bankAccounts = BankAccount::all(); // Fetch all bank accounts

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
            'bankAccounts', // Add bankAccounts to the compact
            'totalInfaqWajib',
            'totalInfaqSukarela',
            'totalInfaq',
            'totalKekurangan'
        ));
    }
}
