@extends('layouts.pengajar')

@section('title', 'Kehadiran')

@section('content')
    <div class="container mx-auto p-6">
        <!-- Rekap Kehadiran Bulan Ini -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Rekap Kehadiran {{ $monthlyRecap['month_name'] }}</h2>
            <div class="bg-green-50 p-4 rounded-lg inline-block">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Kehadiran</h3>
                <p class="text-3xl font-bold text-green-600">{{ $monthlyRecap['present_days'] }}</p>
            </div>
        </div>

        <!-- Riwayat Presensi Bulan Ini -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Riwayat Presensi Bulan Ini</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Hari Mengajar</th>
                            <th class="px-4 py-3">Waktu Kedatangan</th>
                            <th class="px-4 py-3">Waktu Selesai</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse($monthlyPresences as $index => $presence)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($presence->date)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $presence->day }}</td>
                                <td class="px-4 py-3">{{ $presence->arrival_time }}</td>
                                <td class="px-4 py-3">{{ $presence->end_time }}</td>
                                <td class="px-4 py-3">{{ $presence->class }}</td>
                                <td class="px-4 py-3">
                                    @if($presence->proof)
                                        <img src="{{ asset('storage/' . $presence->proof) }}"
                                             alt="Bukti"
                                             class="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-80"
                                             onclick="showProof('{{ asset('storage/' . $presence->proof) }}')">
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data presensi bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Presensi Keseluruhan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Riwayat Presensi Keseluruhan</h2>
            <form method="GET" action="{{ route('attendance-pengajar') }}">
                <div class="md:flex md:justify-between mb-6 overflow-auto">
                    <div class="flex space-x-4 mb-6 lg:mb-2">
                        <select name="year" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" {{ $year == $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                        <select name="month" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                            <option value="">Semua Bulan</option>
                            <option value="1" {{ $month == '1' ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ $month == '2' ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ $month == '3' ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ $month == '4' ? 'selected' : '' }}>April</option>
                            <option value="5" {{ $month == '5' ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ $month == '6' ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ $month == '7' ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ $month == '8' ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ $month == '9' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ $month == '10' ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ $month == '11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ $month == '12' ? 'selected' : '' }}>Desember</option>
                        </select>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 text-sm">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('attendance-pengajar') }}" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabel Riwayat Presensi Keseluruhan -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Hari Mengajar</th>
                            <th class="px-4 py-3">Waktu Kedatangan</th>
                            <th class="px-4 py-3">Waktu Selesai</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse($presenceHistory as $index => $presence)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">{{ $presenceHistory->firstItem() + $index }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($presence->date)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $presence->day }}</td>
                                <td class="px-4 py-3">{{ $presence->arrival_time }}</td>
                                <td class="px-4 py-3">{{ $presence->end_time }}</td>
                                <td class="px-4 py-3">{{ $presence->class }}</td>
                                <td class="px-4 py-3">
                                    @if($presence->proof)
                                        <img src="{{ asset('storage/' . $presence->proof) }}"
                                             alt="Bukti"
                                             class="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-80"
                                             onclick="showProof('{{ asset('storage/' . $presence->proof) }}')">
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data presensi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($presenceHistory->hasPages())
                <div class="flex items-center justify-between mt-4 mb-6">
                    <span class="text-sm text-gray-700">
                        Menampilkan {{ $presenceHistory->firstItem() }} - {{ $presenceHistory->lastItem() }}
                        dari {{ $presenceHistory->total() }} data
                    </span>
                    <div class="flex space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($presenceHistory->onFirstPage())
                            <span class="px-4 py-2 text-sm text-gray-400 bg-gray-200 rounded-md cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <a href="{{ $presenceHistory->appends(request()->query())->previousPageUrl() }}"
                               class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Previous
                            </a>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($presenceHistory->hasMorePages())
                            <a href="{{ $presenceHistory->appends(request()->query())->nextPageUrl() }}"
                               class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Next
                            </a>
                        @else
                            <span class="px-4 py-2 text-sm text-gray-400 bg-gray-200 rounded-md cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal untuk menampilkan bukti presensi -->
    <div id="proof-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Bukti Presensi</h2>
                <button onclick="closeProofModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-center">
                <img id="proof-image" src="" alt="Bukti Presensi" class="max-w-full h-auto rounded-lg">
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closeProofModal()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function showProof(imageSrc) {
            document.getElementById('proof-image').src = imageSrc;
            document.getElementById('proof-modal').classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proof-modal').classList.add('hidden');
        }

        // Tutup modal ketika klik di luar modal
        document.getElementById('proof-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProofModal();
            }
        });
    </script>
@endsection
