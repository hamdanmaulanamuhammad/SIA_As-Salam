<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icons/favicon.png') }}">
    <title>E-Rapor Santri - {{ $santri->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Times New Roman';
            margin: 0;
            padding: 20px;
            background-color: #f7fafc;
            min-width: 760px !important;
        }
        .container {
            background: white;
            max-width: 800px !important;
            min-width: 760px !important;
            min-height: 1130px;
            margin: 0 auto;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .pdf-content {
            width: 760px;
            margin: 15px auto;
            padding: 20px;
            box-sizing: border-box;
            font-size: 9pt;
        }
        .logo-container {
            width: 150px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .logo-placeholder {
            font-size: 10px;
            text-align: center;
            padding: 5px;
        }
        .signature-container {
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            margin: 5px auto;
        }
        .signature-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .signature-placeholder {
            font-size: 8px;
            text-align: center;
        }
        .cap-container {
            width: 180px;
            height: 100px;
            display: flex;
            position: absolute;
            top: 30px;
            left: 1px;
            z-index: 1000;
            overflow: visible;
        }
        .cap-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .cap-placeholder {
            color: #999;
            font-size: 8px;
        }
        .mudir-signature-area {
            position: relative;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 2px;
            text-align: center;
            box-sizing: border-box;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .container {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                box-shadow: none;
            }
            .pdf-content {
                width: 190mm;
                margin: 0 auto;
                padding: 10mm;
            }
            table {
                page-break-inside: avoid;
            }
            .no-break {
                page-break-inside: avoid;
            }
            @page {
                size: A4;
                margin: 0;
            }
            .logo-container, .signature-container, .cap-container {
                border: none !important;
                background-color: transparent !important;
            }
            .logo-placeholder, .signature-placeholder, .cap-placeholder {
                display: none !important;
            }
            .button-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="text-center mb-6">
        <div class="button-container flex justify-center gap-4 w-full">
            <button id="downloadPdf" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Download PDF
            </button>
            <a href="{{ route('pengajar.rapor.show', [$kelasSemester->id, $santri->id]) }}"
               class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </div>

    <div class="container">
        <div class="pdf-content" id="rapor-content">
            <div class="flex items-center justify-center gap-x-5">
                <div class="flex-shrink-0">
                    <div class="logo-container">
                        <img src="{{ asset('assets/cap-logo-rapor/logo-kop.png') }}" alt="Logo TPA">
                    </div>
                </div>
                <div class="text-center">
                    <h1 class="text-lg font-bold uppercase">Taman Pendidikan Al-Quran</h1>
                    <h2 class="text-lg font-bold uppercase">As-Salam Yogyakarta</h2>
                </div>
            </div>

            <div class="text-center text-[10px] mt-2">
                <p>Ngemplong Lor, Sariharjo, Ngaglik, Sleman, Daerah Istimewa Yogyakarta 55581</p>
                <p>
                    Telp. +628517983482 |
                    Email: <a href="mailto:info.tpaassalam@gmail.com" class="text-blue-600">info.tpaassalam@gmail.com</a>
                </p>
            </div>

            <div class="border-t-2 border-black mt-1 relative"></div>

            <div class="text-center mt-4 text-xl-center font-bold">
                <p>CAPAIAN KOMPETENSI SANTRI</p>
            </div>

            <div class="mt-2 font-[Times New Roman] text-[11px] leading-relaxed no-break">
                <div class="grid grid-cols-[max-content_1ch_1fr] gap-x-2 max-w-sm mb-2">
                    <p class="font-bold">Nama Santri</p>
                    <p>:</p>
                    <p>{{ $santri->nama_lengkap }}</p>

                    <p class="font-bold">NIS</p>
                    <p>:</p>
                    <p>{{ $santri->nis }}</p>

                    <p class="font-bold">Mustawa</p>
                    <p>:</p>
                    <p>{{ $kelasSemester->kelas->nama_kelas }}</p>
                </div>

                <table class="w-full border border-black text-center table-fixed mb-6">
                    <thead class="font-bold">
                        <tr>
                            <th class="border border-black px-2 py-1 w-[5px]" rowspan="2">No</th>
                            <th class="border border-black px-2 py-1 w-[85px]" rowspan="2">Bahan Ajar</th>
                            <th class="border border-black px-2 py-1 w-[85px]" colspan="3">Nilai</th>
                        </tr>
                        <tr>
                            <th class="border border-black py-1 w-16">Angka</th>
                            <th class="border border-black px-2 py-1 w-32" colspan="2">Terbilang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($mapelsByCategory as $category => $mapels)
                            <tr>
                                <td class="border border-black px-2 py-1 font-semibold text-center"></td>
                                <td class="border border-black px-2 py-1 font-semibold text-center">{{ $category }}</td>
                                <td class="border border-black px-2 py-1 text-center"></td>
                                <td class="border border-black px-2 py-1 text-center" colspan="2"></td>
                            </tr>
                            @foreach ($mapels as $mapel)
                                @php
                                    $nilaiRapor = $nilaiRaporDataWithTerbilang->where('kelas_mapel_semester_id', $mapel->id)->first();
                                    $nilai = $nilaiRapor ? $nilaiRapor->nilai : null;
                                    $terbilangNilai = $nilaiRapor ? $nilaiRapor->terbilang : null;
                                @endphp
                                @if ($nilai)
                                    <tr>
                                        <td class="border border-black px-2 py-1 text-center">{{ $no++ }}</td>
                                        <td class="border border-black px-2 py-1 text-left pl-4">{{ $mapel->mataPelajaran->nama_mapel }}</td>
                                        <td class="border border-black px-2 py-1 text-center">{{ $nilai }}</td>
                                        <td class="border border-black px-2 py-1 text-center" colspan="2">{{ $terbilangNilai ?? '-' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                        <tr class="font-semibold">
                            <td class="border border-black px-2 py-1 text-center" colspan="2">Jumlah Nilai</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $jumlahNilai ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center" colspan="2">{{ $jumlahNilaiTerbilang ?? '-' }}</td>
                        </tr>
                        <tr class="font-semibold">
                            <td class="border border-black px-2 py-1 text-center" colspan="2">Rata-rata</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $rataRata ? number_format($rataRata) : '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center" colspan="2">{{ $terbilang ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="w-full">
                    <div class="mb-3">
                        <p class="font-bold mb-2">Predikat :</p>
                        <div class="border border-black p-2 text-center font-semibold">{{ $predikat ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="flex border-black pt-2">
                            <div class="w-3/4 pr-3">
                                <p><span class="font-bold">Keputusan:</span><br>
                                Dengan memperhatikan hasil capaian kompetensi santri di atas, maka santri yang bersangkutan ditempatkan di:</p>
                            </div>
                            <div class="w-1/4 flex items-center justify-center border border-black font-bold text-center p-2">
                                {{ $catatanRapor && $catatanRapor->keputusan_kelas_id ? \App\Models\Kelas::find($catatanRapor->keputusan_kelas_id)->nama_kelas : $kelasSemester->kelas->nama_kelas }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p class="font-bold mb-2">Catatan:</p>
                        <div class="border border-black p-2 italic mb-2">
                            Dari Ustadzah :{{ $kelasSemester->waliKelas->full_name ?? 'Ustadzah' }}
                        </div>
                        <div class="border border-black p-2">
                            {{ $catatanRapor ? $catatanRapor->catatan : '-' }}
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-3 gap-4 text-center text-[10px] no-break">
                    <div class="flex flex-col justify-between h-32">
                        <div class="space-y-1">
                            <p>Wali Santri,</p>
                        </div>
                        <p>.......................................</p>
                    </div>
                    <div class="flex flex-col justify-between h-32">
                        <div class="space-y-1">
                            <p>Musyrifah Mustawa {{ $kelasSemester->kelas->nama_kelas }}</p>
                        </div>
                        <div class="signature-container">
                            @if ($kelasSemester->waliKelas->signature)
                                <img src="{{ asset('storage/' . $kelasSemester->waliKelas->signature) }}" alt="Tanda Tangan Musyrifah">
                            @else
                                <div class="signature-placeholder">Tanda tangan tidak tersedia</div>
                            @endif
                        </div>
                        <p class="font-bold">{{ $kelasSemester->waliKelas->full_name }}</p>
                    </div>
                    <div class="flex flex-col justify-between h-32 mudir-signature-area">
                        <div class="space-y-1">
                            <p>Mengetahui,</p>
                            <p>Mudir TPA As-Salam</p>
                        </div>
                        <div class="signature-container">
                            @if ($kelasSemester->mudir->signature)
                                <img src="{{ asset('storage/' . $kelasSemester->mudir->signature) }}" alt="Tanda Tangan Mudir">
                            @else
                                <div class="signature-placeholder">Tanda tangan tidak tersedia</div>
                            @endif
                        </div>
                        <div class="cap-container">
                            <img src="{{ asset('assets/cap-logo-rapor/cap-as-salam.png') }}" alt="Cap/Stempel">
                        </div>
                        <p class="font-bold">{{ $kelasSemester->mudir->full_name ?? 'Hamdan Maulana Muhammad' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function triggerPrint() {
            window.print(); // Membuka dialog cetak browser
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('downloadPdf').addEventListener('click', triggerPrint);
        });
    </script>
</body>
</html>
