@extends('layouts.admin')

@section('title', 'Detail Rekap Presensi')

@section('content')

<div class="container px-6 mx-auto grid">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4 mt-6">
        <a href="{{ route('recaps.index') }}" class="text-blue-600 hover:underline">Daftar Rekap Presensi</a> > <span class="text-gray-800">Detail Rekap Presensi</span>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800">Detail Rekap Presensi</h1>
        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
            Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $recap->getRawOriginal('periode'), 'Asia/Jakarta')->locale('id')->translatedFormat('F Y') }}
        </span>
    </div>

    <!-- Card Pengajar -->
    <div id="pengajar-container" class="space-y-4">
        @foreach ($pengajars as $pengajar)
            @php
                // Gunakan nilai dari database untuk konsistensi
                $baseMukafaah = $recap->mukafaah ?? 30000;
                $maxBonus = $recap->bonus ?? 5000;
                $pengurangPerLate = 500; // Pengurang bonus per 5 menit keterlambatan
                $totalMukafaah = 0;
                $totalLateness = 0;
                $hadir = 0;
                $terlambat = 0;
                $tidakHadir = 0;
                $totalDays = count($dates);
                $presencesForPengajar = $presences->get($pengajar->id, collect());
                \Log::info("Pengajar ID: {$pengajar->id}, Base Mukafaah: $baseMukafaah, Max Bonus: $maxBonus");
            @endphp

            <!-- Hitung statistik rekap performa -->
            @foreach ($dates as $date)
                @php
                    $presence = $presencesForPengajar->firstWhere('date', $date);
                    if ($presence && $presence->arrival_time) {
                        // Validasi format waktu
                        if (!\Carbon\Carbon::hasFormat($recap->batas_keterlambatan, 'H:i:s')) {
                            $batas = \Carbon\Carbon::createFromTime(16, 15, 0, 'Asia/Jakarta');
                            \Log::warning('Invalid batas_keterlambatan, using default 16:15:00', ['recap_id' => $recap->id, 'value' => $recap->batas_keterlambatan]);
                        } else {
                            $batas = \Carbon\Carbon::parse($recap->batas_keterlambatan, 'Asia/Jakarta');
                        }
                        if (\Carbon\Carbon::hasFormat($presence->arrival_time, 'H:i:s')) {
                            $arrival = \Carbon\Carbon::parse($presence->arrival_time, 'Asia/Jakarta');
                        } else {
                            $arrival = null;
                            \Log::warning('Invalid arrival_time', ['presence' => $presence]);
                        }

                        if ($arrival && $arrival->greaterThan($batas)) {
                            $terlambat++;
                            $minutesLate = $arrival->diffInMinutes($batas);
                            if ($minutesLate < 0) {
                                \Log::error('Negative minutes late detected', ['date' => $date, 'arrival' => $arrival->toTimeString(), 'batas' => $batas->toTimeString()]);
                                $minutesLate = abs($minutesLate);
                            }
                            $totalLateness += $minutesLate;
                            $rangesOf5Minutes = ceil($minutesLate / 5);
                            $bonusReduction = $rangesOf5Minutes * $pengurangPerLate;
                            $currentBonus = max(0, $maxBonus - $bonusReduction);
                            $dailyMukafaah = $baseMukafaah + $currentBonus;
                            $totalMukafaah += $dailyMukafaah;
                            \Log::info("Pengajar: {$pengajar->id}, Date: $date, Minutes Late: $minutesLate, Bonus: $currentBonus, Mukafaah: $dailyMukafaah");
                        } else {
                            $hadir++;
                            $dailyMukafaah = $baseMukafaah + $maxBonus;
                            $totalMukafaah += $dailyMukafaah;
                            \Log::info("Pengajar: {$pengajar->id}, Date: $date, Tepat Waktu, Mukafaah: $dailyMukafaah");
                        }
                    } else {
                        $tidakHadir++;
                        \Log::info("Pengajar: {$pengajar->id}, Date: $date, Tidak Hadir");
                    }
                @endphp
            @endforeach

            @php
                $skorRataRata = $totalDays > 0 ? round(($hadir / $totalDays) * 100) : 0;
            @endphp

            <!-- Card Pengajar -->
            <div class="bg-white rounded-lg shadow-md p-10 pengajar-card" id="pengajar-card-{{ $pengajar->id }}">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Nama: {{ $pengajar->full_name }}</h3>
                    </div>
                </div>

                <!-- Tabel Rekap Performa -->
                <h3 class="text-base font-semibold text-gray-800 mt-3">Rekap Performa</h3>
                <div class="overflow-x-auto">
                    <table class="w-full mt-2 bg-white rounded-lg shadow text-sm">
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
                <h3 class="text-base font-semibold text-gray-800 mt-3">Detail Kehadiran</h3>
                <div class="overflow-x-auto">
                    <table class="w-full mt-2 bg-white rounded-lg shadow text-sm">
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
                                        // Waktu Kehadiran dan Pulang hanya H:i, Presensi dengan tanggal
                                        $timeIn = \Carbon\Carbon::parse($presence->arrival_time)->format('H:i');
                                        $timestampPresensi = \Carbon\Carbon::parse($presence->created_at)->format('H:i:s, d/m/Y');
                                        $timeOut = $presence->end_time ? \Carbon\Carbon::parse($presence->end_time)->format('H:i') : '-';
                                        if (!\Carbon\Carbon::hasFormat($recap->batas_keterlambatan, 'H:i:s')) {
                                            $batas = \Carbon\Carbon::createFromTime(16, 15, 0, 'Asia/Jakarta');
                                            \Log::warning('Invalid batas_keterlambatan, using default 16:15:00', ['recap_id' => $recap->id, 'value' => $recap->batas_keterlambatan]);
                                        } else {
                                            $batas = \Carbon\Carbon::parse($recap->batas_keterlambatan, 'Asia/Jakarta');
                                        }
                                        if (\Carbon\Carbon::hasFormat($presence->arrival_time, 'H:i:s')) {
                                            $arrival = \Carbon\Carbon::parse($presence->arrival_time, 'Asia/Jakarta');
                                        } else {
                                            $arrival = null;
                                            \Log::warning('Invalid arrival_time', ['presence' => $presence]);
                                        }

                                        if ($arrival && $arrival->greaterThan($batas)) {
                                            $minutesLate = $arrival->diffInMinutes($batas);
                                            if ($minutesLate < 0) {
                                                \Log::error('Negative minutes late detected', ['date' => $date, 'arrival' => $arrival->toTimeString(), 'batas' => $batas->toTimeString()]);
                                                $minutesLate = abs($minutesLate);
                                            }
                                            $rangesOf5Minutes = ceil($minutesLate / 5);
                                            $bonusReduction = $rangesOf5Minutes * $pengurangPerLate;
                                            $currentBonus = max(0, $maxBonus - $bonusReduction);
                                            $dailyMukafaah = $baseMukafaah + $currentBonus;
                                            $status = "Terlambat ($minutesLate menit)";
                                            $statusClass = 'text-yellow-800 bg-yellow-100';
                                            \Log::info("Pengajar: {$pengajar->id}, Date: $date, Minutes Late: $minutesLate, Bonus: $currentBonus, Mukafaah: $dailyMukafaah");
                                        } else {
                                            $dailyMukafaah = $baseMukafaah + $maxBonus;
                                            $status = 'Tepat Waktu';
                                            $statusClass = 'text-green-800 bg-green-100';
                                            \Log::info("Pengajar: {$pengajar->id}, Date: $date, Tepat Waktu, Mukafaah: $dailyMukafaah");
                                        }
                                    } else {
                                        $status = 'Tidak Hadir';
                                        $statusClass = 'text-red-800 bg-red-100';
                                        \Log::info("Pengajar: {$pengajar->id}, Date: $date, Tidak Hadir");
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

                <!-- Mukafaah -->
                <div class="mt-4">
                    <h3 class="text-base font-semibold text-gray-800">Mukafaah</h3>
                    <p class="text-sm text-gray-600">Mukafaah Pokok: Rp {{ number_format($baseMukafaah, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600">Bonus Maksimal: Rp {{ number_format($maxBonus, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600">Total Keterlambatan: {{ $totalLateness }} menit</p>
                    <p class="text-sm font-semibold text-gray-800">Total Mukafaah: Rp {{ number_format($totalMukafaah, 0, ',', '.') }}</p>
                </div>
            </div>
            <button class="no-pdf px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600" onclick="downloadCard('pengajar-card-{{ $pengajar->id }}', '{{ $pengajar->full_name }}-Rekap-Presensi.pdf')">
                Download PDF
            </button>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadCard(elementId, filename) {
        const element = document.getElementById(elementId);
        const opt = {
            margin: 0.3,
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
@endsection
