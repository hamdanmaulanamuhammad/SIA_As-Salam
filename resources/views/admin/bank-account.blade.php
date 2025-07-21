@extends('layouts.admin')

@section('title', 'Kelola Rekening')

@section('content')
<div class="container px-6 mx-auto">
    <h1 class="text-2xl font-bold mb-6">Kelola Rekening</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">{{ session('success') }}</div>
    @endif
    <form action="{{ route('bank-accounts.store') }}" method="POST" class="mb-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                <input type="text" name="bank_name" id="bank_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
            <div>
                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                <input type="text" name="account_number" id="account_number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
            <div>
                <label for="account_holder" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik</label>
                <input type="text" name="account_holder" id="account_holder" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Tambah Rekening</button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full bg-white text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Bank</th>
                    <th class="px-4 py-3">Nomor Rekening</th>
                    <th class="px-4 py-3">Nama Pemilik</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($accounts as $index => $account)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $account->bank_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $account->account_number }}</td>
                    <td class="px-4 py-3 text-sm">{{ $account->account_holder }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex space-x-2">
                            <button class="edit-bank-account-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $account->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form action="{{ route('bank-accounts.destroy', $account->id) }}" method="POST" class="delete-bank-account-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<!-- Modal for Edit Rekening -->
<div id="bank-account-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Rekening</h3>
            <button id="close-bank-account-form-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="bank-account-form" action="" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="bank-account-id" name="id">
            <div class="mb-4">
                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                <input type="text" name="bank_name" id="bank_name_edit" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
            <div class="mb-4">
                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                <input type="text" name="account_number" id="account_number_edit" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
            <div class="mb-4">
                <label for="account_holder" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik</label>
                <input type="text" name="account_holder" id="account_holder_edit" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancel-bank-account-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk submit form edit rekening
        function submitBankAccountForm() {
            const form = document.getElementById('bank-account-form');
            const modal = document.getElementById('bank-account-form-modal');
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const id = form.querySelector('input[name="id"]').value;
                const formData = new FormData(form);

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';

                    if (data.success) {
                        modal.classList.add('hidden');
                        Swal.fire({
                            title: "Berhasil!",
                            text: data.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: data.message || 'Terjadi kesalahan.',
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';
                    Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                });
            });
        }

        // Fungsi untuk edit rekening
        function editBankAccount() {
            document.querySelectorAll('.edit-bank-account-button').forEach(button => {
                button.addEventListener('click', () => {
                    const itemId = button.getAttribute('data-id');

                    fetch(`/bank-accounts/${itemId}/edit`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const form = document.getElementById('bank-account-form');
                            form.querySelector('input[name="id"]').value = data.data.id;
                            document.getElementById('bank_name_edit').value = data.data.bank_name || '';
                            document.getElementById('account_number_edit').value = data.data.account_number || '';
                            document.getElementById('account_holder_edit').value = data.data.account_holder || '';
                            form.action = `/bank-accounts/${data.data.id}`;
                            document.getElementById('bank-account-form-modal').classList.remove('hidden');
                        } else {
                            Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Edit error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                    });
                });
            });
        }

        // Fungsi untuk hapus rekening
        function deleteBankAccount() {
            document.querySelectorAll('.delete-bank-account-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menghapus rekening ini?",
                        icon: "warning",
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
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Rekening berhasil dihapus.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || 'Gagal menghapus rekening.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus rekening.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                });
            });
        }

        // Modal Handlers
        document.getElementById('close-bank-account-form-modal').addEventListener('click', () => {
            document.getElementById('bank-account-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-bank-account-form-button').addEventListener('click', () => {
            document.getElementById('bank-account-form-modal').classList.add('hidden');
        });

        // Inisialisasi fungsi
        submitBankAccountForm();
        editBankAccount();
        deleteBankAccount();
    });
</script>
@endsection
