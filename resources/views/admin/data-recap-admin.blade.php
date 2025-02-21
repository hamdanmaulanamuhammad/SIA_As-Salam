@extends('layouts.admin')

@section('title', 'Rekap Data')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-end mb-6 mt-6">
            <button id="create-recap-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus"></i> Rekap Data
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Modal untuk menambahkan/mengedit data rekap -->
        <div id="recap-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md max-h-screen overflow-y-auto">
                <span id="close-recap-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
                <h2 class="text-xl font-bold mb-4" id="modal-title">Tambah Rekap Data</h2>
                
                <form id="recap-form" method="POST" action="{{ route('recap.store') }}">
                    @csrf
                    <div id="method-field"></div>
                    <input type="hidden" id="recap-id" name="recap_id">
                    
                    <div class="mb-4">
                        <label for="nama_rekap" class="block text-sm font-medium text-gray-700">Nama Rekap</label>
                        <input type="text" id="nama_rekap" name="nama_rekap" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        @error('nama_rekap')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="periode" class="block text-sm font-medium text-gray-700">Periode</label>
                        <input type="month" id="periode" name="periode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        @error('periode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="batas_keterlambatan" class="block text-sm font-medium text-gray-700">Batas Keterlambatan</label>
                        <input type="time" id="batas_keterlambatan" name="batas_keterlambatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        @error('batas_keterlambatan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="mukafaah" class="block text-sm font-medium text-gray-700">Mukafaah</label>
                        <input type="number" id="mukafaah" name="mukafaah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        @error('mukafaah')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="bonus" class="block text-sm font-medium text-gray-700">Bonus</label>
                        <input type="number" id="bonus" name="bonus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        @error('bonus')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" id="cancel-btn" class="text-sm px-4 py-2 text-white bg-gray-500 border border-gray-500 rounded-md hover:bg-gray-600 transition duration-200 mr-2">
                            Batal
                        </button>
                        <button type="submit" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Rekap Data -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Rekap</th>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3 min-w-32">Aksi</th>
                            <th class="px-4 py-3">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y" id="recap-table-body">
                        @forelse($recaps as $index => $recap)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $recap->nama_rekap }}</td>
                                <td class="px-4 py-3">{{ date('F', strtotime($recap->periode)) }}</td>
                                <td class="px-4 py-3">{{ date('Y', strtotime($recap->periode)) }}</td>
                                <td class="px-4 py-3 flex">
                                    <button class="edit-button text-xl text-blue-600 hover:text-blue-800 mr-2" 
                                            data-id="{{ $recap->id }}"
                                            data-nama="{{ $recap->nama_rekap }}"
                                            data-periode="{{ date('Y-m', strtotime($recap->periode)) }}"
                                            data-batas="{{ $recap->batas_keterlambatan }}"
                                            data-mukafaah="{{ $recap->mukafaah }}"
                                            data-bonus="{{ $recap->bonus }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('recap.destroy', $recap->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-button text-red-600 text-xl hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('recap.show', $recap->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center">
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-gray-700">
                                <td colspan="6" class="px-4 py-3 text-center">Tidak ada data rekap</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to reset modal form
        function resetForm() {
            document.getElementById('recap-form').reset();
            document.getElementById('method-field').innerHTML = '';
            document.getElementById('recap-form').action = "{{ route('recap.store') }}";
            document.getElementById('modal-title').innerText = 'Tambah Rekap Data';
        }

        // Function to open modal
        function openModal() {
            document.getElementById('recap-modal').classList.remove('hidden');
            document.getElementById('recap-modal').classList.add('flex');
        }

        // Function to close modal
        function closeModal() {
            document.getElementById('recap-modal').classList.remove('flex');
            document.getElementById('recap-modal').classList.add('hidden');
            resetForm();
        }

        // Menangani klik tombol buat rekap
        document.getElementById('create-recap-button').addEventListener('click', function () {
            resetForm();
            openModal();
        });

        // Menangani klik tombol tutup modal
        document.getElementById('close-recap-modal').addEventListener('click', function () {
            closeModal();
        });

        // Menangani klik tombol batal di modal
        document.getElementById('cancel-btn').addEventListener('click', function () {
            closeModal();
        });

        // Menangani klik tombol edit
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const periode = this.getAttribute('data-periode');
                const batas = this.getAttribute('data-batas');
                const mukafaah = this.getAttribute('data-mukafaah');
                const bonus = this.getAttribute('data-bonus');

                // Update form for edit
                document.getElementById('method-field').innerHTML = `@method('PUT')`;
                document.getElementById('recap-form').action = `/data-recap-admin/${id}`;
                document.getElementById('modal-title').innerText = 'Edit Rekap Data';
                
                // Fill form with existing data
                document.getElementById('nama_rekap').value = nama;
                document.getElementById('periode').value = periode;
                document.getElementById('batas_keterlambatan').value = batas;
                document.getElementById('mukafaah').value = mukafaah;
                document.getElementById('bonus').value = bonus;
                
                openModal();
            });
        });

        // Menangani klik tombol hapus
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const form = this.closest('.delete-form');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Handle form submission with validation
        document.getElementById('recap-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const action = form.getAttribute('action');
            const method = document.getElementById('method-field').innerHTML.includes('PUT') ? 'PUT' : 'POST';
            
            // Send AJAX request
            fetch(action, {
                method: method === 'PUT' ? 'POST' : method, // For PUT, we still send as POST but with _method=PUT
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message || 'Data telah disimpan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload the page to show the updated data
                        window.location.reload();
                    });
                } else {
                    let errorMessage = 'Terjadi kesalahan dalam menyimpan data.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        });

        // Display SweetAlert for session messages
        @if(session('success'))
            Swal.fire({
                title: 'Sukses!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
@endsection