@extends('layouts.admin')

@section('title', 'Rekap Data')

@section('content')
    <div class="container px-6 mx-auto">
        <h2 class="text-2xl font-semibold text-gray-700 mt-8 mb-6">Periode : Januari 2025</h2>
        
        <!-- Kartu Pengajar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800">NIP : 123456</h3>
            <h3 class="text-lg font-semibold text-gray-800">Nama : John Doe</h3>
    
            <!-- Tabel Rekap Performa -->
            <h3 class="text-lg font-semibold text-gray-800 mt-4">Rekap Performa</h3>
            <div class="overflow-x-auto">
                <table class="w-full mt-4 bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Hadir</th>
                            <th class="px-4 py-3">Terlambat</th>
                            <th class="px-4 py-3">Izin</th>
                            <th class="px-4 py-3">Tidak Hadir</th>
                            <th class="px-4 py-3">Skor Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">20</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">3</td>
                            <td class="px-4 py-3">85</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <!-- Detail Kehadiran -->
            <h3 class="text-lg font-semibold text-gray-800 mt-4">Detail Kehadiran</h3>
            <div class="overflow-x-auto">
                <table class="w-full mt-4 bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Nama Acara</th>
                            <th class="px-4 py-3">Waktu Acara</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">Acara 1</td>
                            <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Hadir</span>
                            </td>
                        </tr>
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">Acara 2</td>
                            <td class="px-4 py-3">11:00 AM, 15/01/2024</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">5 menit</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <!-- Gaji -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800">Mukafaah</h3>
                <p class="text-sm text-gray-600">Nominal Mukafaah: Rp 500.000</p>
            </div>
        </div>

        <!-- Tambahkan kartu pengajar lainnya di sini -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800">NIP : 654321</h3>
            <h3 class="text-lg font-semibold text-gray-800">Nama : Jane Smith</h3>
    
            <!-- Tabel Rekap Performa -->
            <h3 class="text-lg font-semibold text-gray-800 mt-4">Rekap Performa</h3>
            <div class="overflow-x-auto">
                <table class="w-full mt-4 bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Hadir</th>
                            <th class="px-4 py-3">Terlambat</th>
                            <th class="px-4 py-3">Izin</th>
                            <th class="px-4 py-3">Tidak Hadir</th>
                            <th class="px-4 py-3">Skor Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">18</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">4</td>
                            <td class="px-4 py-3">80</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <!-- Detail Kehadiran -->
            <h3 class="text-lg font-semibold text-gray-800 mt-4">Detail Kehadiran</h3>
            <div class="overflow-x-auto">
                <table class="w-full mt-4 bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Nama Acara</th>
                            <th class="px-4 py-3">Waktu Acara</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">Acara 3</td>
                            <td class="px-4 py-3">09:00 AM, 10/01/2024</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Hadir</span>
                            </td>
                        </tr>
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">Acara 4</td>
                            <td class="px-4 py-3">10:30 AM, 12/01/2024</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">10 menit</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <!-- Gaji -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800">Mukafaah</h3>
                <p class="text-sm text-gray-600">Nominal Mukafaah: Rp 450.000</p>
            </div>
        </div>
    </div>
@endsection
