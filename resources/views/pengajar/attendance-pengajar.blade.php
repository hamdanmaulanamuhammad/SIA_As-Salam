@extends('layouts.pengajar')

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
                        <th class="px-4 py-3">Keterangan</th>
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
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 w-8 text-white bg-green-600 rounded-md" onclick="showAttendanceOptions()">
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
                        <td class="px-4 py-3">Izin</td>
                        <td class="px-4 py-3">
                        </td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">3</td>
                        <td class="px-4 py-3">Acara 3</td>
                        <td class="px-4 py-3">09:00 AM, 10/01/2024</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Selesai</span>
                        </td>
                        <td class="px-4 py-3">Terlambat 10 menit</td>
                        <td class="px-4 py-3">
                            
                        </td>
                    </tr>
                    <!-- Tambahkan lebih banyak baris sesuai kebutuhan -->
                </tbody>                            
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 mb-10">
            <span class="text-sm text-gray-700">Showing 1-3 of 3 entries</span>
            <div>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Previous</button>
                <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Pilihan Kehadiran -->
    <div id="attendance-options-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-option-modal" class="float-right cursor-pointer text-gray-500" onclick="closeOptionModal()">&times;</span>
            <h2 class="text-lg font-semibold">Pilih Opsi Kehadiran</h2>
            <div class="mt-4">
                <button class="w-full mb-2 px-4 py-2 text-white bg-green-600 rounded-md" onclick="openAttendanceModal()">Presensi</button>
                <button class="w-full px-4 py-2 text-white bg-blue-600 rounded-md" onclick="openLeaveModal()">Izin</button>
            </div>
            <button class="mt-4 text-gray-600" onclick="closeAttendanceOptionsModal()">Batal</button>
        </div>
    </div>

    <!-- Modal Presensi -->
    <div id="attendance-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-modal" class="float-right cursor-pointer text-gray-500" onclick="closeAttendanceModal()">&times;</span>
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
                <button class="mr-2 text-gray-600 border border-gray-300 rounded-md px-4 py-2" onclick="closeAttendanceModal()">Batal</button>
                <button class="bg-blue-600 text-white rounded-md px-4 py-2" onclick="confirmAttendance()">Presensi</button>
            </div>
        </div>
    </div>

    <!-- Modal Izin -->
    <div id="leave-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-leave-modal" class="float-right cursor-pointer text-gray-500" onclick="closeLeaveModal()">&times;</span>
            <h2 class="text-lg font-semibold">Form Izin</h2>
            <div class="mt-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Subjek Izin</label>
                    <select id="leave-subject" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        <option value="">Pilih Subjek Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="acara">Acara</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi Izin (opsional)</label>
                    <textarea id="leave-description" class="mt-1 block w-full border border-gray-300 rounded-md p-2" rows="3"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">File Pendukung (wajib)</label>
                    <input type="file" id="supporting-file" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button class="mr-2 text-gray-600 border border-gray-300 rounded-md px-4 py-2" onclick="closeLeaveModal()">Batal</button>
                <button class="bg-blue-600 text-white rounded-md px-4 py-2" onclick="submitLeave()">Kirim Izin</button>
            </div>
        </div>
    </div>

    <script>
        function showAttendanceOptions() {
            document.getElementById('attendance-options-modal').classList.remove('hidden');
        }

        function closeAttendanceOptionsModal() {
            document.getElementById('attendance-options-modal').classList.add('hidden');
        }

        function closeOptionModal(){
            document.getElementById('attendance-options-modal').classList.add('hidden');
        }

        function openAttendanceModal() {
            closeAttendanceOptionsModal();
            document.getElementById('attendance-modal').classList.remove('hidden');
        }

        function closeAttendanceModal() {
            document.getElementById('attendance-modal').classList.add('hidden');
        }

        function openLeaveModal() {
            closeAttendanceOptionsModal();
            document.getElementById('leave-modal').classList.remove('hidden');
        }

        function closeLeaveModal() {
            document.getElementById('leave-modal').classList.add('hidden');
        }

        function confirmAttendance() {
            closeAttendanceModal();
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Anda telah berhasil melakukan presensi.',
                confirmButtonText: 'Tutup'
            });
        }

        function submitLeave() {
            closeLeaveModal();
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Permohonan izin telah dikirim.',
                confirmButtonText: 'Tutup'
            });
        }
    </script>
@endsection
