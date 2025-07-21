@extends('layouts.pengajar')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-md p-6 md:mt-10">
            <div class="flex flex-col items-center space-y-5 sm:flex-row sm:space-y-0">
                <img class="object-cover w-40 h-40 p-1 rounded-full"
                    src="{{ $user->photo ? Storage::url($user->photo) : 'https://placehold.co/100x100' }}"
                    alt="Profile Picture">

                <div class="flex flex-col space-y-5 sm:ml-8">
                    <button type="button" id="change-photo-button"
                        class="py-3.5 px-7 text-base font-medium text-white focus:outline-none bg-blue-600 rounded-lg border border-blue-200 hover:bg-blue-700 focus:z-10 focus:ring-4 focus:ring-blue-200">
                        Ganti Foto
                    </button>
                    <form action="{{ route('profile.pengajar.deletePhoto') }}" method="POST" class="inline-block" id="delete-photo-form">
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

            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Tanda Tangan</h3>
                <div class="flex flex-col items-center space-y-4">
                    @if($user->signature)
                        <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                            <img src="{{ Storage::url($user->signature) }}" alt="Tanda Tangan" class="max-w-full h-auto"
                                style="max-width: 456px; max-height: 231px;">
                        </div>
                    @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                            <p class="text-gray-500">Belum ada tanda tangan</p>
                        </div>
                    @endif

                    <div class="flex space-x-4">
                        <button type="button" id="upload-signature-button"
                            class="py-2.5 px-5 text-sm font-medium text-white focus:outline-none bg-green-600 rounded-lg border border-green-200 hover:bg-green-700 focus:z-10 focus:ring-4 focus:ring-green-200">
                            {{ $user->signature ? 'Ganti Tanda Tangan' : 'Upload Tanda Tangan' }}
                        </button>

                        @if($user->signature)
                            <form action="{{ route('profile.pengajar.deleteSignature') }}" method="POST" id="delete-signature-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="delete-signature-button"
                                    class="py-2.5 px-5 text-sm font-medium text-red-600 focus:outline-none bg-white rounded-lg border border-red-200 hover:bg-red-100 focus:z-10 focus:ring-4 focus:ring-red-200">
                                    Hapus Tanda Tangan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->full_name }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" id="nip"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->id }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->username }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" id="phone"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->phone }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->email }}" readonly />
                </div>
                <div class="flex justify-end">
                    <button type="button" id="edit-button"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                        Edit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit Profile -->
    <div id="edit-profile-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-edit-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold">Edit Profile</h2>
            <form id="edit-profile-form" action="{{ route('profile.pengajar.update') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="edit-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="full_name" id="edit-name-input"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->full_name }}" required />
                    <span id="edit-name-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-username-input" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit-username-input"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->username }}" required />
                    <span id="edit-username-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-phone-input" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" name="phone" id="edit-phone-input"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->phone }}" required />
                    <span id="edit-phone-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-email-input" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit-email-input"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"
                        value="{{ $user->email }}" required />
                    <span id="edit-email-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-edit-button"
                        class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Ganti Foto -->
    <div id="change-photo-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <span id="close-change-photo-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold">Ganti Foto Profil</h2>
            <form id="change-photo-form" action="{{ route('profile.pengajar.uploadPhoto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="photo" accept="image/*" required
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 mt-4" />
                <span id="photo_error" class="text-red-500 text-sm hidden"></span>
                <div class="flex justify-end mt-4">
                    <button type="button" id="cancel-change-photo"
                        class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Ganti Foto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Upload Tanda Tangan -->
    <div id="upload-signature-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-2xl">
            <span id="close-signature-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold">Upload Tanda Tangan</h2>
            <p class="text-sm text-gray-600 mb-4">Pilih gambar tanda tangan, kemudian crop sesuai area yang diinginkan
                (akan diresize ke 912x462px)</p>
            <form id="signature-form" action="{{ route('profile.pengajar.uploadSignature') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="signature" id="signature-input" accept="image/*" required
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                    <span id="signature_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div id="cropper-container" class="hidden mb-4">
                    <img id="signature-preview" class="max-w-full" />
                </div>
                <input type="hidden" name="x" id="crop-x" />
                <input type="hidden" name="y" id="crop-y" />
                <input type="hidden" name="width" id="crop-width" />
                <input type="hidden" name="height" id="crop-height" />
                <div class="flex justify-end">
                    <button type="button" id="cancel-signature-upload"
                        class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" id="upload-signature-btn"
                        class="px-4 py-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700"
                        disabled>Upload Tanda Tangan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let cropper;

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

        // Menangani modal upload tanda tangan
        document.getElementById('upload-signature-button').addEventListener('click', () => {
            document.getElementById('upload-signature-modal').classList.remove('hidden');
        });

        document.getElementById('close-signature-modal').addEventListener('click', () => {
            document.getElementById('upload-signature-modal').classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        document.getElementById('cancel-signature-upload').addEventListener('click', () => {
            document.getElementById('upload-signature-modal').classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Menangani upload dan crop tanda tangan
        document.getElementById('signature-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('signature-preview');
                    preview.src = e.target.result;
                    document.getElementById('cropper-container').classList.remove('hidden');

                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(preview, {
                        aspectRatio: 912 / 462,
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true,
                        crop: function(event) {
                            document.getElementById('crop-x').value = Math.round(event.detail.x);
                            document.getElementById('crop-y').value = Math.round(event.detail.y);
                            document.getElementById('crop-width').value = Math.round(event.detail.width);
                            document.getElementById('crop-height').value = Math.round(event.detail.height);
                            document.getElementById('upload-signature-btn').disabled = false;
                        }
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // Menangani edit profil dengan AJAX
        document.getElementById('edit-profile-form').addEventListener('submit', function(event) {
            event.preventDefault();
            document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('input').forEach(el => el.classList.remove('border-red-500'));

            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(field => {
                if (!field.value) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    document.getElementById(`${field.id}_error`).textContent = `${field.name} wajib diisi.`;
                    document.getElementById(`${field.id}_error`).classList.remove('hidden');
                }
            });

            if (!isValid) {
                Swal.fire('Perhatian!', 'Harap isi semua field yang wajib diisi.', 'warning');
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.success ? 'Berhasil!' : 'Gagal!',
                    text: data.message || (data.success ? 'Profil berhasil diperbarui.' : 'Gagal memperbarui profil.'),
                    icon: data.success ? 'success' : 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (data.success) {
                        document.getElementById('edit-profile-modal').classList.add('hidden');
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat memperbarui profil.', 'error');
            });
        });

        // Menangani upload foto dengan AJAX
        document.getElementById('change-photo-form').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('photo_error').classList.add('hidden');
            document.querySelector('input[name="photo"]').classList.remove('border-red-500');

            const fileInput = document.querySelector('input[name="photo"]');
            if (!fileInput.files[0]) {
                document.getElementById('photo_error').textContent = 'File foto wajib diisi.';
                document.getElementById('photo_error').classList.remove('hidden');
                fileInput.classList.add('border-red-500');
                Swal.fire('Perhatian!', 'Harap pilih file foto.', 'warning');
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.success ? 'Berhasil!' : 'Gagal!',
                    text: data.message || (data.success ? 'Foto profil berhasil diunggah.' : 'Gagal mengunggah foto.'),
                    icon: data.success ? 'success' : 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (data.success) {
                        document.getElementById('change-photo-modal').classList.add('hidden');
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat mengunggah foto.', 'error');
            });
        });

        // Menangani upload tanda tangan dengan AJAX
        document.getElementById('signature-form').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('signature_error').classList.add('hidden');
            document.getElementById('signature-input').classList.remove('border-red-500');

            const fileInput = document.getElementById('signature-input');
            if (!fileInput.files[0]) {
                document.getElementById('signature_error').textContent = 'File tanda tangan wajib diisi.';
                document.getElementById('signature_error').classList.remove('hidden');
                fileInput.classList.add('border-red-500');
                Swal.fire('Perhatian!', 'Harap pilih file tanda tangan.', 'warning');
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.success ? 'Berhasil!' : 'Gagal!',
                    text: data.message || (data.success ? 'Tanda tangan berhasil diunggah.' : 'Gagal mengunggah tanda tangan.'),
                    icon: data.success ? 'success' : 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (data.success) {
                        document.getElementById('upload-signature-modal').classList.add('hidden');
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat mengunggah tanda tangan.', 'error');
            });
        });

        // Menangani penghapusan foto dengan AJAX
        document.getElementById('delete-photo-form').addEventListener('submit', function(event) {
            event.preventDefault();
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
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: data.success ? 'Berhasil!' : 'Gagal!',
                            text: data.message || (data.success ? 'Foto profil berhasil dihapus.' : 'Gagal menghapus foto.'),
                            icon: data.success ? 'success' : 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus foto.', 'error');
                    });
                }
            });
        });

        // Menangani penghapusan tanda tangan dengan AJAX
        @if($user->signature)
        document.getElementById('delete-signature-form').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus tanda tangan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: data.success ? 'Berhasil!' : 'Gagal!',
                            text: data.message || (data.success ? 'Tanda tangan berhasil dihapus.' : 'Gagal menghapus tanda tangan.'),
                            icon: data.success ? 'success' : 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus tanda tangan.', 'error');
                    });
                }
            });
        });
        @endif
    </script>
@endsection
