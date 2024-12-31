@extends('layouts.admin')

@section('title', 'Presensi Manual')

@section('content')
    <div class="container mx-auto p-6">
        <nav class="mb-4">
            <ol class="list-reset flex text-blue-600">
                <li><a href="{{ route('attendance-admin') }}" class="hover:underline">Kehadiran</a></li>
                <li><span class="mx-2">></span></li>
                <li><a href="#" class="hover:underline">Presensi Manual</a></li>
            </ol>
        </nav>
        <h2 class="text-2xl font-semibold text-gray-700 mt-8">Data Pengajar</h2>
        <!-- Tabel Presensi -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Status Presensi</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">123456</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Hadir</span>
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">654321</td>
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Belum</span>
                        </td>
                        <td class="px-4 py-3">
                            <button class="w-8 h-8 text-white bg-green-600 rounded-md show-presensi-form" data-nip="654321" data-name="Jane Smith">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">3</td>
                        <td class="px-4 py-3">789012</td>
                        <td class="px-4 py-3">Alice Johnson</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">Izin</span>
                        </td>
                        <td class="px-4 py-3">
                            <button class="w-8 h-8 text-white bg-yellow-500 rounded-md show-verification-form" data-nip="789012" data-name="Alice Johnson" data-subject="Sakit" data-file="file_pendukung.pdf">
                                <i class="fas fa-question"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">4</td>
                        <td class="px-4 py-3">345678</td>
                        <td class="px-4 py-3">Bob Brown</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Izin</span>
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Presensi -->
        <div id="presensi-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
                <span id="close-presensi-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
                <h3 class="text-lg font-semibold">Form Presensi</h3>
                <form id="presensi-form" class="mt-4">
                    <input type="hidden" id="nip-input" />
                    <div class="mb-4">
                        <label for="name-input" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="name-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 p-1" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="time-input" class="block text-sm font-medium text-gray-700">Waktu Presensi</label>
                        <input type="datetime-local" id="time-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 p-1" required />
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="cancel-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Presensi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Verifikasi Izin -->
        <div id="verification-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
                <span id="close-verification-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
                <h3 class="text-lg font-semibold">Detail Izin</h3>
                <form id="verification-form" class="mt-4">
                    <input type="hidden" id="verification-nip-input" />
                    <div class="mb-4">
                        <label for="verification-name-input" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="verification-name-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 p-1" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="leave-subject-input" class="block text-sm font-medium text-gray-700">Subjek Izin</label>
                        <input type="text" id="leave-subject-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 p-1" readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Lampiran File Pendukung</label>
                        <a id="supporting-file-link" href="#" class="text-blue-600 hover:underline">Download File</a>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="cancel-verification-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="button" id="approve-button" class="px-4 py-2 mr-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Verifikasi</button>
                        <button type="button" id="reject-button" class="px-4 py-2 text-sm mr-2 text-white bg-red-600 rounded-md hover:bg-red-700">Tolak</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tombol Selesai -->
        <div class="mt-6">
            <button id="finish-button" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Akhiri Presensi</button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // JavaScript untuk interaksi
        document.getElementById('cancel-button').addEventListener('click', function() {
            document.getElementById('presensi-modal').classList.add('hidden');
        });

        document.getElementById('presensi-form').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Berhasil menambahkan presensi.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            document.getElementById('presensi-modal').classList.add('hidden');
        });

        document.getElementById('cancel-verification-button').addEventListener('click', function() {
            document.getElementById('verification-modal').classList.add('hidden');
        });

        document.getElementById('approve-button').addEventListener('click', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Izin telah diverifikasi.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            document.getElementById('verification-modal').classList.add('hidden');
        });

        document.getElementById('reject-button').addEventListener('click', function() {
            Swal.fire({
                title: 'Ditolak!',
                text: 'Izin telah ditolak.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            document.getElementById('verification-modal').classList.add('hidden');
        });

        document.getElementById('finish-button').addEventListener('click', function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengakhiri acara? Data presensi tidak dapat diedit lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, akhiri',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Sukses!', 'Acara telah diakhiri.', 'success').then(() => {
                        window.location.href = '{{ route('attendance-admin') }}'; // Redirect ke halaman kehadiran
                    });
                }
            });
        });

        // Fungsi untuk menampilkan modal presensi
        function showPresensiForm(nip, name) {
            document.getElementById('nip-input').value = nip;
            document.getElementById('name-input').value = name;
            document.getElementById('presensi-modal').classList.remove('hidden');
        }

        // Fungsi untuk menampilkan modal verifikasi izin
        function showVerificationForm(nip, name, subject, file) {
            document.getElementById('verification-nip-input').value = nip;
            document.getElementById('verification-name-input').value = name;
            document.getElementById('leave-subject-input').value = subject;
            document.getElementById('supporting-file-link').href = file; // Set the file link
            document.getElementById('verification-modal').classList.remove('hidden');
        }

        // Event listener untuk tombol presensi
        document.querySelectorAll('.show-presensi-form').forEach(button => {
            button.addEventListener('click', function() {
                const nip = this.getAttribute('data-nip');
                const name = this.getAttribute('data-name');
                showPresensiForm(nip, name);
            });
        });

        // Event listener untuk tombol verifikasi
        document.querySelectorAll('.show-verification-form').forEach(button => {
            button.addEventListener('click', function() {
                const nip = this.getAttribute('data-nip');
                const name = this.getAttribute('data-name');
                const subject = this.getAttribute('data-subject');
                const file = this.getAttribute('data-file');
                showVerificationForm(nip, name, subject, file);
            });
        });

        // Menutup modal presensi
        document.getElementById('close-presensi-modal').addEventListener('click', function() {
            document.getElementById('presensi-modal').classList.add('hidden');
        });

        // Menutup modal verifikasi izin
        document.getElementById('close-verification-modal').addEventListener('click', function() {
            document.getElementById('verification-modal').classList.add('hidden');
        });

    </script>
@endsection
