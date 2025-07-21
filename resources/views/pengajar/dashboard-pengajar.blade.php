@extends('layouts.pengajar')

@section('title', 'Dashboard')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="text-2xl font-semibold text-gray-700 mt-8">Statistik {{ $monthName }}</h2>
        <!-- Cards -->
        <div class="grid gap-6 mb-16 md:grid-cols-1 xl:grid-cols-2 mt-6">
            <!-- Card 1: Total Kehadiran Bulan ini -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-3 mr-4 bg-green-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total kehadiran.svg') }}" alt="Total Kehadiran" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Kehadiran Bulan ini</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $totalAttendance }}</p>
                </div>
            </div>

            <!-- Card 2: Masa Kontrak -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-3 mr-4 bg-blue-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/contract-icon.svg') }}" alt="Masa Kontrak" class="w-5 h-5" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Masa Kontrak</p>
                    <p class="text-lg font-semibold text-gray-700">
                        {{ $contractDuration ? $contractDuration . ' hari' : 'Belum ada kontrak aktif' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Presensi -->
        <div id="event-info" class="mb-6">
            <div class="bg-white rounded-lg shadow py-8 px-6 flex justify-between border border-gray-200">
                <div class="mb-2">
                    <h3 class="text-xl font-semibold text-gray-800">Presensi</h3>
                </div>

                <button id="attendance-button" class="my-auto w-8 h-8 text-white bg-green-600 rounded-md" onclick="openPresenceForm()">
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Tabel Riwayat Presensi -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Presensi</h3>

        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Hari Mengajar</th>
                        <th class="px-4 py-3">Waktu Kedatangan</th>
                        <th class="px-4 py-3">Waktu Selesai</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($recentPresences as $index => $presence)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ $presence->created_at->format('H:i, d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $presence->date }}</td>
                            <td class="px-4 py-3">{{ $presence->arrival_time }}</td>
                            <td class="px-4 py-3">{{ $presence->end_time }}</td>
                            <td class="px-4 py-3 min-w-24">
                                <button class="edit-button text-blue-500 hover:text-blue-700 mr-2" data-id="{{ $presence->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('pengajar.presence.destroy', $presence->id) }}" method="POST" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500">Belum ada data presensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal untuk Form Presensi -->
        <div id="presence-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
                <span id="close-presence-form-modal" class="float-right cursor-pointer text-gray-500">×</span>
                <h2 class="text-lg font-semibold">Form Presensi</h2>
                <form id="presence-form" action="{{ route('pengajar.presence.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="presence-id" name="id">
                    <input type="hidden" name="_method" id="presence-method" value="POST">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengajar</label>
                        <input type="text" id="user_name" value="{{ auth()->user()->full_name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" readonly />
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}" />
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
        // Fungsi untuk membuka form presensi langsung
        function openPresenceForm() {
            document.getElementById('presence-form-modal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('presence-form-modal').classList.add('hidden');
        }

        // Menambahkan event listener untuk tombol penutup modal
        document.getElementById('close-presence-form-modal').addEventListener('click', closeModal);

        // Menambahkan event listener untuk tombol batal pada form
        document.getElementById('cancel-presence-form-button').addEventListener('click', closeModal);

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

        // Menangani tombol edit
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', () => {
                fetch(`/presence-pengajar/edit/${button.getAttribute('data-id')}`)
                    .then(response => response.json())
                    .then(data => {
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
                        document.getElementById('user_name').value = data.user_name || "{{ auth()->user()->full_name }}";
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

        // Menambahkan event listener untuk form presensi
        document.getElementById('presence-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const id = document.getElementById('presence-id')?.value;
            const url = id ? `/presence/${id}` : this.action;
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
            });
        });
    </script>
@endsection
