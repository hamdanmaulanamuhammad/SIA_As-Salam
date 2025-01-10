@extends('layouts.admin')

@section('title', 'Registrasi')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-6">Data Permintaan Registrasi</h2>

        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter dan Entries -->
        <div class="md:flex md:justify-between">
            <div class="flex space-x-4 mb-6">
                <select id="year-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Tahun</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                </select>
                <select id="month-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
                <button id="filter-button" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 text-sm">Terapkan Filter</button>
            </div>
            <div class="flex items-center">
                <label for="entries" class="text-sm font-medium text-gray-700">Show</label>
                <select id="entries" class="ml-2 border border-gray-300 rounded-md">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
                <span class="ml-2 text-sm text-gray-600">entries</span>
            </div>
        </div>

        <!-- Tabel Permintaan Pendaftaran -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No Telp</th>
                        <th class="px-4 py-3">Waktu Registrasi</th>
                        <th class="px-4 py-3">Tempat Kuliah</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3 min-w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($users as $user)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $user->id }}</td> <!-- Menggunakan ID sebagai NIP -->
                        <td class="px-4 py-3">{{ $user->full_name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->phone }}</td>
                        <td class="px-4 py-3">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $user->university }}</td>
                        <td class="px-4 py-3">{{ $user->address }}</td>
                        <td class="px-4 py-3">
                            <!-- Form untuk menerima pendaftaran -->
                            <form action="{{ route('registration.accept', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="px-2 py-1 w-8 text-white bg-green-600 rounded-md" onclick="confirmAccept('{{ $user->full_name }}', {{ $user->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <!-- Form untuk menolak pendaftaran -->
                            <form action="{{ route('registration.reject', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="px-2 py-1 w-8 text-white bg-red-600 rounded-md" onclick="confirmReject('{{ $user->full_name }}', {{ $user->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="flex items-center justify-between mt-4 mb-10">
                <span class="text-sm text-gray-700">Showing {{ $users->count() }} of {{ $users->total() }} entries</span>
                <div>
                    {{ $users->links() }} <!-- Pagination links -->
                </div>
            </div>
        </div>
    </div>

    <script>
     // Fungsi untuk mengonfirmasi penerimaan pendaftaran
    function confirmAccept(name, id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin menerima pendaftaran pengajar ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, terima',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim permintaan untuk menerima pendaftaran
                fetch(`/registration/accept/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Sukses!', `Pendaftaran pengajar ${name} telah diterima.`, 'success')
                            .then(() => {
                                location.reload(); // Reload halaman untuk memperbarui tampilan
                            });
                    } else {
                        Swal.fire('Error!', data.message || 'Terjadi kesalahan saat menerima pendaftaran.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menerima pendaftaran.', 'error');
                });
            }
        });
    }

    // Fungsi untuk mengonfirmasi penolakan pendaftaran
    function confirmReject(name, id) {
        Swal.fire({
            title: 'Konfirmasi Tolak Pendaftaran',
            text: `Apakah Anda yakin ingin menolak pendaftaran pengajar ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim permintaan untuk menolak pendaftaran
                fetch(`/registration/reject/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Sukses!', `Pendaftaran pengajar ${name} telah ditolak.`, 'success')
                            .then(() => {
                                location.reload(); // Reload halaman untuk memperbarui tampilan
                            });
                    } else {
                        Swal.fire('Error!', data.message || 'Terjadi kesalahan saat menolak pendaftaran.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menolak pendaftaran.', 'error');
                });
            }
        });
    }
</script>
@endsection
