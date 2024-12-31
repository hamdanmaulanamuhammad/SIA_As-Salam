@extends('layouts.admin')

@section('title', 'Kehadiran')

@section('content')
    <div class="container mx-auto p-6">
        <!-- Filter dan Entries -->
        <div class="md:flex md:justify-between mb-6">
            <div class="flex space-x-4 mb-6 lg:mb-2">
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
                <button class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 text-sm">Terapkan Filter</button>
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

        <!-- Tabel Acara -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Acara</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">Acara 1</td>
                        <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Berlangsung</span>
                        </td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 w-8 text-white bg-green-600 rounded-md" onclick="window.location.href='{{ route('manual-attendance-admin') }}'">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">Acara 2</td>
                        <td class="px-4 py-3">11:00 AM, 15/01/2024</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Selesai</span>
                        </td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 w-8 text-white bg-red-600 rounded-md" disabled>
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Tambahkan lebih banyak baris sesuai kebutuhan -->
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
@endsection