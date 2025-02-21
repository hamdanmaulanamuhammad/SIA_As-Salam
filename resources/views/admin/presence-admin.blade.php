@extends('layouts.admin')

@section('title', 'Presensi Pengajar')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-end mb-6 mt-6">
            <button id="create-presence-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus"></i> Presensi / Izin
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
                    @foreach($presences->where('type', 'presence')->values() as $presence)
                        @if($presence->type == 'presence')
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
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
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mb-6 mt-6">Izin Hari Ini</h3>
        <div class="overflow-x-auto">
            <table class="w-full mt-4 bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Nama Pengajar</th>
                        <th class="px-4 py-3">Hari Mengajar</th>
                        <th class="px-4 py-3">Subjek Izin</th>
                        <th class="px-4 py-3">Bukti Izin</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($presences->where('type', 'leave')->values() as $presence)
                        @if($presence->type == 'leave')
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $presence->created_at }}</td>
                                <td class="px-4 py-3">{{ $presence->user->full_name }}</td>
                                <td class="px-4 py-3">{{ $presence->date }}</td>
                                <td class="px-4 py-3">{{ $presence->leave_reason }}</td>
                                <td class="px-4 py-3">
                                    @if($presence->proof)
                                        <a href="#" class="preview-image w-0.5 h-2/3" data-src="{{ asset('storage/' . $presence->proof) }}">
                                            <img src="{{ asset('storage/' . $presence->proof) }}" alt="Bukti Izin" class="w-10 h-10 rounded-md">
                                        </a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 flex justify-evenly min-w-26">
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
                        @endif
                    @endforeach
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
                        <select id="user_id" name="user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            <option value="">Pilih Pengajar</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
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
                    <input type="hidden" name="_method" id="leave-form-method" value="POST">
                    <input type="hidden" name="type" value="leave">
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

    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Menangani tombol untuk membuka modal pilihan presensi/izin
    document.getElementById('create-presence-button')?.addEventListener('click', () => {
        document.getElementById('presence-modal')?.classList.remove('hidden');
    });

    // Menangani penutupan modal pilihan presensi/izin
    document.getElementById('close-presence-modal')?.addEventListener('click', () => {
        document.getElementById('presence-modal')?.classList.add('hidden');
    });

    // Menangani pilihan presensi
    document.getElementById('presence-option')?.addEventListener('click', () => {
        document.getElementById('presence-modal')?.classList.add('hidden');
        document.getElementById('presence-form-modal')?.classList.remove('hidden');
    });

    // Menangani pilihan izin
    document.getElementById('leave-option')?.addEventListener('click', () => {
        document.getElementById('presence-modal')?.classList.add('hidden');
        document.getElementById('leave-form-modal')?.classList.remove('hidden');
    });

    // Menangani penutupan modal form presensi
    document.getElementById('close-presence-form-modal')?.addEventListener('click', () => {
        document.getElementById('presence-form-modal')?.classList.add('hidden');
    });

    document.getElementById('cancel-presence-form-button')?.addEventListener('click', () => {
        document.getElementById('presence-form-modal')?.classList.add('hidden');
    });

    // Menangani penutupan modal form izin
    document.getElementById('close-leave-form-modal')?.addEventListener('click', () => {
        document.getElementById('leave-form-modal')?.classList.add('hidden');
    });

    document.getElementById('cancel-leave-form-button')?.addEventListener('click', () => {
        document.getElementById('leave-form-modal')?.classList.add('hidden');
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
                accept:'application/json'
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

    document.getElementById('leave-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        let formData = new FormData(event.target);
        try {
            let response = await fetch(document.getElementById('leave-form').action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                }
            });

            let result = await response.json();
            if (result.success) {
                document.getElementById('leave-form-modal').classList.add('hidden');
                Swal.fire({
                    title: "Sukses!",
                    text: "Izin berhasil disimpan.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK"
                }).then(() => {
                    location.reload(); 
                });

            } else {
                Swal.fire({
                    title: "Gagal!",
                    text: result.message || "Terjadi kesalahan saat menyimpan izin.",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK"
                }).then(() => {
                    location.reload();
                });
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                title: "Gagal!",
                text: "Terjadi kesalahan dalam mengirim data.",
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "OK"
            }).then(() => {
                location.reload();
            });
        }
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
                    if(data.type === 'leave') {
                        // Handle form izin
                        document.getElementById('leave-form').querySelector('[name="user_id"]').value = data.user_id;
                        document.getElementById('teaching-day').value = data.date;
                        document.getElementById('leave-subject').value = data.leave_reason;
                        
                        // Handle existing proof
                        if(data.proof) {
                            document.getElementById('leave-proof-existing').value = data.proof;
                            document.getElementById('leave-proof-preview').src = "{{ asset('storage') }}/" + data.proof;
                            document.getElementById('leave-proof-preview').classList.remove('hidden');
                            document.getElementById('leave-proof').removeAttribute('required');
                        }
                        
                        // Set form action untuk update
                        document.getElementById('leave-form').action = `/presence-admin/${data.id}`;
                        document.getElementById('leave-form-method').value = 'PUT';
                        
                        document.getElementById('leave-form-modal').classList.remove('hidden');
                    } else {
                        // Handle form presensi
                        // Handle existing proof
                        if(data.proof) {
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
                    }
                });
        });
    });

    // Menangani tombol delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah submit langsung

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
                        method: 'POST', // Laravel delete menggunakan form method spoofing
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Supaya Laravel mengenali sebagai AJAX
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

});
</script>
@endsection
