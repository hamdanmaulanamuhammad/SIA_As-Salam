<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Administrasi dan Mukafaah TPA As-Salam {{ $administrasi->bulan }} {{ $administrasi->tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .letterhead {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .letterhead h1 {
            font-size: 18px;
            margin: 0;
            color: #1a1a1a;
        }
        .letterhead p {
            font-size: 12px;
            color: #4a4a4a;
            margin: 5px 0;
        }
        h2 {
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1a1a1a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
        }
        .bank-info {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        .bank-info h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #1a1a1a;
        }
        .bank-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .bank-item {
            padding: 5px;
        }
        .bank-item strong {
            display: block;
            font-size: 11px;
            color: #4a4a4a;
            text-transform: uppercase;
        }
        .bank-item span {
            font-size: 12px;
            color: #1a1a1a;
        }
        .pengajar-section {
            page-break-before: always;
        }
        .pengajar-section:last-child {
            page-break-after: auto;
        }
        @media print {
            body {
                margin: 15px;
            }
            .pengajar-section {
                page-break-before: always;
            }
            .pengajar-section:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <!-- HALAMAN PERTAMA: Data Pengeluaran dan Informasi Rekening -->
    <div>
        <div class="letterhead">
            <h1>Laporan Administrasi dan Mukafaah</h1>
            <h1>TPA As-Salam</h1>
            <p>Periode: {{ $administrasi->bulan }} {{ $administrasi->tahun }}</p>
        </div>

        <!-- Data Pengeluaran Table -->
        <h2>Data Pengeluaran</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengeluaran</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @if($recap)
                    <tr>
                        <td>1</td>
                        <td>Total Mukafaah Keseluruhan</td>
                        <td>Rp {{ number_format($totalMukafaahAll, 0, ',', '.') }}</td>
                        <td>Semua Pengajar</td>
                        <td>Periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}</td>
                    </tr>
                @endif
                @foreach($pengeluaran as $index => $item)
                    <tr>
                        <td>{{ $index + ($recap ? 2 : 1) }}</td>
                        <td>{{ $item->nama_pengeluaran ?? '-' }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">Total Pengeluaran</td>
                    <td colspan="3">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Simplified Bank Account Information -->
        <h2>Informasi Rekening</h2>
        @if($administrasi->bankAccount)
            <div class="bank-info">
                <h3>Detail Rekening Transfer</h3>
                <div class="bank-details">
                    <div class="bank-item">
                        <strong>Nama Bank</strong>
                        <span>{{ $administrasi->bankAccount->bank_name }}</span>
                    </div>
                    <div class="bank-item">
                        <strong>Nomor Rekening</strong>
                        <span>{{ $administrasi->bankAccount->account_number }}</span>
                    </div>
                    <div class="bank-item">
                        <strong>Nama Penerima</strong>
                        <span>{{ $administrasi->bankAccount->account_holder }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bank-info">
                <h3>Informasi Rekening</h3>
                <p style="margin: 0; color: #6c757d;">Rekening belum tersedia untuk periode ini.</p>
            </div>
        @endif
    </div>

    <!-- LAMPIRAN: Setiap Pengajar di Halaman Terpisah -->
    @if($recap)
        @foreach ($pengajars as $pengajar)
            <div class="pengajar-section">
                @php
                    $baseMukafaah = $recap->mukafaah;
                    $maxBonus = $recap->bonus;
                    $pengurangPerLate = 500;
                    $totalMukafaahBase = 0;
                    $presencesForPengajar = $presences->get($pengajar->id, collect());
                    $additionalMukafaah = $additionalMukafaahs->where('user_id', $pengajar->id)->first();
                    $additionalAmount = $additionalMukafaah ? $additionalMukafaah->additional_mukafaah : 0;
                    $additionalDescription = $additionalMukafaah ? $additionalMukafaah->description : '-';
                @endphp

                <div class="letterhead">
                    <h1>Lampiran: Presensi {{ $pengajar->full_name }}</h1>
                    <p>Periode: {{ $administrasi->bulan }} {{ $administrasi->tahun }}</p>
                </div>

                <h2>Detail Presensi Harian</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Hari Mengajar</th>
                            <th>Waktu Kehadiran</th>
                            <th>Waktu Presensi</th>
                            <th>Waktu Pulang</th>
                            <th>Status</th>
                            <th>Nominal Mukafaah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dates as $date)
                            @php
                                $presence = $presencesForPengajar->firstWhere('date', $date);
                                $status = '';
                                $dailyMukafaah = 0;
                                $timeIn = '-';
                                $timeOut = '-';
                                $timestampPresensi = '-';

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
                                    } else {
                                        $dailyMukafaah = $baseMukafaah + $maxBonus;
                                        $status = 'Tepat Waktu';
                                    }
                                    $totalMukafaahBase += $dailyMukafaah;
                                } else {
                                    $status = 'Tidak Hadir';
                                }
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $timeIn }}</td>
                                <td>{{ $timestampPresensi }}</td>
                                <td>{{ $timeOut }}</td>
                                <td>{{ $status }}</td>
                                <td>Rp {{ number_format($dailyMukafaah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h2>Ringkasan Mukafaah {{ $pengajar->full_name }}</h2>
                <table style="max-width: 500px;">
                    <tr>
                        <td><strong>Mukafaah Kehadiran</strong></td>
                        <td style="text-align: right;">Rp {{ number_format($totalMukafaahBase, 0, ',', '.') }}</td>
                    </tr>
                    @if($additionalAmount > 0)
                        <tr>
                            <td><strong>{{ $additionalDescription }}</strong></td>
                            <td style="text-align: right;">Rp {{ number_format($additionalAmount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="total-row" style="background-color: #f8f9f9;">
                        <td><strong>Total Mukafaah</strong></td>
                        <td style="text-align: right;"><strong>Rp {{ number_format($totalMukafaahBase + $additionalAmount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        @endforeach
    @else
        <div class="pengajar-section">
            <div class="letterhead">
                <h1>Lampiran Presensi</h1>
                <p>Periode: {{ $administrasi->bulan }} {{ $administrasi->tahun }}</p>
            </div>
            <p>Tidak ada data rekap presensi untuk periode {{ $administrasi->bulan }} {{ $administrasi->tahun }}.</p>
        </div>
    @endif
</body>
</html>
