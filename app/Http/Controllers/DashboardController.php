<?php

namespace App\Http\Controllers;

use App\Models\AdditionalMukafaah;
use App\Models\PengeluaranBulanan;
use App\Models\Presence;
use App\Models\Contract;
use App\Models\Recap;
use App\Models\Santri;
use App\Models\TransaksiKas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function indexAdmin()
    {
        // Total Pengajar
        $totalPengajar = User::where('role', 'pengajar')->count();

        // Jumlah Santri Aktif
        $totalSantriAktif = Santri::where('status', 'aktif')->count();

        // Pengeluaran Bulan Ini
        $bulanMap = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        ];
        $periode = now()->year . '-' . $bulanMap[now()->monthName];

        // Ambil rekapan untuk periode ini
        $recap = Recap::where('periode', $periode)->first();
        $totalMukafaahAll = 0;

        if ($recap) {
            $pengajars = User::where('role', 'pengajar')->where('accepted', '1')->get();
            $dates = json_decode($recap->dates, true) ?? [];
            $presences = Presence::whereIn('date', $dates)
                ->whereIn('user_id', $pengajars->pluck('id'))
                ->get()
                ->groupBy('user_id');
            $additionalMukafaahs = AdditionalMukafaah::where('recap_id', $recap->id)->get();

            foreach ($pengajars as $pengajar) {
                $baseMukafaah = $recap->mukafaah;
                $maxBonus = $recap->bonus;
                $pengurangPerLate = 500;
                $totalMukafaahBase = 0;
                $presencesForPengajar = $presences->get($pengajar->id, collect());
                $additionalMukafaah = $additionalMukafaahs->where('user_id', $pengajar->id)->first();
                $additionalAmount = $additionalMukafaah ? $additionalMukafaah->additional_mukafaah : 0;

                foreach ($dates as $date) {
                    $presence = $presencesForPengajar->firstWhere('date', $date);
                    if ($presence && $presence->arrival_time) {
                        $batas = \Carbon\Carbon::hasFormat($recap->batas_keterlambatan, 'H:i:s')
                            ? \Carbon\Carbon::parse($recap->batas_keterlambatan, 'Asia/Jakarta')
                            : \Carbon\Carbon::createFromTime(16, 15, 0, 'Asia/Jakarta');
                        $arrival = \Carbon\Carbon::hasFormat($presence->arrival_time, 'H:i:s')
                            ? \Carbon\Carbon::parse($presence->arrival_time, 'Asia/Jakarta')
                            : null;

                        if ($arrival && $arrival->greaterThan($batas)) {
                            $minutesLate = $arrival->diffInMinutes($batas);
                            $rangesOf5Minutes = ceil($minutesLate / 5);
                            $bonusReduction = $rangesOf5Minutes * $pengurangPerLate;
                            $currentBonus = max(0, $maxBonus - $bonusReduction);
                            $dailyMukafaah = $baseMukafaah + $currentBonus;
                            $totalMukafaahBase += $dailyMukafaah;
                        } else {
                            $dailyMukafaah = $baseMukafaah + $maxBonus;
                            $totalMukafaahBase += $dailyMukafaah;
                        }
                    }
                }
                $totalMukafaahAll += ($totalMukafaahBase + $additionalAmount);
            }
        }

        // Ambil pengeluaran lain dari PengeluaranBulanan
        $pengeluaranBulanIni = PengeluaranBulanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');

        // Total pengeluaran (mukafaah + pengeluaran lain)
        $pengeluaranBulanIni += $totalMukafaahAll;

        // Saldo Akhir Tahun Ini
        $totalDebet = TransaksiKas::where('jenis', 'debet')
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');
        $totalKredit = TransaksiKas::where('jenis', 'kredit')
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');
        $saldoAkhir = $totalDebet - $totalKredit;

        // Presensi Hari Ini
        $presences = Presence::with('user')
            ->whereDate('date', now()->toDateString())
            ->get();

        return view('admin.dashboard-admin', compact(
            'totalPengajar',
            'totalSantriAktif',
            'pengeluaranBulanIni',
            'saldoAkhir',
            'presences'
        ));
    }

    public function indexPengajar()
    {
        $user_id = Auth::id();
        $currentDate = Carbon::now();

        // Get dynamic month name
        $monthName = $currentDate->format('F Y');

        // Total attendance for current month
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $totalAttendance = Presence::where('user_id', $user_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->count();

        // Contract duration
        $contract = Contract::where('user_id', $user_id)
            ->where('status', 'active')
            ->first();

        $contractDuration = null;
        if ($contract) {
            $startDate = Carbon::parse($contract->start_date);
            $endDate = $contract->end_date ? Carbon::parse($contract->end_date) : $currentDate;
            $contractDuration = $startDate->diffInDays($endDate);
        }

        // Recent presences
        $recentPresences = Presence::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pengajar.dashboard-pengajar', compact(
            'monthName',
            'totalAttendance',
            'contractDuration',
            'recentPresences'
        ));
    }
}
