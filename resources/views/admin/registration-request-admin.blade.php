@extends('layouts.admin')

@section('title', 'Registrasi')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-6">Data Permintaan Registrasi</h2>
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
        <!-- Tabel Permintaan Pendaftaran -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No Telp</th>
                        <th class="px-4 py-3">Waktu Registrasi</th>
                        <th class="px-4 py-3">Tempat Kuliah</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3 min-w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">NULL</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">john.doe@example.com</td>
                        <td class="px-4 py-3">08123456789</td>
                        <td class="px-4 py-3">01/01/2024</td>
                        <td class="px-4 py-3">Universitas A</td>
                        <td class="px-4 py-3">Jl. Contoh No. 1</td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 w-8 text-white bg-green-600 rounded-md" onclick="confirmAccept('John Doe')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="px-2 py-1 w-8 text-white bg-red-600 rounded-md" onclick="confirmReject('John Doe')">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>                                                                     
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">NULL</td>
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">jane.smith@example.com</td>
                        <td class="px-4 py-3">08234567890</td>
                        <td class="px-4 py-3">02/02/2024</td>
                        <td class="px-4 py-3">Universitas B</td>
                        <td class="px-4 py-3">Jl. Contoh No. 2</td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 w-8 text-white bg-green-600 rounded-md" onclick="confirmAccept('Jane Smith')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="px-2 py-1 w-8 text-white bg-red-600 rounded-md" onclick="confirmReject('Jane Smith')">
                                <i class="fas fa-times"></i>
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

    <!-- Modal Konfirmasi Terima Pendaftaran -->
    <div id="accept-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-accept-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h3 class="text-lg font-semibold">Konfirmasi Terima Pendaftaran</h3>
            <p class="mt-2">Masukkan NIP untuk pengajar <span id="accept-teacher-name"></span>:</p>
            <input type="text" id="nip-input" class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="NIP" required />
            <div class="flex justify-end mt-4">
                <button id="cancel-accept" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                <button id="confirm-accept" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Terima</button>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk mengonfirmasi penerimaan pendaftaran
        function confirmAccept(name) {
            // Menampilkan modal konfirmasi
            document.getElementById('accept-teacher-name').textContent = name;
            document.getElementById('accept-modal').classList.remove('hidden');

            // Menangani tombol batal
            document.getElementById('cancel-accept').onclick = () => {
                document.getElementById('accept-modal').classList.add('hidden');
            };

            // Menangani tombol terima
            document.getElementById('confirm-accept').onclick = () => {
                const nip = document.getElementById('nip-input').value;
                if (nip) {
                    // Logika untuk menerima pendaftaran
                    Swal.fire('Sukses!', `Pendaftaran pengajar ${name} dengan NIP ${nip} telah diterima.`, 'success');
                    document.getElementById('accept-modal').classList.add('hidden');
                } else {
                    Swal.fire('Error!', 'NIP tidak boleh kosong!', 'error');
                }
            };

            // Menangani tombol close
            document.getElementById('close-accept-modal').onclick = () => {
                document.getElementById('accept-modal').classList.add('hidden');
            };
        }

        // Fungsi untuk mengonfirmasi penolakan pendaftaran
        function confirmReject(name) {
            Swal.fire({
                title: 'Konfirmasi Tolak Pendaftaran',
                text: `Apakah Anda yakin ingin menolak pendaftaran pengajar ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Logika untuk menolak pendaftaran
                    Swal.fire('Sukses!', `Pendaftaran pengajar ${name} telah ditolak.`, 'success');
                    // Tambahkan logika untuk memperbarui tabel jika diperlukan
                }
            });
        }
    </script>
@endsection
