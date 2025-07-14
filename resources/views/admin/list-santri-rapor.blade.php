@extends('layouts.admin')

@section('title', 'Daftar Santri - Rapor')

@section('content')
<div class="container px-5 mx-auto py-6">
    <!-- Header -->
    <div class="flex justify-between mb-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800">
            Daftar Santri - {{ $kelasSemester->kelas->nama }} ({{ $kelasSemester->semester->nama_semester }} {{ $kelasSemester->semester->tahun_ajaran }})
        </h3>
        <div class="flex gap-2">
            <a href="{{ route('akademik.kelas-semester.index', $kelasSemester->semester_id) }}" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button id="tambah-santri-button" class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus"></i> Tambah Santri
            </button>
        </div>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Santri -->
    <div class="overflow-x-auto">
        <table class="w-full mt-4 bg-white rounded-lg shadow-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">NIS</th>
                    <th class="px-4 py-3">Nama Santri</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse ($santriList as $index => $santriKelasSemester)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $santriKelasSemester->santri->nis }}</td>
                        <td class="px-4 py-3">{{ $santriKelasSemester->santri->nama_lengkap }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="#" class="input-rapor-link text-blue-600 hover:text-blue-800" data-href="{{ route('akademik.rapor.show', [$kelasSemester->id, $santriKelasSemester->santri->id]) }}" data-has-mapel="{{ $hasMapel ? 'true' : 'false' }}">
                                <i class="fas fa-edit"></i> Input Rapor
                            </a>
                            <button class="edit-santri-button text-yellow-600 hover:text-yellow-800" data-id="{{ $santriKelasSemester->id }}">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <form class="delete-santri-form" action="{{ route('akademik.rapor.destroy', [$kelasSemester->id, $santriKelasSemester->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-600">Belum ada santri terdaftar di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit Santri -->
    <div id="santri-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
            <span id="close-santri-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold text-gray-700 mb-4" id="modal-title">Tambah Santri</h2>
            <form id="santri-form" action="{{ route('akademik.rapor.storeSantri', $kelasSemester->id) }}" method="POST">
                @csrf
                <input type="hidden" id="santri_kelas_semester_id" name="id">
                <input type="hidden" name="_method" id="santri-method" value="POST">
                <div class="mb-4">
                    <label for="santri_id" class="block text-sm font-medium text-gray-700">Nama Santri</label>
                    <select id="santri_id" name="santri_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                        <option value="">Pilih Santri</option>
                        @foreach (App\Models\Santri::whereDoesntHave('kelasSemesters', fn($query) => $query->where('kelas_semester_id', $kelasSemester->id))->get() as $santri)
                            <option value="{{ $santri->id }}">{{ $santri->nama_lengkap }} (NIS: {{ $santri->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-santri-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
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
    // Buka modal tambah santri
    document.getElementById('tambah-santri-button')?.addEventListener('click', () => {
        document.getElementById('santri-form').reset();
        document.getElementById('santri_kelas_semester_id').value = '';
        document.getElementById('santri-method').value = 'POST';
        document.getElementById('modal-title').textContent = 'Tambah Santri';
        document.getElementById('santri-form').action = "{{ route('akademik.rapor.storeSantri', $kelasSemester->id) }}";
        // Populate santri list
        const santriSelect = document.getElementById('santri_id');
        santriSelect.innerHTML = '<option value="">Pilih Santri</option>';
        @foreach (App\Models\Santri::whereDoesntHave('kelasSemesters', fn($query) => $query->where('kelas_semester_id', $kelasSemester->id))->get() as $santri)
            santriSelect.innerHTML += `<option value="{{ $santri->id }}">{{ $santri->nama_lengkap }} (NIS: {{ $santri->nis }})</option>`;
        @endforeach
        document.getElementById('santri-modal').classList.remove('hidden');
    });

    // Buka modal edit santri
    document.querySelectorAll('.edit-santri-button').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            fetch(`{{ url('akademik/rapor') }}/{{ $kelasSemester->id }}/${id}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data santri.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('santri_kelas_semester_id').value = data.data.id;
                    document.getElementById('santri_id').value = data.data.santri_id;
                    document.getElementById('modal-title').textContent = 'Edit Santri';
                    document.getElementById('santri-form').action = `{{ url('akademik/rapor') }}/{{ $kelasSemester->id }}/${data.data.id}`;
                    document.getElementById('santri-method').value = 'PUT';
                    // Populate santri list
                    const santriSelect = document.getElementById('santri_id');
                    santriSelect.innerHTML = '<option value="">Pilih Santri</option>';
                    santriSelect.innerHTML += `<option value="${data.data.santri_id}" selected>${data.data.santri_name} (NIS: ${data.data.santri_nis})</option>`;
                    data.data.santri_list.forEach(santri => {
                        santriSelect.innerHTML += `<option value="${santri.id}">${santri.name} (NIS: ${santri.nis})</option>`;
                    });
                    document.getElementById('santri-modal').classList.remove('hidden');
                } else {
                    Swal.fire('Error!', data.message || 'Gagal mengambil data santri.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', error.message || 'Gagal mengambil data santri.', 'error');
            });
        });
    });

    // Tutup modal santri
    document.getElementById('close-santri-modal')?.addEventListener('click', () => {
        document.getElementById('santri-modal').classList.add('hidden');
    });

    document.getElementById('cancel-santri-button')?.addEventListener('click', () => {
        document.getElementById('santri-modal').classList.add('hidden');
    });

    // Handle form tambah/edit santri
    document.getElementById('santri-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const id = document.getElementById('santri_kelas_semester_id').value;
        const url = id ? `{{ url('akademik/rapor') }}/{{ $kelasSemester->id }}/${id}` : this.action;
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
                return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
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
            console.error('Error:', error);
            Swal.fire('Error!', error.message || 'Terjadi kesalahan.', 'error');
        });
    });

    // Handle tombol delete santri
    document.querySelectorAll('.delete-santri-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus santri ini dari kelas?',
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
                            return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
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
                        console.error('Error:', error);
                        Swal.fire('Error!', error.message || 'Terjadi kesalahan.', 'error');
                    });
                }
            });
        });
    });

    // Handle tombol input rapor
    document.querySelectorAll('.input-rapor-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const hasMapel = this.getAttribute('data-has-mapel') === 'true';
            if (!hasMapel) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Belum ada mata pelajaran untuk kelas ini.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            window.location.href = this.getAttribute('data-href');
        });
    });
});
</script>
@endsection
