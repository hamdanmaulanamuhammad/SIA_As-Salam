@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="text-2xl font-semibold text-gray-700 mt-8">Statistik Bulan Januari</h2>
        <!-- Cards -->
        <div class="grid gap-6 mb-16 md:grid-cols-2 xl:grid-cols-4 mt-6">
            <!-- Card 1: Total Pengajar -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total-teachers.svg') }}" alt="Total Pengajar" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Pengajar</p>
                    <p class="text-lg font-semibold text-gray-700">25</p>
                </div>
            </div>

            <!-- Card 2: Total Event Bulan Ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total-events.svg') }}" alt="Total Event Bulan Ini" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Acara</p>
                    <p class="text-lg font-semibold text-gray-700">5</p>
                </div>
            </div>

            <!-- Card 3: Estimasi Penggajian Bulan Ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/mukafaah.svg') }}" alt="Estimasi Penggajian Bulan Ini" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Estimasi Mukafaah</p>
                    <p class="text-lg font-semibold text-gray-700">Rp 10.000.000</p>
                </div>
            </div>

            <!-- Card 4: Total Kehadiran Bulan Ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/avg-attendance.svg') }}" alt="Total Kehadiran Bulan Ini" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Rata-rata Kehadiran</p>
                    <p class="text-lg font-semibold text-gray-700">95%</p>
                </div>
            </div>
        </div>

         <!-- Card Acara yang Sedang Berlangsung -->
         <div class="mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="mb-2">
                    <div class="flex items-center mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">TPA Senin</h3>
                        <p class="text-lg font-semibold text-gray-800 ml-1">Sedang Berlangsung</p>
                    </div>
                    
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-800 mb-2">Terlambat dalam</p>
                            <span class="border bg-yellow-500 text-white text-sm font-bold rounded-md p-2">
                                00:09:54
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800 mb-2">Presensi berakhir dalam</p>
                            <span class="border bg-red-600 text-white text-sm font-bold rounded-md p-2">
                                00:09:54
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Data Event Terakhir -->
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
                        <th class="px-4 py-3">Jumlah Hadir</th>
                        <th class="px-4 py-3">Tidak Hadir</th>
                        <th class="px-4 py-3">Izin</th>
                        <th class="px-4 py-3">Waktu Pelaksanaan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <!-- Data Acara Dummy -->
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">Acara 1</td>
                        <td class="px-4 py-3">20</td>
                        <td class="px-4 py-3">5</td>
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">Acara 2</td>
                        <td class="px-4 py-3">15</td>
                        <td class="px-4 py-3">3</td>
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">11:00 AM, 15/01/2024</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 mb-10">
            <span class="text-sm text-gray-700">Showing 1-3 of 10 entries</span>
            <div>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Previous</button>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Next</button>
            </div>
        </div>
    </div>
@endsection
