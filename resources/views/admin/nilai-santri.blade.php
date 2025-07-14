@extends('layouts.admin')

@section('title', 'Daftar Santri')

@section('content')
<div class="container px-6 mx-auto grid">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6 mt-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('akademik.kelas', ['semester_id' => $semester->id]) }}" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h3 class="text-lg font-semibold text-gray-800">Daftar Santri - {{ $kelas->nama_kelas }} - Semester {{ $semester->nama_semester }} ({{ $semester->tahun_ajaran }})</h3>
        </div>
    </div>

    <!-- Tabel Santri -->
    <div class="overflow-x-auto">
        <table class="w-full mt-4 bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Santri</th>
                    <th class="px-4 py-3">Jenis Kelamin</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="santri-table-body" class="bg-white divide-y">
                <!-- Data will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Modal Input Nilai dan Catatan -->
    <div id="input-nilai-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-input-nilai-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Input Nilai dan Catatan - <span id="nama-santri"></span></h2>
            <form id="input-nilai-form">
                <input type="hidden" id="santri_id" name="santri_id">
                <div id="nilai-fields" class="space-y-4 mb-4"></div>
                <!-- Catatan -->
                <div class="mb-4">
                    <label for="catatan_dari" class="block text-sm font-medium text-gray-700">Catatan Dari</label>
                    <select id="catatan_dari" name="catatan_dari" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Pengajar</option>
                        <option value="Fairuz Cinta Sherly Nanda">Fairuz Cinta Sherly Nanda</option>
                        <option value="Nadila Nurfariza Tahir">Nadila Nurfariza Tahir</option>
                        <option value="Lili Lailatul Al Fitri">Lili Lailatul Al Fitri</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="catatan" name="catatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-2" rows="3" placeholder="Masukkan catatan untuk santri..." required></textarea>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" id="cancel-input-nilai-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript untuk Data dan Modal -->
