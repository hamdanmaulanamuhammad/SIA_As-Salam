@extends('layouts.admin')

@section('title', 'Pengajar')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-4">Detail Pengajar</h2>
        <!-- Filter dan Entries -->
        <div class="md:flex md:justify-between">
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
        <!-- Tabel Detail Pengajar -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Waktu Bergabung</th>
                        <th class="px-4 py-3">Tempat Kuliah</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">123456</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">john.doe@example.com</td>
                        <td class="px-4 py-3">08123456789</td>
                        <td class="px-4 py-3">01/01/2020</td>
                        <td class="px-4 py-3">Universitas A</td>
                        <td class="px-4 py-3">Jl. Contoh No. 1</td>
                        <td class="px-4 py-3">
                            <button class="text-red-600" onclick="confirmDelete('123456', 'John Doe')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">654321</td>
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">jane.smith@example.com</td>
                        <td class="px-4 py-3">08234567890</td>
                        <td class="px-4 py-3">02/02/2020</td>
                        <td class="px-4 py-3">Universitas B</td>
                        <td class="px-4 py-3">Jl. Contoh No. 2</td>
                        <td class="px-4 py-3">
                            <button class="text-red-600" onclick="confirmDelete('654321', 'Jane Smith')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Tambahkan lebih banyak baris sesuai kebutuhan -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-4 mb-10">
                <span class="text-sm text-gray-700">Showing 1-3 of 10 entries</span>
                <div>
                    <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Previous</button>
                    <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Next</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fungsi untuk mengonfirmasi penghapusan pengajar
        function confirmDelete(nip, name) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus pengajar ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Logika untuk menghapus pengajar
                    Swal.fire('Dihapus!', `Pengajar ${name} telah dihapus.`, 'success');
                    // Tambahkan logika untuk memperbarui tabel jika diperlukan
                }
            });
        }

        // Inisialisasi SweetAlert jika diperlukan
        document.addEventListener('DOMContentLoaded', function() {
            // Kode inisialisasi lainnya jika diperlukan
        });
    </script>
@endsection
