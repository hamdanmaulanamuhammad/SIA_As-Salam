<!-- resources/views/admin/data-pengajar-admin.blade.php -->
@extends('layouts.admin')

@section('title', 'Pengajar')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mt-6 mb-4">Data Pengajar</h2>

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
                        <th class="px-4 py-3">Detail</th>
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
                        <td class="px-4 py-3">
                            <button class="text-red-600" onclick="confirmDelete('{{ $teacher->id }}', '{{ $teacher->full_name }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('teachers.detail', $teacher->id) }}" class="text-blue-600">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                                    location.reload();
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
