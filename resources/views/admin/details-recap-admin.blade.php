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
    <div class="overflow-x-auto">
        <!-- Tabel Kelola Mukafaah Tambahan -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Kelola Mukafaah Tambahan</h3>
                <button type="button" id="add-mukafaah-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Mukafaah
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow text-sm">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Pengajar</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Nominal (Rp)</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="additional-mukafaah-table" class="bg-white divide-y">
                        @php $no = 1; $totalAdditionalMukafaah = 0; @endphp
                        @foreach($recap->additionalMukafaahs as $additionalMukafaah)
                            @php
                                $pengajarData = $pengajars->firstWhere('id', $additionalMukafaah->user_id);
                                $totalAdditionalMukafaah += $additionalMukafaah->additional_mukafaah;
                            @endphp
                            @if($pengajarData)
                            <tr data-id="{{ $additionalMukafaah->id }}">
                                <td class="px-4 py-3">{{ $no++ }}</td>
                                <td class="px-4 py-3">{{ $pengajarData->full_name }}</td>
                                <td class="px-4 py-3">{{ $additionalMukafaah->description }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($additionalMukafaah->additional_mukafaah, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 flex items-center gap-x-2 min-w-26">
                                    <button class="edit-mukafaah-button text-blue-600 hover:text-blue-800" data-id="{{ $additionalMukafaah->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('recaps.additional.destroy', [$recap->id, $additionalMukafaah->id]) }}" method="POST" class="delete-mukafaah-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-mukafaah-button text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Mukafaah Keseluruhan -->
        @php
            $totalMukafaahAll = 0;
            foreach ($pengajars as $pengajar) {
                $baseMukafaah = $recap->mukafaah;
                $maxBonus = $recap->bonus;
                $pengurangPerLate = 500;
                $totalMukafaahBase = 0;
                $presencesForPengajar = $presences->get($pengajar->id, collect());
                $additionalMukafaah = $recap->additionalMukafaahs->where('user_id', $pengajar->id)->first();
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
                            $minutesLate = $minutesLate < 0 ? abs($minutesLate) : $minutesLate;
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
        @endphp
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-green-500 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-green-800">Total Mukafaah Keseluruhan</h5>
                        <p class="text-xs text-green-600">Semua Pengajar, Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $recap->getRawOriginal('periode'), 'Asia/Jakarta')->locale('id')->translatedFormat('F Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-green-800">
                        Rp {{ number_format($totalMukafaahAll, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrollable Container for Pengajar Cards -->
    <div>
        <!-- Modal untuk Form Mukafaah Tambahan -->
        <div id="mukafaah-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
                <span id="close-mukafaah-form-modal" class="float-right cursor-pointer text-gray-500">×</span>
                <h2 class="text-lg font-semibold">Form Mukafaah Tambahan</h2>
                <form id="mukafaah-form" action="{{ route('recaps.additional.store', $recap->id) }}" method="POST">
                    @csrf
                    <input type="hidden" id="mukafaah-id" name="id">
                    <input type="hidden" name="_method" id="mukafaah-method" value="POST">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengajar</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            <option value="">Pilih Pengajar</option>
                            @foreach($pengajars as $pengajar)
                                <option value="{{ $pengajar->id }}">{{ $pengajar->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <input type="text" id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                        <input type="number" id="amount" name="amount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="cancel-mukafaah-form-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Pengajar -->
        <div id="pengajar-container" class="space-y-6 mb-10">
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
                    $additionalMukafaah = $recap->additionalMukafaahs->where('user_id', $pengajar->id)->first();
                    $additionalAmount = $additionalMukafaah ? $additionalMukafaah->additional_mukafaah : 0;
                    $additionalDescription = $additionalMukafaah ? $additionalMukafaah->description : '-';
                @endphp

                <!-- Hitung statistik rekap performa -->
                @foreach ($dates as $date)
                    @php
                        $presence = $presencesForPengajar->firstWhere('date', $date);
                        if ($presence && $presence->arrival_time) {
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
                <div class="bg-white rounded-lg shadow-md p-6 pengajar-card" id="pengajar-card-{{ $pengajar->id }}">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $pengajar->full_name }}</h3>
                        </div>
                        <button class="no-pdf px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600" onclick="downloadCard('pengajar-card-{{ $pengajar->id }}', '{{ $pengajar->full_name }}-Rekap-Presensi.pdf')">
                            Download PDF
                        </button>
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
                                        <p class="text-sm text-green-600">Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $recap->getRawOriginal('periode'), 'Asia/Jakarta')->locale('id')->translatedFormat('F Y') }}</p>
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
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Menangani tombol untuk membuka form mukafaah
        document.getElementById('add-mukafaah-button')?.addEventListener('click', () => {
            // Reset form
            document.getElementById('mukafaah-form').reset();
            document.getElementById('mukafaah-id').value = '';
            document.getElementById('mukafaah-method').value = 'POST';
            document.getElementById('mukafaah-form').action = "{{ route('recaps.additional.store', $recap->id) }}";
            document.getElementById('mukafaah-form-modal').classList.remove('hidden');
        });

        // Menangani penutupan modal form mukafaah
        document.getElementById('close-mukafaah-form-modal')?.addEventListener('click', () => {
            document.getElementById('mukafaah-form-modal')?.classList.add('hidden');
        });

        document.getElementById('cancel-mukafaah-form-button')?.addEventListener('click', () => {
            document.getElementById('mukafaah-form-modal')?.classList.add('hidden');
        });

        // Menangani pengiriman form mukafaah dengan AJAX
        document.getElementById('mukafaah-form')?.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const id = document.getElementById('mukafaah-id')?.value;

            // URL yang benar sesuai dengan route
            let url;
            if (id) {
                // Update existing mukafaah
                url = `/recaps/{{ $recap->id }}/additional-mukafaahs/${id}`;
                formData.append('_method', 'PUT');
            } else {
                // Create new mukafaah
                url = this.action; // Menggunakan action yang sudah di-set
                formData.append('_method', 'POST');
            }

            console.log('Submitting to URL:', url); // Debug log
            console.log('Method:', id ? 'PUT' : 'POST'); // Debug log

            fetch(url, {
                method: "POST", // Selalu POST karena Laravel method spoofing
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: data.success ? "Berhasil!" : "Gagal!",
                    text: data.message,
                    icon: data.success ? "success" : "error",
                    confirmButtonText: "OK"
                }).then(() => {
                    if (data.success) {
                        document.getElementById('mukafaah-form-modal').classList.add('hidden');
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire("Error!", `Terjadi kesalahan: ${error.message}`, "error");
            });
        });

        // Menangani tombol edit
        document.querySelectorAll('.edit-mukafaah-button').forEach(button => {
            button.addEventListener('click', () => {
                const mukafaahId = button.getAttribute('data-id');
                // URL harus konsisten dengan route di controller
                const url = `/recaps/{{ $recap->id }}/additional-mukafaahs/${mukafaahId}/edit`;

                console.log('Fetching edit data from:', url); // Debug log

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data) {
                            // Populate form dengan data yang diterima
                            document.getElementById('mukafaah-id').value = data.data.id;
                            document.getElementById('user_id').value = data.data.user_id;
                            document.getElementById('description').value = data.data.description || '';
                            document.getElementById('amount').value = data.data.additional_mukafaah;

                            // Set form action dan method untuk update
                            document.getElementById('mukafaah-form').action = `/recaps/{{ $recap->id }}/additional-mukafaahs/${data.data.id}`;
                            document.getElementById('mukafaah-method').value = 'PUT';

                            // Show modal
                            document.getElementById('mukafaah-form-modal').classList.remove('hidden');
                        } else {
                            throw new Error(data.message || 'Data tidak ditemukan');
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error!", `Gagal mengambil data mukafaah: ${error.message}`, "error");
                    });
            });
        });

        // Menangani tombol delete
        document.querySelectorAll('.delete-mukafaah-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus mukafaah ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                                location.reload();
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            location.reload();
                        });
                    }
                });
            });
        });

        // Download PDF
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
    });
</script>
@endsection
