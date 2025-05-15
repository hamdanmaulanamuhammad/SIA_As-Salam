@extends('layouts.admin')

@section('title', 'Pengajar')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-4">Detail Pengajar</h2>

        <!-- Tabel Detail Pengajar -->
        <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No Telp</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($teachers as $index => $teacher)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $teacher->id }}</td>
                        <td class="px-4 py-3">{{ $teacher->full_name }}</td>
                        <td class="px-4 py-3">{{ $teacher->email }}</td>
                        <td class="px-4 py-3">{{ $teacher->phone }}</td>
                        <td class="px-4 py-3 text-sm flex gap-2">
                            <button class="text-red-600" onclick="confirmDelete('{{ $teacher->id }}', '{{ $teacher->full_name }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-4 mb-10">
                <span class="text-sm text-gray-700">Showing 1-3 of 10 entries</span>
                <div>
                    <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Previous</button>
                    <button class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Next</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fungsi untuk mengonfirmasi penghapusan pengajar
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus pengajar ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan untuk menghapus pengajar
                    fetch(`/teachers/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Dihapus!', `Pengajar ${name} telah dihapus.`, 'success')
                                .then(() => {
                                    location.reload(); // Reload halaman untuk memperbarui tampilan
                                });
                        } else {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus pengajar.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus pengajar.', 'error');
                    });
                }
            });
        }
    </script>
@endsection
