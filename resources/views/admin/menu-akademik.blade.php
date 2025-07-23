@extends('layouts.admin')

@section('title', 'Menu Akademik')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6 mt-6">
        <h1 class="text-2xl font-bold">Menu Akademik</h1>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="?tab=kelas" id="tab-kelas" class="tab-btn active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                Kelas
            </a>
            <a href="?tab=mapel" id="tab-mapel" class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Mata Pelajaran
            </a>
            <a href="?tab=semester" id="tab-semester" class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Semester
            </a>
        </nav>
    </div>

    <!-- Tab Content Kelas -->
    <div id="content-kelas" class="tab-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Kelas</h2>
            <button id="tambahKelasButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Tambah Kelas
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Kelas</th>
                        <th class="px-4 py-3">Jumlah Santri</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kelasTableBody" class="bg-white divide-y">
                    @foreach($kelas as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($kelas->currentPage() - 1) * $kelas->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nama_kelas }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->santri_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-kelas-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('akademik.kelas.destroy', $item->id) }}?tab=kelas" method="POST" class="delete-kelas-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $kelas->appends(['tab' => 'kelas'])->links() }}
        </div>
    </div>

    <!-- Tab Content Mata Pelajaran -->
    <div id="content-mapel" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Mata Pelajaran</h2>
            <button id="tambahMapelButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Tambah Mata Pelajaran
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Mapel</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="mapelTableBody" class="bg-white divide-y">
                    @foreach($mapels as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($mapels->currentPage() - 1) * $mapels->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nama_mapel }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $item->kategori === 'Hafalan' ? 'bg-green-100 text-green-800' :
                                   ($item->kategori === 'Teori' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-mapel-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('akademik.mapel.destroy', $item->id) }}?tab=mapel" method="POST" class="delete-mapel-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $mapels->appends(['tab' => 'mapel'])->links() }}
        </div>
    </div>

    <!-- Tab Content Semester -->
    <div id="content-semester" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Semester</h2>
            <button id="tambahSemesterButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Tambah Semester
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Semester</th>
                        <th class="px-4 py-3">Tahun Ajaran</th>
                        <th class="px-4 py-3">Tanggal Mulai</th>
                        <th class="px-4 py-3">Tanggal Selesai</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="semesterTableBody" class="bg-white divide-y">
                    @foreach($semesters as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($semesters->currentPage() - 1) * $semesters->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nama_semester }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tahun_ajaran }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-semester-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('akademik.semester.destroy', $item->id) }}?tab=semester" method="POST" class="delete-semester-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('akademik.kelas-semester', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $semesters->appends(['tab' => 'semester'])->links() }}
        </div>
    </div>

    <!-- Modal for Kelas Form -->
    <div id="kelas-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Kelas</h3>
                <button id="close-kelas-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="kelas-form" action="{{ route('akademik.kelas.store') }}?tab=kelas" method="POST">
                @csrf
                <input type="hidden" id="kelas-id" name="id">
                <input type="hidden" name="_method" id="kelas-method" value="POST">
                <div class="mb-4">
                    <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_kelas" id="nama_kelas" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-kelas-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Mapel Form -->
    <div id="mapel-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Mata Pelajaran</h3>
                <button id="close-mapel-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="mapel-form" action="{{ route('akademik.mapel.store') }}?tab=mapel" method="POST">
                @csrf
                <input type="hidden" id="mapel-id" name="id">
                <input type="hidden" name="_method" id="mapel-method" value="POST">
                <div class="mb-4">
                    <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_mapel" id="nama_mapel" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-600">*</span></label>
                    <select name="kategori" id="kategori" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Hafalan">Hafalan</option>
                        <option value="Teori">Teori</option>
                        <option value="Praktik">Praktik</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-mapel-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Semester Form -->
    <div id="semester-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Semester</h3>
                <button id="close-semester-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="semester-form" action="{{ route('akademik.semester.store') }}?tab=semester" method="POST">
                @csrf
                <input type="hidden" id="semester-id" name="id">
                <input type="hidden" name="_method" id="semester-method" value="POST">
                <div class="mb-4">
                    <label for="nama_semester" class="block text-sm font-medium text-gray-700 mb-1">Nama Semester <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_semester" id="nama_semester" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span class="text-red-600">*</span></label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" placeholder="2024/2025" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-semester-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Management
    function showTab(tabName) {
        console.log('Switching to tab:', tabName); // Debugging log
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        const content = document.getElementById('content-' + tabName);
        if (content) {
            content.classList.remove('hidden');
        } else {
            console.error('Tab content not found for:', tabName);
        }

        // Add active class to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        if (activeTab) {
            activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
        } else {
            console.error('Tab button not found for:', tabName);
        }

        // Store the active tab in sessionStorage
        sessionStorage.setItem('activeTab', tabName);
    }

    // Explicitly add event listeners to tab buttons
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            const tabName = button.id.replace('tab-', '');
            showTab(tabName);
        });
    });

    // Restore active tab on page load
    const urlParams = new URLSearchParams(window.location.search);
    const tabFromUrl = urlParams.get('tab');
    const validTabs = ['kelas', 'mapel', 'semester'];
    const lastTab = sessionStorage.getItem('activeTab');
    const activeTab = tabFromUrl && validTabs.includes(tabFromUrl) ? tabFromUrl : (lastTab && validTabs.includes(lastTab) ? lastTab : 'kelas');
    showTab(activeTab);

    // Kelas Modal Handlers
    document.getElementById('tambahKelasButton')?.addEventListener('click', () => {
        sessionStorage.setItem('activeTab', 'kelas'); // Set tab before opening modal
        document.getElementById('kelas-form').reset();
        document.getElementById('kelas-id').value = '';
        document.getElementById('kelas-method').value = 'POST';
        document.getElementById('kelas-form').action = "{{ route('akademik.kelas.store') }}?tab=kelas";
        document.getElementById('kelas-form-modal').classList.remove('hidden');
    });

    document.getElementById('close-kelas-form-modal')?.addEventListener('click', () => {
        document.getElementById('kelas-form-modal').classList.add('hidden');
    });

    document.getElementById('cancel-kelas-form-button')?.addEventListener('click', () => {
        document.getElementById('kelas-form-modal').classList.add('hidden');
    });

    // Mapel Modal Handlers
    document.getElementById('tambahMapelButton')?.addEventListener('click', () => {
        sessionStorage.setItem('activeTab', 'mapel'); // Set tab before opening modal
        document.getElementById('mapel-form').reset();
        document.getElementById('mapel-id').value = '';
        document.getElementById('mapel-method').value = 'POST';
        document.getElementById('mapel-form').action = "{{ route('akademik.mapel.store') }}?tab=mapel";
        document.getElementById('mapel-form-modal').classList.remove('hidden');
    });

    document.getElementById('close-mapel-form-modal')?.addEventListener('click', () => {
        document.getElementById('mapel-form-modal').classList.add('hidden');
    });

    document.getElementById('cancel-mapel-form-button')?.addEventListener('click', () => {
        document.getElementById('mapel-form-modal').classList.add('hidden');
    });

    // Semester Modal Handlers
    document.getElementById('tambahSemesterButton')?.addEventListener('click', () => {
        sessionStorage.setItem('activeTab', 'semester'); // Set tab before opening modal
        document.getElementById('semester-form').reset();
        document.getElementById('semester-id').value = '';
        document.getElementById('semester-method').value = 'POST';
        document.getElementById('semester-form').action = "{{ route('akademik.semester.store') }}?tab=semester";
        document.getElementById('semester-form-modal').classList.remove('hidden');
    });

    document.getElementById('close-semester-form-modal')?.addEventListener('click', () => {
        document.getElementById('semester-form-modal').classList.add('hidden');
    });

    document.getElementById('cancel-semester-form-button')?.addEventListener('click', () => {
        document.getElementById('semester-form-modal').classList.add('hidden');
    });

    // Kelas Form Submission
    document.getElementById('kelas-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        sessionStorage.setItem('activeTab', 'kelas'); // Set tab before submission
        const formData = new FormData(this);
        const id = document.getElementById('kelas-id')?.value;
        const url = id ? `/akademik/kelas/${id}?tab=kelas` : this.action;

        fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Berhasil!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location = "{{ route('akademik.index') }}?tab=kelas";
                });
            } else {
                Swal.fire({
                    title: "Gagal!",
                    html: data.message || 'Terjadi kesalahan.',
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        })
        .catch(error => {
            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
        });
    });

    // Mapel Form Submission
    document.getElementById('mapel-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        sessionStorage.setItem('activeTab', 'mapel'); // Set tab before submission
        const formData = new FormData(this);
        const id = document.getElementById('mapel-id')?.value;
        const url = id ? `/akademik/mapel/${id}?tab=mapel` : this.action;

        fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Berhasil!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location = "{{ route('akademik.index') }}?tab=mapel";
                });
            } else {
                Swal.fire({
                    title: "Gagal!",
                    html: data.message || 'Terjadi kesalahan.',
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        })
        .catch(error => {
            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
        });
    });

    // Semester Form Submission
    document.getElementById('semester-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        sessionStorage.setItem('activeTab', 'semester'); // Set tab before submission
        const formData = new FormData(this);
        const id = document.getElementById('semester-id')?.value;
        const url = id ? `/akademik/semester/${id}?tab=semester` : this.action;

        fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Berhasil!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location = "{{ route('akademik.index') }}?tab=semester";
                });
            } else {
                Swal.fire({
                    title: "Gagal!",
                    html: data.message || 'Terjadi kesalahan.',
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        })
        .catch(error => {
            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
        });
    });

    // Edit Kelas
    document.querySelectorAll('.edit-kelas-button').forEach(button => {
        button.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'kelas'); // Set tab before edit
            fetch(`/akademik/kelas/${button.getAttribute('data-id')}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('kelas-id').value = data.data.id;
                        document.getElementById('nama_kelas').value = data.data.nama_kelas;
                        document.getElementById('kelas-method').value = 'PUT';
                        document.getElementById('kelas-form').action = `/akademik/kelas/${data.data.id}?tab=kelas`;
                        document.getElementById('kelas-form-modal').classList.remove('hidden');
                    } else {
                        Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                });
        });
    });

    // Edit Mapel
    document.querySelectorAll('.edit-mapel-button').forEach(button => {
        button.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'mapel'); // Set tab before edit
            fetch(`/akademik/mapel/${button.getAttribute('data-id')}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('mapel-id').value = data.data.id;
                        document.getElementById('nama_mapel').value = data.data.nama_mapel;
                        document.getElementById('kategori').value = data.data.kategori;
                        document.getElementById('mapel-method').value = 'PUT';
                        document.getElementById('mapel-form').action = `/akademik/mapel/${data.data.id}?tab=mapel`;
                        document.getElementById('mapel-form-modal').classList.remove('hidden');
                    } else {
                        Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                });
        });
    });

    // Edit Semester
    document.querySelectorAll('.edit-semester-button').forEach(button => {
        button.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'semester'); // Set tab before edit
            fetch(`/akademik/semester/${button.getAttribute('data-id')}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('semester-id').value = data.data.id;
                        document.getElementById('nama_semester').value = data.data.nama_semester;
                        document.getElementById('tahun_ajaran').value = data.data.tahun_ajaran;
                        document.getElementById('tanggal_mulai').value = data.data.tanggal_mulai || '';
                        document.getElementById('tanggal_selesai').value = data.data.tanggal_selesai || '';
                        document.getElementById('semester-method').value = 'PUT';
                        document.getElementById('semester-form').action = `/akademik/semester/${data.data.id}?tab=semester`;
                        document.getElementById('semester-form-modal').classList.remove('hidden');
                    } else {
                        Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                });
        });
    });

    // Delete Handlers
    document.querySelectorAll('.delete-kelas-form, .delete-mapel-form, .delete-semester-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const tabName = new URL(form.action).searchParams.get('tab') || 'kelas';
            sessionStorage.setItem('activeTab', tabName); // Set tab before deletion
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location = "{{ route('akademik.index') }}?tab=" + tabName;
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    });
                }
            });
        });
    });
});
</script>
@endsection
