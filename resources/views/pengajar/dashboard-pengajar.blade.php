@extends('layouts.pengajar')

@section('title', 'Dashboard')

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

        <!-- Card Presensi -->
        <div id="event-info" class="mb-6">
            <div class="bg-white rounded-lg shadow py-8 px-6 flex justify-between border border-gray-200">
                <div class="mb-2">
                    <h3 class="text-xl font-semibold text-gray-800">Presensi</h3>
                </div>

                <button id="attendance-button" class="my-auto w-8 h-8 text-white bg-green-600 rounded-md" onclick="openModal()">
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

    <!-- Tabel Riwayat Presensi Hari ini -->
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tabel Kehadiran Hari Ini</h3>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Timestamp</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Waktu Kedatangan</th>
                    <th class="px-4 py-3">Hari Mengajar</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <!-- Data Acara Dummy -->
                <tr class="text-gray-700">
                    <td class="px-4 py-3">1</td>
                    <td class="px-4 py-3">10:00 AM, 01/01/2024</td>
                    <td class="px-4 py-3">John Doe</td>
                    <td class="px-4 py-3">04:22:00</td>
                    <td class="px-4 py-3">2025-02-11</td>
                    <td class="px-4 py-3">Hadir</td>
                    <td class="px-4 py-3 min-w-24">
                        <button class="text-blue-500 hover:text-blue-700 mr-2">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="text-red-500 hover:text-red-700">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk Presensi/Izin -->
    <div id="presence-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-presence-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold">Pilih Jenis Presensi</h2>
            <div class="flex justify-center mt-4">
                <button id="presence-option" class="px-4 py-2 mr-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700">Presensi</button>
                <button id="leave-option" class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">Izin</button>
            </div>
        </div>
    </div>

    <!-- Modal untuk Form Presensi -->
    <div id="presence-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
                <span id="close-presence-form-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
                <h2 class="text-lg font-semibold">Form Presensi</h2>
                <form id="presence-form" action="{{ route('presence.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="presence-id" name="id">
                    <input type="hidden" name="_method" id="presence-method" value="POST">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengajar</label>
                        <input type="text" id="user_id" name="user_id" value="{{ auth()->user()->full_name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" readonly />
                    </div>
                    <input type="hidden" name="type" value="presence">
                    <div class="mb-4">
                        <label for="teaching-day" class="block text-sm font-medium text-gray-700">Hari Mengajar</label>
                        <input type="date" id="teaching-day" name="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required value="{{ now()->format('Y-m-d') }}" />
                    </div>
                    <div class="mb-4">
                        <label for="arrival-time" class="block text-sm font-medium text-gray-700">Waktu Kedatangan</label>
                        <input type="time" id="arrival-time" name="arrival_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label for="end-time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" id="end-time" name="end_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label for="class" class="block text-sm font-medium text-gray-700">Kelas yang Diajar</label>
                        <select id="class" name="class" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            <option value="Mustawa 1">Mustawa 1</option>
                            <option value="Mustawa 2">Mustawa 2</option>
                            <option value="Mustawa 3">Mustawa 3</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="material" class="block text-sm font-medium text-gray-700">Materi yang Diajarkan</label>
                        <textarea id="material" name="material" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="presence-proof" class="block text-sm font-medium text-gray-700">Bukti Mengajar</label>
                        <input type="file" id="presence-proof" name="proof" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" accept="image/*" required/>
                        <input type="hidden" id="presence-proof-existing" name="proof_existing">
                        <img id="presence-proof-preview" class="mt-2 w-full rounded-md shadow-md hidden" alt="Bukti Izin Sebelumnya">
                    </div>
                    <div class="mb-4">
                        <label for="issues" class="block text-sm font-medium text-gray-700">Kendala (Opsional)</label>
                        <textarea id="issues" name="issues" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" id="cancel-presence-form-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal untuk Form Izin -->
        <div id="leave-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
                <span id="close-leave-form-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
                <h2 class="text-lg font-semibold" id="leave-form-title">Form Izin</h2>
                <form id="leave-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="leave-form-method" value="POST"> <!-- Untuk Edit -->
                    <input type="hidden" name="type" value="leave">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengajar</label>
                        <input type="text" id="user_id" name="user_id" value="{{ auth()->user()->full_name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="teaching-day" class="block text-sm font-medium text-gray-700">Hari Mengajar</label>
                        <input type="date" id="teaching-day" name="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required value="{{ now()->format('Y-m-d') }}" />
                    </div>
                    <div class="mb-4">
                        <label for="leave-subject" class="block text-sm font-medium text-gray-700">Subjek Izin</label>
                        <input type="text" id="leave-subject" name="leave_reason" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />
                    </div>

                    <div class="mb-4">
                        <label for="leave-proof" class="block text-sm font-medium text-gray-700">Bukti Izin</label>
                        <input type="file" id="leave-proof" name="proof" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" accept="image/*" required />
                        <input type="hidden" id="leave-proof-existing" name="proof_existing">
                        <img id="leave-proof-preview" class="mt-2 w-full rounded-md shadow-md hidden" alt="Bukti Izin Sebelumnya">
                    </div>

                    <div class="flex justify-end">
                        <button type="button" id="cancel-leave-form-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="submit" id="leave-form-submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

@endsection

@section('scripts')
<script>
    // Fungsi untuk membuka modal opsi
    function openModal() {
        document.getElementById('presence-modal').classList.remove('hidden');
    }

    // Fungsi untuk menutup semua modal
    function closeModal() {
        document.getElementById('presence-modal').classList.add('hidden');
        document.getElementById('presence-form-modal').classList.add('hidden');
        document.getElementById('leave-form-modal').classList.add('hidden');
    }

    // Menambahkan event listener untuk tombol-tombol penutup modal
    document.getElementById('close-presence-modal').addEventListener('click', closeModal);
    document.getElementById('close-presence-form-modal').addEventListener('click', closeModal);
    document.getElementById('close-leave-form-modal').addEventListener('click', closeModal);

    // Menambahkan event listener untuk tombol batal pada form
    document.getElementById('cancel-presence-form-button').addEventListener('click', closeModal);
    document.getElementById('cancel-leave-form-button').addEventListener('click', closeModal);

    // Menambahkan event listener untuk pilihan presensi
    document.getElementById('presence-option').addEventListener('click', function() {
        closeModal();
        document.getElementById('presence-form-modal').classList.remove('hidden');
    });

    // Menambahkan event listener untuk pilihan izin
    document.getElementById('leave-option').addEventListener('click', function() {
        closeModal();
        document.getElementById('leave-form-modal').classList.remove('hidden');
    });

    // Menambahkan event listener untuk preview gambar pada form presensi
    document.getElementById('presence-proof').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('presence-proof-preview').src = e.target.result;
                document.getElementById('presence-proof-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('presence-proof-preview').classList.add('hidden');
        }
    });

    // Menambahkan event listener untuk preview gambar pada form izin
    document.getElementById('leave-proof').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('leave-proof-preview').src = e.target.result;
                document.getElementById('leave-proof-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('leave-proof-preview').classList.add('hidden');
        }
    });

    // Menambahkan event listener untuk form presensi
    document.getElementById('presence-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // Simpan data ke server atau lakukan proses lainnya
        // Setelah berhasil, tampilkan pesan sukses menggunakan SweetAlert
        Swal.fire({
            title: 'Berhasil!',
            text: 'Presensi berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                closeModal();
                // Lakukan tindakan lain setelah menutup modal jika diperlukan
            }
        });
    });

    // Menambahkan event listener untuk form izin
    document.getElementById('leave-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // Simpan data ke server atau lakukan proses lainnya
        // Setelah berhasil, tampilkan pesan sukses menggunakan SweetAlert
        Swal.fire({
            title: 'Berhasil!',
            text: 'Izin berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                closeModal();
                // Lakukan tindakan lain setelah menutup modal jika diperlukan
            }
        });
    });
</script>
@endsection

