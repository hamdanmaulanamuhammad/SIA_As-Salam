@extends('layouts.pengajar')

@section('title', 'Kelas Semester')

@section('content')
<div class="container px-5 mx-auto py-6">
    <!-- Header -->
    <div class="flex justify-between mb-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Kelas - {{ $semester->nama_semester }} ({{ $semester->tahun_ajaran }})</h3>
        <div class="flex gap-2">
            <a href="{{ route('pengajar.akademik.index') }}?tab=semester" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button id="tambah-kelas-button" class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>
    </div>

    <!-- Card Kelas -->
    @if ($kelasSemesters->isEmpty())
        <p class="text-gray-600">Belum ada kelas yang ditambahkan untuk semester ini.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($kelasSemesters as $kelasSemester)
                <a href="{{ route('pengajar.rapor.index', $kelasSemester->id) }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $kelasSemester->kelas->nama_kelas }}</h4>
                        <p class="text-sm text-gray-600 mb-1"><strong>Wali Kelas:</strong> {{ $kelasSemester->waliKelas ? $kelasSemester->waliKelas->full_name : '-' }}</p>
                        <p class="text-sm text-gray-600 mb-3"><strong>Mudir:</strong> {{ $kelasSemester->mudir ? $kelasSemester->mudir->full_name : '-' }}</p>
                        <p class="text-sm text-gray-600 mb-3"><strong>Mata Pelajaran:</strong></p>
                        @if ($kelasSemester->mapels->isEmpty())
                            <p class="text-sm text-gray-500 italic">Belum ada mata pelajaran.</p>
                        @else
                            <ul class="text-sm text-gray-600 list-disc list-inside mb-3">
                                @foreach ($kelasSemester->mapels as $mapel)
                                    <li class="flex justify-between items-center">
                                        {{ $mapel->mataPelajaran->nama_mapel }} ({{ $mapel->mataPelajaran->kategori }})
                                        <form class="delete-mapel-form" action="{{ route('pengajar.kelas-semester.mapel.destroy', $mapel->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="flex gap-2 mt-3">
                            <button class="edit-button text-sm px-3 py-1 text-white bg-yellow-500 rounded-md hover:bg-yellow-600" data-id="{{ $kelasSemester->id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form class="delete-form" action="{{ route('pengajar.kelas-semester.destroy', $kelasSemester->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm px-3 py-1 text-white bg-red-600 rounded-md hover:bg-red-700">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                            <button class="manage-mapel-button text-sm px-3 py-1 text-white bg-green-600 rounded-md hover:bg-green-700" data-id="{{ $kelasSemester->id }}">
                                <i class="fas fa-book"></i> Kelola Mapel
                            </button>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="mt-6">
            {{ $kelasSemesters->links() }}
        </div>
    @endif

    <!-- Modal Tambah/Edit Kelas -->
    <div id="tambah-kelas-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-tambah-kelas-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4" id="modal-title">Tambah Kelas</h2>
            <form id="tambah-kelas-form" action="{{ route('pengajar.kelas-semester.store', $semester->id) }}" method="POST">
                @csrf
                <input type="hidden" id="kelas-semester-id" name="id">
                <input type="hidden" name="_method" id="kelas-method" value="POST">
                <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                <div class="mb-4">
                    <label for="kelas_id" class="block text-sm font-medium text-gray-700">Nama Kelas <span class="text-red-600">*</span></label>
                    <select id="kelas_id" name="kelas_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                        <option value="">Pilih Kelas</option>
                        @foreach (\App\Models\Kelas::all() as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="wali_kelas_id" class="block text-sm font-medium text-gray-700">Wali Kelas <span class="text-red-600">*</span></label>
                    <select id="wali_kelas_id" name="wali_kelas_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                        <option value="">Pilih Wali Kelas</option>
                        @foreach (\App\Models\User::where('role', 'pengajar')->get() as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="mudir_id" class="block text-sm font-medium text-gray-700">Mudir <span class="text-red-600">*</span></label>
                    <select id="mudir_id" name="mudir_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                        <option value="">Pilih Mudir</option>
                        @foreach (\App\Models\User::where('role', 'admin')->get() as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-tambah-kelas-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Kelola Mata Pelajaran -->
    <div id="kelola-mapel-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-kelola-mapel-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Kelola Mata Pelajaran</h2>
            <form id="tambah-mapel-form" action="{{ route('pengajar.kelas-semester.mapel.store') }}" method="POST">
                @csrf
                <input type="hidden" id="kelas-semester-id-mapel" name="kelas_semester_id">
                <div class="mb-4">
                    <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran <span class="text-red-600">*</span></label>
                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach (\App\Models\Mapel::all() as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} ({{ $mapel->kategori }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-kelola-mapel-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Buka modal tambah kelas
    document.getElementById('tambah-kelas-button')?.addEventListener('click', () => {
        document.getElementById('tambah-kelas-form').reset();
        document.getElementById('kelas-semester-id').value = '';
        document.getElementById('kelas-method').value = 'POST';
        document.getElementById('modal-title').textContent = 'Tambah Kelas';
        const actionUrl = "{{ route('pengajar.kelas-semester.store', $semester->id) }}";
        document.getElementById('tambah-kelas-form').action = actionUrl;
        document.getElementById('tambah-kelas-modal').classList.remove('hidden');
    });

    // Tutup modal kelas
    document.getElementById('close-tambah-kelas-modal')?.addEventListener('click', () => {
        document.getElementById('tambah-kelas-modal').classList.add('hidden');
    });

    document.getElementById('cancel-tambah-kelas-button')?.addEventListener('click', () => {
        document.getElementById('tambah-kelas-modal').classList.add('hidden');
    });

    // Handle form tambah/edit kelas
    document.getElementById('tambah-kelas-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const id = document.getElementById('kelas-semester-id').value;
        const url = id ? "{{ url('pengajar/akademik/kelas-semester') }}/" + id : "{{ route('pengajar.kelas-semester.store', $semester->id) }}";
        const method = id ? 'PUT' : 'POST';
        const formData = new FormData(this);
        formData.append('_method', method);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(JSON.stringify(err) || 'Terjadi kesalahan pada server.') });
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: data.success ? 'Berhasil!' : 'Gagal!',
                text: data.message,
                icon: data.success ? 'success' : 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                if (data.success) {
                    location.reload();
                }
            });
        })
        .catch(error => {
            Swal.fire('Error!', error.message || 'Terjadi kesalahan pada server.', 'error');
        });
    });

    // Handle tombol edit kelas
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const id = button.getAttribute('data-id');
            const url = `{{ url('pengajar/akademik/kelas-semester') }}/${id}/edit`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data kelas.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('kelas-semester-id').value = data.data.id;
                    document.getElementById('kelas_id').value = data.data.kelas_id;
                    document.getElementById('wali_kelas_id').value = data.data.wali_kelas_id;
                    document.getElementById('mudir_id').value = data.data.mudir_id;
                    document.getElementById('modal-title').textContent = 'Edit Kelas';
                    document.getElementById('tambah-kelas-form').action = `{{ url('pengajar/akademik/kelas-semester') }}/${data.data.id}`;
                    document.getElementById('kelas-method').value = 'PUT';
                    document.getElementById('tambah-kelas-modal').classList.remove('hidden');
                } else {
                    Swal.fire('Error!', data.message || 'Gagal mengambil data kelas.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Gagal mengambil data kelas: ' + error.message, 'error');
            });
        });
    });

    // Handle tombol delete kelas
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus kelas ini dari semester?',
                icon: 'warning',
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
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(JSON.stringify(err) || 'Terjadi kesalahan saat menghapus data.') });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', error.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                    });
                }
            });
        });
    });

    // Handle tombol kelola mata pelajaran
    document.querySelectorAll('.manage-mapel-button').forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const id = button.getAttribute('data-id');
            const actionUrl = "{{ route('pengajar.kelas-semester.mapel.store') }}";
            document.getElementById('kelas-semester-id-mapel').value = id;
            document.getElementById('tambah-mapel-form').reset();
            document.getElementById('tambah-mapel-form').action = actionUrl;
            document.getElementById('kelola-mapel-modal').classList.remove('hidden');
        });
    });

    // Tutup modal mata pelajaran
    document.getElementById('close-kelola-mapel-modal')?.addEventListener('click', () => {
        document.getElementById('kelola-mapel-modal').classList.add('hidden');
    });

    document.getElementById('cancel-kelola-mapel-button')?.addEventListener('click', () => {
        document.getElementById('kelola-mapel-modal').classList.add('hidden');
    });

    // Handle form tambah mata pelajaran
    document.getElementById('tambah-mapel-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const url = this.action;
        const formData = new FormData(this);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(JSON.stringify(err) || 'Terjadi kesalahan pada server.') });
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: data.success ? 'Berhasil!' : 'Gagal!',
                text: data.message,
                icon: data.success ? 'success' : 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                if (data.success) {
                    location.reload();
                }
            });
        })
        .catch(error => {
            Swal.fire('Error!', error.message || 'Terjadi kesalahan pada server.', 'error');
        });
    });

    // Handle tombol delete mata pelajaran
    document.querySelectorAll('.delete-mapel-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus mata pelajaran ini dari kelas?',
                icon: 'warning',
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
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(JSON.stringify(err) || 'Terjadi kesalahan saat menghapus data.') });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', error.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                    });
                }
            });
        });
    });
});
</script>
@endsection
