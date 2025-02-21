@extends('layouts.pengajar')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-lg bg-white rounded-lg shadow-md p-6 md:mt-10">
            <div class="flex flex-col items-center space-y-5 sm:flex-row sm:space-y-0">
                <img class="object-cover w-40 h-40 p-1 rounded-full"
                    src="{{ $user->photo ? Storage::url($user->photo) : 'https://placehold.co/100x100' }}" alt="Profile Picture">

                <div class="flex flex-col space-y-5 sm:ml-8">
                    <button type="button" id="change-photo-button"
                        class="py-3.5 px-7 text-base font-medium text-white focus:outline-none bg-blue-600 rounded-lg border border-blue-200 hover:bg-blue-700 focus:z-10 focus:ring-4 focus:ring-blue-200 ">
                        Ganti Foto
                    </button>
                    <form action="{{ route('profile.pengajar-deletePhoto') }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')

                        <button type="submit" id="delete-photo-button"
                            class="py-3.5 px-7 text-base font-medium text-red-600 focus:outline-none bg-white rounded-lg border border-red-200 hover:bg-red-100 focus:z-10 focus:ring-4 focus:ring-red-200"
                            @if(auth()->user()->photo == null) disabled @endif>
                            Hapus Foto
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->full_name }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" id="nip" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->id }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->username }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" id="phone" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->phone }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->email }}" readonly />
                </div>
                <div class="flex justify-end">
                    <button type="button" id="edit-button" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit Profile -->
    <div id="edit-profile-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-edit-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold">Edit Profile</h2>
            <form action="{{ route('profile.pengajar-update') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="edit-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="full_name" id="edit-name-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->full_name }}" required />
                </div>
                <div class="mb-4">
                    <label for="edit-username-input" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit-username-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->username }}" required />
                </div>
                <div class="mb-4">
                    <label for="edit-phone-input" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" name="phone" id="edit-phone-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->phone }}" required />
                </div>
                <div class="mb-4">
                    <label for="edit-email-input" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit-email-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ $user->email }}" required />
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-edit-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Ganti Foto -->
    <div id="change-photo-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-change-photo-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold">Ganti Foto Profil</h2>
            <form action="{{ route('profile.pengajar-uploadPhoto') }}" method="POST" enctype="multipart/form-data" id="change-photo-form">
                @csrf
                <input type="file" name="photo" accept="image/*" required class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 mt-4" />
                <div class="flex justify-end mt-4">
                    <button type="button" id="cancel-change-photo" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Ganti Foto</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Menangani modal edit profil
        document.getElementById('edit-button').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
        });

        document.getElementById('close-edit-modal').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        });

        document.getElementById('cancel-edit-button').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        });

        // Menangani modal ganti foto
        document.getElementById('change-photo-button').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.remove('hidden');
        });

        document.getElementById('close-change-photo-modal').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.add('hidden');
        });

        document.getElementById('cancel-change-photo').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.add('hidden');
        });
        // Menangani penghapusan foto dengan SweetAlert
        document.getElementById('delete-photo-button').addEventListener('click', (event) => {
            event.preventDefault()
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus foto profil ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengkonfirmasi, kirim permintaan untuk menghapus foto
                    fetch('{{ route("profile.pengajar-deletePhoto") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Dihapus!',
                                'Foto profil Anda telah dihapus.',
                                'success'
                            ).then(() => {
                                location.reload(); 
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menghapus foto.',
                            'error'
                        );
                    });
                }
            });
        });

        // Menangani SweetAlert untuk pesan sukses
        @if (session('success'))
            Swal.fire({
                title: 'Sukses!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
