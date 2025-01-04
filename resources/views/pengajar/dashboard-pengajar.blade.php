@extends('layouts.pengajar')

@section('title', 'Dashboard Pengajar')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="text-2xl font-semibold text-gray-700 mt-8">Statistik Bulan Januari</h2>
        <!-- Cards -->
        <div class="grid gap-6 mb-16 md:grid-cols-1 xl:grid-cols-3 mt-6">
            <!-- Card 1: Total Acara -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-3 mr-4 bg-green-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total-events.svg') }}" alt="Total Kehadiran" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Acara</p>
                    <p class="text-lg font-semibold text-gray-700">20</p>
                </div>
            </div>

            <!-- Card 2: Total Kehadiran -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-3 mr-4 bg-yellow-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/attend.svg') }}" alt="Total Acara" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Kehadiran</p>
                    <p class="text-lg font-semibold text-gray-700">5</p>
                </div>
            </div>

            <!-- Card 3: Performa -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-3 mr-4 text-purple-500 bg-blue-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/performance.svg') }}" alt="Performa" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Performa</p>
                    <p class="text-lg font-semibold text-gray-700">90%</p>
                </div>
            </div>
        </div>

        <!-- Card Acara yang Sedang Berlangsung -->
        <div id="event-info" class="mb-6">
            <div class="bg-white rounded-lg shadow p-4 flex justify-between border border-gray-200">
                <div class="mb-2">
                    <div class="flex items-center mb-2">
                        <h3 class="text-lg font-semibold text-gray-800" id="event-name-display">Nama Acara</h3>
                        <p class="text-lg font-semibold text-gray-800 ml-1">Sedang Berlangsung</p>
                    </div>
                    
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-800 mb-2">Terlambat dalam</p>
                            <span class="border bg-yellow-500 text-white text-sm font-bold rounded-md p-2" id="event-late-timer">
                                00:09:54
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800 mb-2">Presensi berakhir dalam</p>
                            <span class="border bg-red-600 text-white text-sm font-bold rounded-md p-2" id="event-presence-timer">
                                00:09:54
                            </span>
                        </div>
                    </div>
                </div>

                <button id="attendance-button" class="my-auto w-8 h-8 text-white bg-green-600 rounded-md" onclick="openModal()">
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Tabel Riwayat Acara -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Acara</h3>
        <!-- Filter dan Entries -->
        <div class="md:flex md:justify-between mb-6">
            <div class="flex space-x-4 mb-6">
                <select id="year-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Tahun</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                </select>
                <select id="month-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
                <button id="filter-button" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 text-sm">Terapkan Filter</button>
            </div>
            <div class="flex items-center">
                <label for="entries" class="text-sm font-medium text-gray-700">Show</label>
                <select id="entries" class="ml-2 border border-gray-300 rounded-md">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
                <span class="ml-2 text-sm text-gray-600">entries</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Acara</th>
                        <th class="px-4 py-3">Waktu Pelaksanaan</th>
                        <th class="px-4 py-3">Status Kehadiran</th>
                        <th class="px-4 py-3">Waktu Presensi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <!-- Data Acara Dummy -->
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">Acara 1</td>
                        <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                        <td class="px-4 py-3">Hadir</td>
                        <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">Acara 2</td>
                        <td class="px-4 py-3">11:00 AM, 15/01/2024</td>
                        <td class="px-4 py-3">Izin</td>
                        <td class="px-4 py-3">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 mb-10">
            <span class="text-sm text-gray-700">Showing 1-2 of 2 entries</span>
            <div>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Previous</button>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Presensi -->
    <div id="attendance-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-modal" class="float-right cursor-pointer text-gray-500" onclick="closeModal()">&times;</span>
            <h2 class="text-lg font-semibold">Informasi Presensi</h2>
            <div class="mt-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">NIP</label>
                    <input type="text" id="nip-display" class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="123456789" readonly />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name-display" class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="Nama Pengajar" readonly />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Waktu Kehadiran</label>
                    <input type="text" id="timestamp-display" class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="2024-01-01 10:00 AM" readonly />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button class="mr-2 text-gray-600 border border-gray-300 rounded-md px-4 py-2" onclick="closeModal()">Batal</button>
                <button class="bg-blue-600 text-white rounded-md px-4 py-2" onclick="confirmAttendance()">Presensi</button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('attendance-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('attendance-modal').classList.add('hidden');
        }

        function confirmAttendance() {
            // Menutup modal presensi
            closeModal();
            // Menampilkan modal sukses menggunakan SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Anda telah berhasil melakukan presensi.',
                confirmButtonText: 'Tutup'
            });
        }
    </script>
@endsection
