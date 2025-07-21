@extends('layouts.admin')

@section('title', 'Presensi Pengajar')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-end mb-6 mt-6">
            <button id="create-presence-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus"></i> Tambah Presensi
            </button>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mb-6">Presensi Hari Ini</h3>
        <div class="overflow-x-auto">
            <table class="w-full mt-4 bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Hari Mengajar</th>
                        <th class="px-4 py-3">Nama Pengajar</th>
                        <th class="px-4 py-3">Waktu Kedatangan</th>
                        <th class="px-4 py-3">Waktu Selesai</th>
                        <th class="px-4 py-3">Bukti Mengajar</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($presences as $index => $presence)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ $presence->created_at }}</td>
                            <td class="px-4 py-3">{{ $presence->date }}</td>
                            <td class="px-4 py-3">{{ $presence->user->full_name }}</td>
                            <td class="px-4 py-3">{{ $presence->arrival_time }}</td>
                            <td class="px-4 py-3">{{ $presence->end_time }}</td>
                            <td class="px-4 py-3">
                                @if($presence->proof)
                                    <a href="#" class="preview-image w-0.5 h-2/3" data-src="{{ asset('storage/' . $presence->proof) }}">
                                        <img src="{{ asset('storage/' . $presence->proof) }}" alt="Bukti Mengajar" class="w-10 h-10 rounded-md">
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex justify-between min-w-26">
                                <button class="edit-button text-blue-600 hover:text-blue-800 mr-2" data-id="{{ $presence->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('presence.destroy', $presence->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

         <h3 class="text-lg font-semibold text-gray-800 mb-6 mt-8">Riwayat Kehadiran Seluruh Pengajar</h3>

        <!-- Filter dan Search -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Filter Nama Pengajar -->
                    <select id="pengajarFilter" name="pengajar" class="w-full md:w-48 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">Semua Pengajar</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('pengajar') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Tahun -->
                    <select id="tahunFilter" name="tahun" class="w-full md:w-32 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300 min-w-40">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Bulan -->
                    <select id="bulanFilter" name="bulan" class="w-full md:w-32 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300 min-w-40">
                        <option value="">Semua Bulan</option>
                        <option value="1" {{ request('bulan') == '1' ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ request('bulan') == '2' ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ request('bulan') == '3' ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ request('bulan') == '4' ? 'selected' : '' }}>April</option>
                        <option value="5" {{ request('bulan') == '5' ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ request('bulan') == '6' ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ request('bulan') == '7' ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ request('bulan') == '8' ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ request('bulan') == '9' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>

                <!-- Entries Display -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select id="entriesSelect" name="per_page" class="px-2 py-1 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-600">entries</span>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Kehadiran -->
        <div class="overflow-x-auto">
            <table class="w-full mt-4 bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Nama Pengajar</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Waktu Kedatangan</th>
                        <th class="px-4 py-3">Waktu Selesai</th>
                        <th class="px-4 py-3">Materi</th>
                        <th class="px-4 py-3">Bukti Mengajar</th>
                        <th class="px-4 py-3">Kendala</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="bg-white divide-y">
                    @foreach($historyPresences as $index => $presence)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-4 py-3 text-sm">{{ ($historyPresences->currentPage() - 1) * $historyPresences->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($presence->date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $presence->user->full_name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $presence->class }}</td>
                            <td class="px-4 py-3 text-sm">{{ $presence->arrival_time }}</td>
                            <td class="px-4 py-3 text-sm">{{ $presence->end_time }}</td>
                            <td class="px-4 py-3 text-sm max-w-32 truncate" title="{{ $presence->material }}">{{ $presence->material }}</td>
                            <td class="px-4 py-3">
                                @if($presence->proof)
                                    <a href="#" class="preview-image" data-src="{{ asset('storage/' . $presence->proof) }}">
                                        <img src="{{ asset('storage/' . $presence->proof) }}" alt="Bukti Mengajar" class="w-10 h-10 rounded-md">
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm max-w-32 truncate" title="{{ $presence->issues }}">{{ $presence->issues ?: '-' }}</td>
                            <td class="px-4 py-3 flex justify-between min-w-26">
                                <button class="edit-button text-blue-600 hover:text-blue-800 mr-2" data-id="{{ $presence->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('presence.destroy', $presence->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($showPagination)
            <div class="mt-6 mb-6">
                {{ $historyPresences->links() }}
            </div>
        @endif

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
                        <select id="user_id" name="user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            <option value="">Pilih Pengajar</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <img id="presence-proof-preview" class="mt-2 w-full rounded-md shadow-md hidden" alt="Bukti Mengajar Sebelumnya">
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
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Menangani tombol untuk membuka form presensi
    document.getElementById('create-presence-button')?.addEventListener('click', () => {
        // Reset form
        document.getElementById('presence-form').reset();
        document.getElementById('presence-id').value = '';
        document.getElementById('presence-method').value = 'POST';
        document.getElementById('presence-proof-preview').classList.add('hidden');
        document.getElementById('presence-proof').setAttribute('required', 'required');
        document.getElementById('presence-form').action = "{{ route('presence.store') }}";
        document.getElementById('presence-form-modal').classList.remove('hidden');
    });

    // Menangani penutupan modal form presensi
    document.getElementById('close-presence-form-modal')?.addEventListener('click', () => {
        document.getElementById('presence-form-modal')?.classList.add('hidden');
    });

    document.getElementById('cancel-presence-form-button')?.addEventListener('click', () => {
        document.getElementById('presence-form-modal')?.classList.add('hidden');
    });

    // Menangani pengiriman form presensi dengan AJAX
    document.getElementById('presence-form')?.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const id = document.getElementById('presence-id')?.value;
        const url = id ? `/presence-admin/${id}` : this.action;
        formData.append('_method', id ? 'PUT' : 'POST');

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
            Swal.fire({
                title: data.success ? "Berhasil!" : "Gagal!",
                text: data.message,
                icon: data.success ? "success" : "error",
                confirmButtonText: "OK"
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
            location.reload();
        });
    });

    // Menangani preview gambar bukti mengajar
    document.querySelectorAll('a.preview-image').forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            Swal.fire({
                html: `<img src="${link.getAttribute('data-src')}" style="display: block; margin: auto; max-width: 90%; max-height: 80vh;" />`,
                showCloseButton: true,
                showConfirmButton: false,
                width: 'auto',
                padding: '0',
                backdrop: 'rgba(0,0,0,0.5)',
            });
        });
    });

    // Menangani tombol edit
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            fetch(`/presence-admin/edit/${button.getAttribute('data-id')}`)
                .then(response => response.json())
                .then(data => {
                    if (data.proof) {
                        document.getElementById('presence-proof-existing').value = data.proof;
                        document.getElementById('presence-proof-preview').src = "{{ asset('storage') }}/" + data.proof;
                        document.getElementById('presence-proof-preview').classList.remove('hidden');
                        document.getElementById('presence-proof').removeAttribute('required');
                    }
                    document.getElementById('presence-id').value = data.id;
                    document.getElementById('user_id').value = data.user_id;
                    document.getElementById('teaching-day').value = data.date;
                    document.getElementById('arrival-time').value = data.arrival_time.split(':').slice(0,2).join(':');
                    document.getElementById('end-time').value = data.end_time.split(':').slice(0,2).join(':');
                    document.getElementById('class').value = data.class;
                    document.getElementById('material').value = data.material;
                    document.getElementById('issues').value = data.issues;
                    document.getElementById('presence-form').action = `/presence-admin/${data.id}`;
                    document.getElementById('presence-method').value = 'PUT';
                    document.getElementById('presence-form-modal').classList.remove('hidden');
                });
        });
    });

    // Menangani tombol delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
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
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            location.reload();
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        location.reload();
                    });
                }
            });
        });
    });

    // Fungsi untuk memuat tabel riwayat dengan AJAX
    function loadHistoryTable() {
        const pengajar = document.getElementById('pengajarFilter').value;
        const tahun = document.getElementById('tahunFilter').value;
        const bulan = document.getElementById('bulanFilter').value;
        const perPage = document.getElementById('entriesSelect').value;

        const params = new URLSearchParams({
            pengajar: pengajar,
            tahun: tahun,
            bulan: bulan,
            per_page: perPage
        });

        // Hapus parameter kosong
        Object.keys(Object.fromEntries(params)).forEach(key => {
            if (!params.get(key)) {
                params.delete(key);
            }
        });

        const url = window.location.pathname + '?' + params.toString();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Parse HTML dari respons JSON
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(data.table, 'text/html');
            const newTableBody = newDoc.querySelector('#historyTableBody');
            const paginationContainer = document.querySelectorAll('.mt-6')[document.querySelectorAll('.mt-6').length - 1];

            if (newTableBody) {
                document.querySelector('#historyTableBody').innerHTML = newTableBody.innerHTML;
            }

            // Perbarui paginasi hanya jika ada
            if (data.pagination && paginationContainer) {
                const newPagination = parser.parseFromString(data.pagination, 'text/html').body.firstChild;
                paginationContainer.innerHTML = newPagination ? newPagination.outerHTML : '';
            } else if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }

            // Re-bind event listeners untuk tombol edit dan delete yang baru
            bindHistoryTableEvents();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat memuat data.', 'error');
        });
    }

    // Fungsi untuk mengikat ulang event listener
    function bindHistoryTableEvents() {
        // Re-bind preview image events
        document.querySelectorAll('a.preview-image').forEach(link => {
            link.removeEventListener('click', handlePreviewClick);
            link.addEventListener('click', handlePreviewClick);
        });

        // Re-bind edit button events
        document.querySelectorAll('.edit-button').forEach(button => {
            button.removeEventListener('click', handleEditClick);
            button.addEventListener('click', handleEditClick);
        });

        // Re-bind delete form events
        document.querySelectorAll('.delete-form').forEach(form => {
            form.removeEventListener('submit', handleDeleteSubmit);
            form.addEventListener('submit', handleDeleteSubmit);
        });
    }

    function handlePreviewClick(event) {
        event.preventDefault();
        const link = event.currentTarget;
        Swal.fire({
            html: `<img src="${link.getAttribute('data-src')}" style="display: block; margin: auto; max-width: 90%; max-height: 80vh;" />`,
            showCloseButton: true,
            showConfirmButton: false,
            width: 'auto',
            padding: '0',
            backdrop: 'rgba(0,0,0,0.5)',
        });
    }

    function handleEditClick(event) {
        const button = event.currentTarget;
        fetch(`/presence-admin/edit/${button.getAttribute('data-id')}`)
            .then(response => response.json())
            .then(data => {
                if (data.proof) {
                    document.getElementById('presence-proof-existing').value = data.proof;
                    document.getElementById('presence-proof-preview').src = "{{ asset('storage') }}/" + data.proof;
                    document.getElementById('presence-proof-preview').classList.remove('hidden');
                    document.getElementById('presence-proof').removeAttribute('required');
                }
                document.getElementById('presence-id').value = data.id;
                document.getElementById('user_id').value = data.user_id;
                document.getElementById('teaching-day').value = data.date;
                document.getElementById('arrival-time').value = data.arrival_time.split(':').slice(0,2).join(':');
                document.getElementById('end-time').value = data.end_time.split(':').slice(0,2).join(':');
                document.getElementById('class').value = data.class;
                document.getElementById('material').value = data.material;
                document.getElementById('issues').value = data.issues;
                document.getElementById('presence-form').action = `/presence-admin/${data.id}`;
                document.getElementById('presence-method').value = 'PUT';
                document.getElementById('presence-form-modal').classList.remove('hidden');
            });
    }

    function handleDeleteSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
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
                            loadHistoryTable();
                        });
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                        loadHistoryTable();
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    loadHistoryTable();
                });
            }
        });
    }

    // Event listeners untuk filter
    document.getElementById('pengajarFilter')?.addEventListener('change', loadHistoryTable);
    document.getElementById('tahunFilter')?.addEventListener('change', loadHistoryTable);
    document.getElementById('bulanFilter')?.addEventListener('change', loadHistoryTable);
    document.getElementById('entriesSelect')?.addEventListener('change', loadHistoryTable);

    // Initial binding
    bindHistoryTableEvents();
});
</script>
@endsection