<script>
    // Ambil kelasList dari localStorage
    const kelasList = JSON.parse(localStorage.getItem('kelasList')) || [];
    const kelasId = {{ $kelas->id }};
    const semesterId = {{ $semester->id }};
    const kelas = kelasList.find(k => k.id == kelasId);

    // Ambil santriList dari localStorage atau inisialisasi jika belum ada
    let santriList = JSON.parse(localStorage.getItem('santriList')) || [
        {
            id: 1,
            nama_santri: 'Ahmad Rizky',
            jenis_kelamin: 'Laki-laki',
            nilai: {},
            catatan: '',
            catatan_dari: '',
            nis: '24051'
        },
        {
            id: 2,
            nama_santri: 'Siti Aisyah',
            jenis_kelamin: 'Perempuan',
            nilai: {},
            catatan: '',
            catatan_dari: '',
            nis: '24052'
        }
    ];

    // Fungsi untuk menyimpan santriList ke localStorage
    function saveSantriList() {
        localStorage.setItem('santriList', JSON.stringify(santriList));
    }

    // Fungsi untuk merender tabel
    function renderSantriTable() {
        const tableBody = document.getElementById('santri-table-body');
        tableBody.innerHTML = '';
        santriList.forEach((santri, index) => {
            const row = `
                <tr class="text-gray-700">
                    <td class="px-4 py-3">${index + 1}</td>
                    <td class="px-4 py-3">${santri.nama_santri}</td>
                    <td class="px-4 py-3">${santri.jenis_kelamin}</td>
                    <td class="px-4 py-3 text-sm flex gap-2">
                        <button class="input-nilai-button w-8 h-8 text-white bg-blue-600 rounded-md flex items-center justify-center hover:bg-blue-700" data-id="${santri.id}" data-nama="${santri.nama_santri}" data-catatan="${santri.catatan || ''}" data-catatan-dari="${santri.catatan_dari || ''}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="/akademik/rekap-nilai/${semesterId}/kelas/${kelasId}/santri/${santri.id}/rapor" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/akademik/rekap-nilai/${semesterId}/kelas/${kelasId}/santri/${santri.id}/rapor/download" class="w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        // Re-attach event listeners untuk tombol input nilai
        attachEventListeners();
    }

    // Fungsi untuk render field nilai di modal
    function renderNilaiFields(santriId) {
        const fieldsContainer = document.getElementById('nilai-fields');
        fieldsContainer.innerHTML = '';
        if (!kelas || !kelas.mata_pelajaran) {
            fieldsContainer.innerHTML = '<p class="text-sm text-gray-600">Mata pelajaran belum diatur untuk kelas ini.</p>';
            return;
        }

        // Kelompokkan mata pelajaran berdasarkan kategori
        const grouped = kelas.mata_pelajaran.reduce((acc, mp) => {
            if (!acc[mp.kategori]) acc[mp.kategori] = [];
            acc[mp.kategori].push(mp);
            return acc;
        }, {});

        // Render field per kategori
        for (const [kategori, mataPelajaran] of Object.entries(grouped)) {
            const kategoriSection = `
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">${kategori}</h4>
                    <div class="space-y-2">
                        ${mataPelajaran.map(mp => `
                            <div>
                                <label for="nilai_${mp.nama.replace(/\s+/g, '_')}" class="block text-sm font-medium text-gray-700">${mp.nama}</label>
                                <input type="number" id="nilai_${mp.nama.replace(/\s+/g, '_')}" name="nilai[${mp.nama}]" value="${santriList.find(s => s.id == santriId)?.nilai[mp.nama] || ''}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" min="0" max="100" required>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            fieldsContainer.innerHTML += kategoriSection;
        }
    }

    // Fungsi untuk menambahkan event listener
    function attachEventListeners() {
        // Input Nilai Button
        document.querySelectorAll('.input-nilai-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const catatan = this.getAttribute('data-catatan');
                const catatanDari = this.getAttribute('data-catatan-dari');
                document.getElementById('santri_id').value = id;
                document.getElementById('nama-santri').textContent = nama;
                document.getElementById('catatan').value = catatan;
                document.getElementById('catatan_dari').value = catatanDari;
                renderNilaiFields(id);
                document.getElementById('input-nilai-modal').classList.remove('hidden');
            });
        });
    }

    // Render tabel saat halaman dimuat
    renderSantriTable();

    // Buka/Tutup Modal Input Nilai
    document.getElementById('close-input-nilai-modal').addEventListener('click', function() {
        document.getElementById('input-nilai-modal').classList.add('hidden');
    });

    document.getElementById('cancel-input-nilai-button').addEventListener('click', function() {
        document.getElementById('input-nilai-modal').classList.add('hidden');
    });

    // Submit Form Input Nilai dan Catatan
    document.getElementById('input-nilai-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('santri_id').value;
        const nilaiInputs = document.querySelectorAll('#nilai-fields input[name*="nilai"]');
        const nilai = {};
        nilaiInputs.forEach(input => {
            const nama = input.name.match(/\[(.+)\]/)[1];
            nilai[nama] = input.value;
        });
        const updatedSantri = {
            id: parseInt(id),
            nama_santri: santriList.find(s => s.id == id).nama_santri,
            jenis_kelamin: santriList.find(s => s.id == id).jenis_kelamin,
            nilai: nilai,
            catatan: document.getElementById('catatan').value,
            catatan_dari: document.getElementById('catatan_dari').value,
            nis: santriList.find(s => s.id == id).nis
        };
        santriList = santriList.map(santri => santri.id == id ? updatedSantri : santri);
        saveSantriList(); // Simpan ke localStorage
        document.getElementById('input-nilai-modal').classList.add('hidden');
        Swal.fire(
            'Berhasil!',
            'Nilai dan catatan santri telah diperbarui.',
            'success'
        );
        renderSantriTable();
    });
</script>
@endsection
