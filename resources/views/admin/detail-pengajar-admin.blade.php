@extends('layouts.admin')

@section('title', 'Detail Pengajar')

@section('content')
    <div class="container mx-auto p-4 sm:p-6">
        <!-- Teacher Profile -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center">
                    <img class="h-24 w-24 sm:h-32 sm:w-32 rounded-full border-4 border-blue-500 object-cover"
                         src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : asset('images/default-avatar.png') }}"
                         alt="Profile Photo">
                    <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $teacher->full_name }}</h1>
                        <p class="mt-2 text-gray-500 text-sm sm:text-base">{{ $teacher->university }}</p>
                    </div>
                </div>
                <button onclick="openResetPasswordModal()" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">Reset Password</button>
            </div>

            <div class="mt-6 border-t-2 border-gray-100 pt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak</h3>
                            <div class="mt-2 space-y-2">
                                <p class="flex items-center text-gray-600 text-sm sm:text-base">
                                    <svg class="h-5 w-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ $teacher->email }}
                                </p>
                                <p class="flex items-center text-gray-600 text-sm sm:text-base">
                                    <svg class="h-5 w-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    {{ $teacher->phone }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat</h3>
                            <p class="mt-2 text-gray-600 text-sm sm:text-base">{{ $teacher->address }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Informasi</h3>
                            <div class="mt-2 space-y-2">
                                <p class="text-gray-600 text-sm sm:text-base"><span class="font-medium">ID:</span> {{ $teacher->id }}</p>
                                <p class="text-gray-600 text-sm sm:text-base"><span class="font-medium">Username:</span> {{ $teacher->username }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Information (Only for pengajar) -->
        @if($isPengajar)
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                <h3 class="text-lg font-semibold">Informasi Kontrak</h3>
                <button onclick="openContractModal()" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">Tambah Kontrak</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full bg-white text-sm sm:text-base">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Tanggal Mulai</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Tanggal Selesai</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Status</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($contracts as $contract)
                        <tr class="text-gray-700">
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $contract->start_date }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $contract->end_date ?? '-' }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                <span class="{{ $contract->status == 'active' ? 'text-green-600' : ($contract->status == 'expired' ? 'text-red-600' : 'text-yellow-600') }}">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 flex gap-2">
                                <button onclick="openEditContractModal({{ $contract }})" class="text-blue-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDeleteContract('{{ $contract->id }}')" class="text-red-600">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $contracts->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
        @endif

        <!-- Attendance History -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Riwayat Presensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full bg-white text-sm sm:text-base">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Tanggal</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Hari</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Jam Masuk</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Jam Keluar</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Kelas</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Materi</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Bukti</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Kendala</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3">Saran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($presences as $presence)
                        <tr class="text-gray-700">
                            <td class="px-implement0 sm:px-4 py-2 sm:py-3">{{ $presence->date }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->day }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->arrival_time }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->end_time }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->class }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->material }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                @if($presence->proof)
                                    <img src="{{ asset('storage/' . $presence->proof) }}" alt="Proof" class="w-12 h-12 sm:w-16 sm:h-16 object-cover cursor-pointer"
                                         onclick="showProofImage('{{ asset('storage/' . $presence->proof) }}')">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->issues ?? '-' }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $presence->suggestion ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $presences->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Reset Password Pengajar</h3>
            <form id="resetPasswordForm" action="{{ route('teachers.reset-password', $teacher->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm sm:text-base">Password Baru</label>
                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm sm:text-base">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Contract Modal -->
    @if($isPengajar)
    <div id="contractModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-md">
            <h3 id="contractModalTitle" class="text-lg font-semibold mb-4">Tambah Kontrak</h3>
            <form id="contractForm" method="POST">
                @csrf
                <input type="hidden" id="contract_id" name="contract_id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm sm:text-base">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="contract_start_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm sm:text-base">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="contract_end_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm sm:text-base">Status</label>
                    <select name="status" id="contract_status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeContractModal()" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Reset Password Modal
        function openResetPasswordModal() {
            document.getElementById('resetPasswordModal').classList.remove('hidden');
            document.getElementById('resetPasswordForm').reset();
        }

        function closeResetPasswordModal() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
        }

        // Show Proof Image in SweetAlert
        function showProofImage(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: 'Bukti Mengajar',
                showConfirmButton: false,
                showCloseButton: true,
                width: '90%',
                padding: '1rem',
            });
        }

        // Contract Modal
        @if($isPengajar)
        function openContractModal() {
            document.getElementById('contractModalTitle').innerText = 'Tambah Kontrak';
            document.getElementById('contractForm').action = '{{ route("contracts.store", $teacher->id) }}';
            document.getElementById('contractForm').reset();
            document.getElementById('contract_id').value = '';
            document.getElementById('contractModal').classList.remove('hidden');
        }

        function openEditContractModal(contract) {
            document.getElementById('contractModalTitle').innerText = 'Edit Kontrak';
            document.getElementById('contractForm').action = '{{ route("contracts.update", [$teacher->id, ":contract_id"]) }}'.replace(':contract_id', contract.id);
            document.getElementById('contract_id').value = contract.id;
            document.getElementById('contract_start_date').value = contract.start_date;
            document.getElementById('contract_end_date').value = contract.end_date || '';
            document.getElementById('contract_status').value = contract.status;
            document.getElementById('contractModal').classList.remove('hidden');
        }

        function closeContractModal() {
            document.getElementById('contractModal').classList.add('hidden');
        }

        function confirmDeleteContract(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus kontrak ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("contracts.destroy", [$teacher->id, ":contract_id"]) }}'.replace(':contract_id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Dihapus!', 'Kontrak telah dihapus.', 'success')
                                .then(() => {
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Error!', data.message || 'Terjadi kesalahan saat menghapus kontrak.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus kontrak.', 'error');
                    });
                }
            });
        }

        // Handle Contract Form Submission
        document.getElementById('contractForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const method = form.querySelector('#contract_id').value ? 'PUT' : 'POST';
            fetch(form.action, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => response.json())
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
                Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
            });
        });
        @endif

        // Handle Reset Password Form Submission
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', 'Password telah direset.', 'success').then(() => {
                        closeResetPasswordModal();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Terjadi kesalahan saat mereset password.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat mereset password.', 'error');
            });
        });

        // Update day field based on date selection
        document.getElementById('presence_date').addEventListener('change', function() {
            const date = new Date(this.value);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            document.getElementById('presence_day').value = days[date.getDay()];
        });
    </script>
@endsection
