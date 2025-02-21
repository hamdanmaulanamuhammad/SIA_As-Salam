@extends('layouts.admin')

@section('title', 'Data Santri')

@section('content')
    <div class="container mx-auto p-6">
        <!-- Navigasi Path -->
        <nav class="text-sm text-gray-600 mb-4">
            <a href="#" class="text-blue-600">Santri Admin</a> > <span class="text-gray-800">Data Santri</span>
        </nav>
        
        <h1 class="text-2xl font-bold mb-6">Data Santri</h1>

        <!-- Filter Options -->
        <div class="mb-4 flex items-center justify-between">
            <div>
                <label for="status" class="mr-2">Status:</label>
                <select id="status" class="border rounded px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>

                <label for="class" class="ml-4 mr-2">Kelas:</label>
                <select id="class" class="border rounded px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="1">Kelas 1</option>
                    <option value="2">Kelas 2</option>
                    <option value="3">Kelas 3</option>
                    <!-- Add more class options as needed -->
                </select>
            </div>

            <button id="add-student-btn" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus"></i> Tambah Santri
            </button>
        </div>

        <!-- Tabel Data Santri -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3 min-w-52">Nama Lengkap Santri</th>
                        <th class="px-4 py-3">Nama Panggilan</th>
                        <th class="px-4 py-3 min-w-52">TTL</th>
                        <th class="px-4 py-3">Umur</th>
                        <th class="px-4 py-3 min-w-32">Jenis Kelamin</th>
                        <th class="px-4 py-3 min-w-40">Hobi</th>
                        <th class="px-4 py-3 min-w-40">Riwayat Penyakit</th>
                        <th class="px-4 py-3 min-w-52">Alamat</th>
                        <th class="px-4 py-3 min-w-44">Sekolah</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Jilid/Juz</th>
                        <th class="px-4 py-3 min-w-40">Nama Wali</th>
                        <th class="px-4 py-3">Pekerjaan</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3 min-w-40">Foto</th>
                        <th class="px-4 py-3 min-w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">123456</td>
                        <td class="px-4 py-3">Ahmad Fauzan</td>
                        <td class="px-4 py-3">Fauzan</td>
                        <td class="px-4 py-3">Sleman 09 September 2014</td>
                        <td class="px-4 py-3">14</td>
                        <td class="px-4 py-3">Laki-laki</td>
                        <td class="px-4 py-3">Sepak Bola</td>
                        <td class="px-4 py-3">Asma</td>
                        <td class="px-4 py-3">Jl. Merdeka No.10</td>
                        <td class="px-4 py-3">SMP Negeri 1</td>
                        <td class="px-4 py-3">8</td>
                        <td class="px-4 py-3">Juz 5</td>
                        <td class="px-4 py-3">Budi Santoso</td>
                        <td class="px-4 py-3">Wiraswasta</td>
                        <td class="px-4 py-3">08123456789</td>
                        <td class="px-4 py-3"><img src="https://placehold.co/50x50" class="w-20 h-20"></td>
                        <td class="px-4 py-3">
                            <button class="px-2 py-1 text-blue-600 edit-student-btn" data-id="1"><i class="fas fa-edit"></i></button>
                            <button class="px-2 py-1 text-red-600 delete-student-btn" data-id="1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <nav class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">1</span> dari <span class="font-medium">1</span> hasil
                    </span>
                </div>
                <div>
                    <span class="relative z-0 inline-flex shadow-sm">
                        <button class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-l-md">
                            Sebelumnya
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-r-md">
                            Selanjutnya
                        </button>
                    </span>
                </div>
            </nav>
        </div>
    </div>

    <!-- Modal untuk Form Tambah Santri -->
    <div id="add-student-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-modal-btn" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold mb-4">Tambah Santri Baru</h2>
            <form id="add-student-form">
                <div class="mb-4">
                    <label for="nama_lengkap" class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Nama Lengkap" required>
                </div>
                <div class="mb-4">
                    <label for="nama_panggilan" class="block text-gray-700 text-sm font-medium mb-2">Nama Panggilan</label>
                    <input type="text" id="nama_panggilan" name="nama_panggilan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Nama Panggilan" required>
                </div>
                <div class="mb-4">
                    <label for="ttl" class="block text-gray-700 text-sm font-medium mb-2">Tanggal Lahir</label>
                    <input type="date" id="ttl" name="ttl" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="umur" class="block text-gray-700 text-sm font-medium mb-2">Umur</label>
                    <input type="number" id="umur" name="umur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Umur" required>
                </div>
                <div class="mb-4">
                    <label for="jenis_kelamin" class="block text-gray-700 text-sm font-medium mb-2">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="hobi" class="block text-gray-700 text-sm font-medium mb-2">Hobi</label>
                    <input type="text" id="hobi" name="hobi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Hobi" required>
                </div>
                <div class="mb-4">
                    <label for="riwayat_penyakit" class="block text-gray-700 text-sm font-medium mb-2">Riwayat Penyakit</label>
                    <input type="text" id="riwayat_penyakit" name="riwayat_penyakit" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Riwayat Penyakit" required>
                </div>
                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700 text-sm font-medium mb-2">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Alamat" required>
                </div>
                <div class="mb-4">
                    <label for="sekolah" class="block text-gray-700 text-sm font-medium mb-2">Sekolah</label>
                    <input type="text" id="sekolah" name="sekolah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Sekolah" required>
                </div>
                <div class="mb-4">
                    <label for="kelas" class="block text-gray-700 text-sm font-medium mb-2">Kelas</label>
                    <input type="text" id="kelas" name="kelas" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Kelas" required>
                </div>
                <div class="mb-4">
                    <label for="jilid_juz" class="block text-gray-700 text-sm font-medium mb-2">Jilid/Juz</label>
                    <input type="text" id="jilid_juz" name="jilid_juz" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Jilid/Juz" required>
                </div>
                <div class="mb-4">
                    <label for="nama_wali" class="block text-gray-700 text-sm font-medium mb-2">Nama Wali</label>
                    <input type="text" id="nama_wali" name="nama_wali" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Nama Wali" required>
                </div>
                <div class="mb-4">
                    <label for="pekerjaan" class="block text-gray-700 text-sm font-medium mb-2">Pekerjaan Wali</label>
                    <input type="text" id="pekerjaan" name="pekerjaan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan Pekerjaan Wali" required>
                </div>
                <div class="mb-4">
                    <label for="no_hp" class="block text-gray-700 text-sm font-medium mb-2">No HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Masukkan No HP" required>
                </div>
                <div class="mb-4">
                    <label for="foto" class="block text-gray-700 text-sm font-medium mb-2">Foto</label>
                    <input type="file" id="foto" name="foto" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="close-modal-btn" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addStudentBtn = document.getElementById('add-student-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
        const addStudentModal = document.getElementById('add-student-modal');
        const editStudentModal = document.getElementById('edit-student-modal');
        const addStudentForm = document.getElementById('add-student-form');
        const editStudentForm = document.getElementById('edit-student-form');

        // Open modal form for adding new student
        addStudentBtn.addEventListener('click', function() {
            addStudentModal.classList.remove('hidden');
        });

        // Close modal form for adding new student
        closeModalBtn.addEventListener('click', function() {
            addStudentModal.classList.add('hidden');
        });

        // Add new student
        addStudentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Simulate successful addition
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data santri berhasil ditambahkan!',
            });

            // Close modal form
            addStudentModal.classList.add('hidden');
        });

        // Open modal form for editing student
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                // Simulate fetching student data
                const studentData = {
                    id: studentId,
                    nis: '123456',
                    nama_lengkap: 'Ahmad Fauzan',
                    // Add more fields as needed
                };

                document.getElementById('edit-student-id').value = studentData.id;
                document.getElementById('edit-nis').value = studentData.nis;
                document.getElementById('edit-nama_lengkap').value = studentData.nama_lengkap;
                // Set other fields as needed

                editStudentModal.classList.remove('hidden');
            });
        });

        // Close modal form for editing student
        closeEditModalBtn.addEventListener('click', function() {
            editStudentModal.classList.add('hidden');
        });

        // Edit student
        editStudentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Simulate successful update
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data santri berhasil diperbarui!',
            });

            // Close modal form
            editStudentModal.classList.add('hidden');
        });

        // Delete student
        document.querySelectorAll('.delete-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Simulate successful deletion
                        Swal.fire(
                            'Terhapus!',
                            'Data santri berhasil dihapus.',
                            'success'
                        );

                        // Remove row from table (simulated)
                        const row = this.closest('tr');
                        row.remove();
                    }
                });
            });
        });
    });
</script>
@endsection
