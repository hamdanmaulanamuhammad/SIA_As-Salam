@extends('layouts.admin')

@section('title', 'Daftar Santri')

@section('content')
<div class="container px-6 mx-auto grid">
    <!-- Header -->
    <div class="flex justify-end mb-6 mt-6">
        <button id="tambah-santri-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
            <i class="fas fa-plus"></i> Tambah Santri
        </button>
    </div>

    <!-- Judul -->
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Daftar Santri - Kelas A - Semester 1</h3>

    <!-- Tabel Santri -->
    <div class="overflow-x-auto">
        <table class="w-full mt-4 bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Santri</th>
                    <th class="px-4 py-3">NIS</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="santri-table-body" class="bg-white divide-y">
                <!-- Data will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Santri -->
    <div id="tambah-santri-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-tambah-santri-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Tambah Santri</h2>
            <form id="tambah-santri-form">
                <div class="mb-4">
                    <label for="nama_santri" class="block text-sm font-medium text-gray-700">Nama Santri</label>
                    <input type="text" id="nama_santri" name="nama_santri" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Contoh: Ali Hasan" required>
                </div>
                <div class="mb-4">
                    <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                    <input type="text" id="nis" name="nis" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Contoh: 123456" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-tambah-santri-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Input/Edit Nilai -->
    <div id="edit-nilai-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-edit-nilai-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Input/Edit Nilai</h2>
            <form id="edit-nilai-form">
                <input type="hidden" id="edit_santri_id" name="santri_id">
                <div class="mb-4">
                    <label for="mata_pelajaran" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <select id="mata_pelajaran" name="mata_pelajaran" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        <option value="Matematika">Matematika</option>
                        <option value="Bahasa Arab">Bahasa Arab</option>
                        <option value="Agama">Agama</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai</label>
                    <input type="number" id="nilai" name="nilai" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" min="0" max="100" required>
                </div>
                <div class="mb-4">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-edit-nilai-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-santri-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-sm">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Konfirmasi Hapus</h2>
            <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus santri ini?</p>
            <div class="flex justify-end">
                <button type="button" id="cancel-delete-santri-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                <button type="button" id="confirm-delete-santri-button" class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript untuk Data dan Modal -->
<script>
    // Data dummy untuk santri
    let santriList = [
        {
            id: 1,
            nama_santri: 'Ali Hasan',
            nis: '123456',
            nilai: []
        },
        {
            id: 2,
            nama_santri: 'Siti Fatimah',
            nis: '789012',
            nilai: []
        }
    ];

    // Fungsi untuk merender tabel
    function renderSantriTable() {
        const tableBody = document.getElementById('santri-table-body');
        tableBody.innerHTML = '';
        santriList.forEach((santri, index) => {
            const row = `
                <tr class="text-gray-700">
                    <td class="px-4 py-3">${index + 1}</td>
                    <td class="px-4 py-3">${santri.nama_santri}</td>
                    <td class="px-4 py-3">${santri.nis}</td>
                    <td class="px-4 py-3 text-sm flex gap-2">
                        <button class="edit-nilai-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center" data-id="${santri.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="/akademik/rekap-nilai/1/kelas/1/santri/${santri.id}/rapor" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="download-button w-8 h-8 text-white bg-indigo-600 rounded-md flex items-center justify-center" data-id="${santri.id}">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="delete-santri-button w-8 h-8 text-white bg-red-600 rounded-md flex items-center justify-center" data-id="${santri.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        // Re-attach event listeners
        attachSantriEventListeners();
    }

    // Fungsi untuk menambahkan event listener
    function attachSantriEventListeners() {
        // Edit Nilai Button
        document.querySelectorAll('.edit-nilai-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('edit_santri_id').value = id;
                document.getElementById('edit-nilai-modal').classList.remove('hidden');
            });
        });

        // Download Button
        document.querySelectorAll('.download-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire(
                    'Download',
                    `Memulai unduh PDF untuk santri ID: ${id}`,
                    'info'
                );
            });
        });

        // Delete Button
        document.querySelectorAll('.delete-santri-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus santri ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        santriList = santriList.filter(santri => santri.id != id);
                        Swal.fire(
                            'Terhapus!',
                            'Santri telah dihapus.',
                            'success'
                        );
                        renderSantriTable();
                    }
                });
            });
        });
    }

    // Render tabel saat halaman dimuat
    renderSantriTable();

    // Buka/Tutup Modal Tambah Santri
    document.getElementById('tambah-santri-button').addEventListener('click', function() {
        document.getElementById('tambah-santri-modal').classList.remove('hidden');
    });

    document.getElementById('close-tambah-santri-modal').addEventListener('click', function() {
        document.getElementById('tambah-santri-modal').classList.add('hidden');
    });

    document.getElementById('cancel-tambah-santri-button').addEventListener('click', function() {
        document.getElementById('tambah-santri-modal').classList.add('hidden');
    });

    // Buka/Tutup Modal Edit Nilai
    document.getElementById('close-edit-nilai-modal').addEventListener('click', function() {
        document.getElementById('edit-nilai-modal').classList.add('hidden');
    });

    document.getElementById('cancel-edit-nilai-button').addEventListener('click', function() {
        document.getElementById('edit-nilai-modal').classList.add('hidden');
    });

    // Submit Form Tambah Santri
    document.getElementById('tambah-santri-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const newSantri = {
            id: santriList.length + 1,
            nama_santri: document.getElementById('nama_santri').value,
            nis: document.getElementById('nis').value,
            nilai: []
        };
        santriList.push(newSantri);
        document.getElementById('tambah-santri-modal').classList.add('hidden');
        Swal.fire(
            'Berhasil!',
            'Santri baru telah ditambahkan.',
            'success'
        );
        renderSantriTable();
        this.reset();
    });

    // Submit Form Edit Nilai
    document.getElementById('edit-nilai-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_santri_id').value;
        const newNilai = {
            mata_pelajaran: document.getElementById('mata_pelajaran').value,
            nilai: document.getElementById('nilai').value,
            catatan: document.getElementById('catatan').value
        };
        const santri = santriList.find(s => s.id == id);
        santri.nilai.push(newNilai);
        document.getElementById('edit-nilai-modal').classList.add('hidden');
        Swal.fire(
            'Berhasil!',
            'Nilai telah diperbarui.',
            'success'
        );
        renderSantriTable();
        this.reset();
    });
</script>
@endsection
