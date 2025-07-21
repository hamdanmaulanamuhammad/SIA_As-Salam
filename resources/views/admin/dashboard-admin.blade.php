@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="text-2xl font-semibold text-gray-700 mt-8">Statistik Bulan {{ now()->monthName }}</h2>
        <!-- Cards -->
        <div class="grid gap-6 mb-16 md:grid-cols-2 xl:grid-cols-4 mt-6">
            <!-- Card 1: Pengeluaran Bulan Ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/pengeluaran-bulan-ini.svg') }}" alt="Pengeluaran Bulan Ini" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Pengeluaran Bulan Ini</p>
                    <p class="text-lg font-semibold text-gray-700">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Card 2: Total Pengajar -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total-pengajar.svg') }}" alt="Total Pengajar" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Pengajar</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $totalPengajar }}</p>
                </div>
            </div>

            <!-- Card 3: Saldo Akhir Tahun Ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/saldo-akhir.svg') }}" alt="Saldo Akhir Tahun Ini" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Saldo Akhir Tahun Ini</p>
                    <p class="text-lg font-semibold text-gray-700">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Card 4: Jumlah Santri Aktif -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/santri-aktif.svg') }}" alt="Jumlah Santri Aktif" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Jumlah Santri Aktif</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $totalSantriAktif }}</p>
                </div>
            </div>
        </div>


        <!-- Tabel Presensi Hari Ini -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Presensi Hari Ini ({{ now()->format('d/m/Y') }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Nama Pengajar</th>
                        <th class="px-4 py-3">Hari Mengajar</th>
                        <th class="px-4 py-3">Waktu Kedatangan</th>
                        <th class="px-4 py-3">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($presences as $index => $presence)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ $presence->created_at->format('H:i, d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $presence->user->full_name }}</td>
                            <td class="px-4 py-3">{{ $presence->date }}</td>
                            <td class="px-4 py-3">{{ $presence->arrival_time }}</td>
                            <td class="px-4 py-3">{{ $presence->end_time }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-600">Tidak ada data presensi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
