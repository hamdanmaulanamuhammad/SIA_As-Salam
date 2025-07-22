@extends('layouts.admin')

@section('title', 'Detail Administrasi')

@section('content')
<div class="container px-6 mx-auto grid">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-y-4 mb-6 mt-6">
        <h1 class="text-2xl font-bold text-center md:text-left">
            Periode : {{ $administrasi->bulan }} {{ $administrasi->tahun }}
        </h1>
        <a href="{{ route('keuangan.index') }}?tab=administrasi-bulanan"
           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 transition duration-200 w-full md:w-auto">
            <i class="fa fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <!-- Display message if recap is missing -->
    @if($recapMissing)
        <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 max-h-fit rounded-md">
            <p class="text-md"><strong>Peringatan:</strong> Tidak ada rekapan mukafaah untuk periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}. Silakan buat rekapan terlebih dahulu.</p>
        </div>
    @endif

    <div class="rounded-lg shadow-md overflow-x-auto">
    <!-- Informasi Rekening -->
    <div class="bg-white p-6 mt-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 text-center md:text-left mb-4">Informasi Rekening</h2>

        @if($administrasi->bankAccount)
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                    <div>
                        <p class="font-medium text-gray-600">Nama Bank</p>
                        <p class="text-gray-900">{{ $administrasi->bankAccount->bank_name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Nomor Rekening</p>
                        <p class="text-gray-900">{{ $administrasi->bankAccount->account_number }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Nama Pemilik</p>
                        <p class="text-gray-900">{{ $administrasi->bankAccount->account_holder }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-start space-x-3 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                <div class="p-2 bg-yellow-400 rounded-full text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-yellow-800">Rekening belum tersedia</p>
                    <p class="text-xs text-yellow-700">Belum ada rekening yang dipilih untuk periode ini.</p>
                </div>
            </div>
        @endif
    </div>

        <!-- Tabel Pengeluaran Bulanan -->
        <div class="bg-white p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-y-4 mb-4">
            <h2 class="text-xl font-semibold text-center md:text-left">
                Data Pengeluaran
            </h2>
            <div class="flex space-x-2">
                <button id="tambahPengeluaranButton" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200 w-full md:w-auto">
                    <i class="fa fa-plus mr-2"></i>Pengeluaran
                </button>
                <button id="downloadPdfButton" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200 w-full md:w-auto">
                    <i class="fa fa-download mr-2"></i>Download PDF
                </button>
            </div>
        </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white text-sm">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Pengeluaran</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Tanggal Dibuat</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pengeluaranTableBody" class="bg-white divide-y">
                        @if($recap)
                            @php
                                $totalMukafaahAll = 0;
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
                                $totalPengeluaran = $pengeluaran->sum('jumlah');
                                $grandTotal = $totalMukafaahAll + $totalPengeluaran;
                            @endphp
                            <tr class="bg-white">
                                <td class="px-4 py-3 text-sm">1</td>
                                <td class="px-4 py-3 text-sm">Total Mukafaah Keseluruhan</td>
                                <td class="px-4 py-3 text-sm text-blue-600 font-medium">{{ 'Rp ' . number_format($totalMukafaahAll, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm">Semua Pengajar</td>
                                <td class="px-4 py-3 text-sm">Periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}</td>
                                <td class="px-4 py-3 text-sm"></td>
                            </tr>
                        @endif

                        @foreach($pengeluaran as $index => $item)
                        <tr class="{{ ($index + 1) % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 text-sm">{{ ($pengeluaran->currentPage() - 1) * $pengeluaran->perPage() + $index + 2 }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->nama_pengeluaran ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-blue-600 font-medium">{{ 'Rp ' . number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex space-x-2">
                                    <button class="edit-pengeluaran-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('keuangan.administrasi-bulanan.pengeluaran.destroy', [$administrasi->id, $item->id]) }}" method="POST" class="delete-pengeluaran-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold tracking-wide text-left text-gray-500 uppercase border-t bg-gray-50">
                            <td class="px-4 py-3 text-sm" colspan="2">Total Pengeluaran</td>
                            <td class="px-4 py-3 text-sm text-blue-600 font-medium">{{ 'Rp ' . number_format($grandTotal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm" colspan="4">Semua Pengeluaran (Mukafaah + Lainnya)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-6">
                {{ $pengeluaran->links() }}
            </div>
        </div>
    </div>

    <!-- Scrollable Container for Pengajar Cards -->
    <div class="overflow-y-auto mt-8 min-h-fit overflow-x-auto">
        <!-- Card Pengajar -->
        @if($recap)
            <div class="space-y-6 mb-10">
                @foreach ($pengajars as $pengajar)
                    @php
                        $baseMukafaah = $recap->mukafaah;
                        $maxBonus = $recap->bonus;
                        $pengurangPerLate = 500;
                        $totalMukafaahBase = 0;
                        $totalLateness = 0;
                        $hadir = 0;
                        $terlambat = 0;
                        $tidakHadir = 0;
                        $totalDays = count($dates);
                        $presencesForPengajar = $presences->get($pengajar->id, collect());
                        $additionalMukafaah = $additionalMukafaahs->where('user_id', $pengajar->id)->first();
                        $additionalAmount = $additionalMukafaah ? $additionalMukafaah->additional_mukafaah : 0;
                        $additionalDescription = $additionalMukafaah ? $additionalMukafaah->description : '-';
                    @endphp

                    <!-- Hitung statistik rekap performa -->
                    @foreach ($dates as $date)
                        @php
                            $presence = $presencesForPengajar->firstWhere('date', $date);
                            if ($presence && $presence->arrival_time) {
                                $batas = \Carbon\Carbon::hasFormat($recap->batas_keterlambatan, 'H:i:s')
                                    ? \Carbon\Carbon::parse($recap->batas_keterlambatan, 'Asia/Jakarta')
                                    : \Carbon\Carbon::createFromTime(16, 15, 0, 'Asia/Jakarta');
                                $arrival = \Carbon\Carbon::hasFormat($presence->arrival_time, 'H:i:s')
                                    ? \Carbon\Carbon::parse($presence->arrival_time, 'Asia/Jakarta')
                                    : null;

                                if ($arrival && $arrival->greaterThan($batas)) {
                                    $terlambat++;
                                    $minutesLate = $arrival->diffInMinutes($batas);
                                    $minutesLate = $minutesLate < 0 ? abs($minutesLate) : $minutesLate;
                                    $totalLateness += $minutesLate;
                                    $rangesOf5Minutes = ceil($minutesLate / 5);
                                    $bonusReduction = $rangesOf5Minutes * $pengurangPerLate;
                                    $currentBonus = max(0, $maxBonus - $bonusReduction);
                                    $dailyMukafaah = $baseMukafaah + $currentBonus;
                                    $totalMukafaahBase += $dailyMukafaah;
                                } else {
                                    $hadir++;
                                    $dailyMukafaah = $baseMukafaah + $maxBonus;
                                    $totalMukafaahBase += $dailyMukafaah;
                                }
                            } else {
                                $tidakHadir++;
                            }
                        @endphp
                    @endforeach

                    @php
                        $skorRataRata = $totalDays > 0 ? round(($hadir / $totalDays) * 100) : 0;
                        $totalMukafaahFinal = $totalMukafaahBase + $additionalAmount;
                    @endphp

                    <!-- Card Pengajar -->
                    <div class="bg-white rounded-lg shadow-md p-6 pengajar-card mt-6" id="pengajar-card-{{ $pengajar->id }}">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $pengajar->full_name }}</h3>
                            </div>
                        </div>

                        <!-- Tabel Rekap Performa -->
                        <h4 class="text-base font-semibold text-gray-800 mb-3">Rekap Performa</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full bg-white rounded-lg shadow text-sm">
                                <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                        <th class="px-3 py-2">Hadir</th>
                                        <th class="px-3 py-2">Terlambat</th>
                                        <th class="px-3 py-2">Tidak Hadir</th>
                                        <th class="px-3 py-2">Skor Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y">
                                    <tr class="text-gray-700">
                                        <td class="px-3 py-2">{{ $hadir }}</td>
                                        <td class="px-3 py-2">{{ $terlambat }}</td>
                                        <td class="px-3 py-2">{{ $tidakHadir }}</td>
                                        <td class="px-3 py-2">{{ $skorRataRata }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Detail Kehadiran -->
                        <h4 class="text-base font-semibold text-gray-800 mb-3">Detail Kehadiran</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full bg-white rounded-lg shadow text-sm">
                                <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                        <th class="px-3 py-2">Hari Mengajar</th>
                                        <th class="px-3 py-2">Waktu Kehadiran</th>
                                        <th class="px-3 py-2">Waktu Presensi</th>
                                        <th class="px-3 py-2">Waktu Pulang</th>
                                        <th class="px-3 py-2">Status</th>
                                        <th class="px-3 py-2">Nominal Mukafaah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y">
                                    @foreach ($dates as $date)
                                        @php
                                            $presence = $presencesForPengajar->firstWhere('date', $date);
                                            $status = '';
                                            $dailyMukafaah = 0;
                                            $timeIn = '-';
                                            $timeOut = '-';
                                            $timestampPresensi = '-';
                                            $statusClass = '';

                                            if ($presence && $presence->arrival_time) {
                                                $timeIn = \Carbon\Carbon::parse($presence->arrival_time)->format('H:i');
                                                $timestampPresensi = \Carbon\Carbon::parse($presence->created_at)->format('H:i:s, d/m/Y');
                                                $timeOut = $presence->end_time ? \Carbon\Carbon::parse($presence->end_time)->format('H:i') : '-';
                                                $batas = \Carbon\Carbon::hasFormat($recap->batas_keterlambatan, 'H:i:s')
                                                    ? \Carbon\Carbon::parse($recap->batas_keterlambatan, 'Asia/Jakarta')
                                                    : \Carbon\Carbon::createFromTime(16, 15, 0, 'Asia/Jakarta');
                                                $arrival = \Carbon\Carbon::hasFormat($presence->arrival_time, 'H:i:s')
                                                    ? \Carbon\Carbon::parse($presence->arrival_time, 'Asia/Jakarta')
                                                    : null;

                                                if ($arrival && $arrival->greaterThan($batas)) {
                                                    $minutesLate = $arrival->diffInMinutes($batas);
                                                    $minutesLate = $minutesLate < 0 ? abs($minutesLate) : $minutesLate;
                                                    $rangesOf5Minutes = ceil($minutesLate / 5);
                                                    $bonusReduction = $rangesOf5Minutes * $pengurangPerLate;
                                                    $currentBonus = max(0, $maxBonus - $bonusReduction);
                                                    $dailyMukafaah = $baseMukafaah + $currentBonus;
                                                    $status = "Terlambat ($minutesLate menit)";
                                                    $statusClass = 'text-yellow-800 bg-yellow-100';
                                                } else {
                                                    $dailyMukafaah = $baseMukafaah + $maxBonus;
                                                    $status = 'Tepat Waktu';
                                                    $statusClass = 'text-green-800 bg-green-100';
                                                }
                                            } else {
                                                $status = 'Tidak Hadir';
                                                $statusClass = 'text-red-800 bg-red-100';
                                            }
                                        @endphp
                                        <tr class="text-gray-700">
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                            <td class="px-3 py-2">{{ $timeIn }}</td>
                                            <td class="px-3 py-2">{{ $timestampPresensi }}</td>
                                            <td class="px-3 py-2">{{ $timeOut }}</td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold {{ $statusClass }} rounded-full">{{ $status }}</span>
                                            </td>
                                            <td class="px-3 py-2">Rp {{ number_format($dailyMukafaah, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Mukafaah -->
                        <div class="space-y-3">
                            <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-blue-400 rounded-full p-2 mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-semibold text-blue-800">Total Mukafaah</h5>
                                            <p class="text-xs text-blue-600">Mukafaah dari kehadiran</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-blue-800">
                                            Rp {{ number_format($totalMukafaahBase, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($additionalAmount > 0)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-yellow-400 rounded-full p-2 mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-semibold text-yellow-800">{{ $additionalDescription }}</h5>
                                            <p class="text-xs text-yellow-600">Mukafaah Tambahan</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-yellow-800">
                                            Rp {{ number_format($additionalAmount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-green-500 rounded-full p-2 mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-green-800">Total</h4>
                                            <p class="text-sm text-green-600">Periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-green-800">
                                            Rp {{ number_format($totalMukafaahFinal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="bg-yellow-400 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-yellow-800">Tidak Ada Data Mukafaah</h5>
                        <p class="text-xs text-yellow-600">Tidak ada data rekap presensi untuk periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal for Pengeluaran Form -->
        <div id="pengeluaran-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Form Pengeluaran</h3>
                    <button id="close-pengeluaran-form-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <form id="pengeluaran-form" action="{{ route('keuangan.administrasi-bulanan.pengeluaran.store', $administrasi->id) }}" method="POST">
                    @csrf
                    <input type="hidden" id="pengeluaran-id" name="id">
                    <input type="hidden" name="_method" id="pengeluaran-method" value="POST">
                    <div class="mb-4">
                        <label for="nama_pengeluaran" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengeluaran <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" min="0" required>
                    </div>
                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" rows="4"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-pengeluaran-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const routes = {
        pengeluaranStore: "{{ route('keuangan.administrasi-bulanan.pengeluaran.store', $administrasi->id) }}",
        pengeluaranEdit: "{{ route('keuangan.administrasi-bulanan.pengeluaran.edit', [$administrasi->id, ':id']) }}",
        pengeluaranUpdate: "{{ route('keuangan.administrasi-bulanan.pengeluaran.update', [$administrasi->id, ':id']) }}",
        pengeluaranDestroy: "{{ route('keuangan.administrasi-bulanan.pengeluaran.destroy', [$administrasi->id, ':id']) }}",
        pengeluaranIndex: "{{ route('keuangan.administrasi-bulanan.pengeluaran.index', $administrasi->id) }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk submit form
        function submitForm() {
            const form = document.getElementById('pengeluaran-form');
            const modal = document.getElementById('pengeluaran-form-modal');
            const submitButton = form.querySelector('button[type="submit"]');

            const grandTotal = {{ isset($grandTotal) ? $grandTotal : 0 }};
            if (grandTotal === 0) {
                console.log('No recap data available.');
            } else {
                // Process grandTotal
                console.log('Grand Total: ' + grandTotal);
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const id = form.querySelector('input[name="id"]').value;
                const formData = new FormData(form);
                if (id) formData.append('_method', 'PUT');

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';

                const url = id ? routes.pengeluaranUpdate.replace(':id', id) : routes.pengeluaranStore;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';

                    if (data.success) {
                        modal.classList.add('hidden');
                        Swal.fire({
                            title: "Berhasil!",
                            text: data.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location = routes.pengeluaranIndex;
                        });
                    } else {
                        let errorMessage = data.message || 'Terjadi kesalahan.';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            title: "Gagal!",
                            html: errorMessage,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';
                    Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                });
            });
        }

        // Fungsi untuk edit data
        function editData() {
            document.querySelectorAll('.edit-pengeluaran-button').forEach(button => {
                button.addEventListener('click', () => {
                    const itemId = button.getAttribute('data-id');

                    fetch(routes.pengeluaranEdit.replace(':id', itemId), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const form = document.getElementById('pengeluaran-form');
                            form.querySelector('input[name="id"]').value = data.data.id;
                            document.getElementById('nama_pengeluaran').value = data.data.nama_pengeluaran || '';
                            document.getElementById('jumlah').value = data.data.jumlah;
                            document.getElementById('keterangan').value = data.data.keterangan || '';
                            form.querySelector('input[name="_method"]').value = 'PUT';
                            form.action = routes.pengeluaranUpdate.replace(':id', data.data.id);
                            document.querySelector('#pengeluaran-form-modal h3').textContent = 'Edit Pengeluaran';
                            document.getElementById('pengeluaran-form-modal').classList.remove('hidden');
                        } else {
                            Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Edit error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                    });
                });
            });
        }

        // Fungsi untuk hapus data
        function deleteData() {
            document.querySelectorAll('.delete-pengeluaran-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menghapus pengeluaran ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Pengeluaran berhasil dihapus.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location = routes.pengeluaranIndex;
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || 'Gagal menghapus pengeluaran.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus pengeluaran.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                });
            });
        }

        // Validasi jumlah
        function validateJumlahInput() {
            const input = document.getElementById('jumlah');
            input.addEventListener('input', () => {
                const value = parseInt(input.value);
                if (value < 0) {
                    input.setCustomValidity('Jumlah tidak boleh negatif');
                } else {
                    input.setCustomValidity('');
                }
            });
        }

        // Modal Handlers
        document.getElementById('tambahPengeluaranButton').addEventListener('click', () => {
            document.getElementById('pengeluaran-form').reset();
            document.getElementById('pengeluaran-id').value = '';
            document.getElementById('pengeluaran-method').value = 'POST';
            document.getElementById('pengeluaran-form').action = routes.pengeluaranStore;
            document.querySelector('#pengeluaran-form-modal h3').textContent = 'Form Pengeluaran';
            document.getElementById('pengeluaran-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-pengeluaran-form-modal').addEventListener('click', () => {
            document.getElementById('pengeluaran-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-pengeluaran-form-button').addEventListener('click', () => {
            document.getElementById('pengeluaran-form-modal').classList.add('hidden');
        });

        document.getElementById('downloadPdfButton').addEventListener('click', () => {
            window.location.href = "{{ route('keuangan.administrasi-bulanan.download-pdf', $administrasi->id) }}";
        });

        // Inisialisasi fungsi
        submitForm();
        editData();
        deleteData();
        validateJumlahInput();
    });
</script>
@endsection
