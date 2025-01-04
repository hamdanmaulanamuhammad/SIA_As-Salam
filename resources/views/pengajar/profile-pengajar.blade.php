@extends('layouts.pengajar')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-lg bg-white rounded-lg shadow-md p-6 md:mt-10">
            <div class="flex flex-col items-center space-y-5 sm:flex-row sm:space-y-0">
                <img class="object-cover w-40 h-40 p-1 rounded-full"
                    src="https://placehold.co/100x100" alt="Profile Picture">

                <div class="flex flex-col space-y-5 sm:ml-8">
                    <button type="button" id="change-photo-button"
                        class="py-3.5 px-7 text-base font-medium text-white focus:outline-none bg-blue-600 rounded-lg border border-blue-200 hover:bg-blue-700 focus:z-10 focus:ring-4 focus:ring-blue-200 ">
                        Ganti Foto
                    </button>
                    <button type="button" id="delete-photo-button"
                        class="py-3.5 px-7 text-base font-medium text-red-600 focus:outline-none bg-white rounded-lg border border-red-200 hover:bg-red-100 focus:z-10 focus:ring-4 focus:ring-red-200 ">
                        Hapus Foto
                    </button>
                </div>
            </div>

            <div class="mt-8">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" id="name" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="John Doe" readonly />
                </div>
                <div class="mb-4">
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" id="nip" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="123456789" readonly />
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="johndoe" readonly />
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" id="phone" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="08123456789" readonly />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="johndoe@example.com" readonly />
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
            <form id="edit-profile-form" class="mt-4">
                <div class="mb-4">
                    <label for="edit-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" id="edit-name-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="John Doe" required />
                </div>
                <div class="mb-4">
                    <label for="edit-username-input" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="edit-username-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="johndoe" required />
                </div>
                <div class="mb-4">
                    <label for="edit-phone-input" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" id="edit-phone-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="08123456789" required />
                </div>
                <div class="mb-4">
                    <label for="edit-email-input" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="edit-email-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="johndoe@example.com" required />
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-edit-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" id="save-button" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Ganti Foto -->
    <div id="change-photo-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-change-photo-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold">Ganti Foto Profil</h2>
            <form id="change-photo-form" class="mt-4">
                <input type="file" id="photo-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" accept="image/*" required />
                <div class="flex justify-end mt-4">
                    <button type="button" id="cancel-change-photo" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Ganti Foto</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inisialisasi modal dan event listener
        document.getElementById('edit-button').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
        });

        document.getElementById('close-edit-modal').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        });

        document.getElementById('cancel-edit-button').addEventListener('click', () => {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        });

        document.getElementById('edit-profile-form').addEventListener('submit', (event) => {
            event.preventDefault(); // Mencegah pengiriman form default

            // Mengambil nilai dari input
            const name = document.getElementById('edit-name-input').value;
            const username = document.getElementById('edit-username-input').value;
            const phone = document.getElementById('edit-phone-input').value;
            const email = document.getElementById('edit-email-input').value;

            // Menampilkan informasi yang telah diedit
            document.getElementById('name').value = name;
            document.getElementById('username').value = username;
            document.getElementById('phone').value = phone;
            document.getElementById('email').value = email;

            // Menutup modal
            document.getElementById('edit-profile-modal').classList.add('hidden');

            // Menampilkan SweetAlert sukses
            Swal.fire({
                title: 'Sukses!',
                text: 'Profile berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });

        // Ganti Foto
        document.getElementById('change-photo-button').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.remove('hidden');
        });

        document.getElementById('close-change-photo-modal').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.add('hidden');
        });

        document.getElementById('cancel-change-photo').addEventListener('click', () => {
            document.getElementById('change-photo-modal').classList.add('hidden');
        });

        document.getElementById('change-photo-form').addEventListener('submit', (event) => {
            event.preventDefault(); // Mencegah pengiriman form default

            // Logika untuk mengganti foto (misalnya, upload foto)
            const photoInput = document.getElementById('photo-input').files[0];
            if (photoInput) {
                // Simulasi penggantian foto
                Swal.fire({
                    title: 'Sukses!',
                    text: 'Foto profil berhasil diganti.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                document.getElementById('change-photo-modal').classList.add('hidden');
            }
        });

        // Hapus Foto
        document.getElementById('delete-photo-button').addEventListener('click', () => {
            // Logika untuk menghapus foto
            Swal.fire({
                title: 'Sukses!',
                text: 'Foto profil berhasil dihapus.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            // Anda dapat menambahkan logika tambahan di sini untuk memperbarui tampilan foto
        });
    </script>
@endsection
