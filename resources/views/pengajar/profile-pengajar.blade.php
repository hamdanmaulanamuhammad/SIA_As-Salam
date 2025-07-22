@extends('layouts.pengajar')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-md p-4 sm:p-6 md:mt-10">
            <div class="flex flex-col items-center space-y-5 sm:flex-row sm:space-y-0">
                <img class="object-cover w-32 h-32 sm:w-40 sm:h-40 p-1 rounded-full"
                    src="{{ $user->photo ? Storage::url($user->photo) : 'https://placehold.co/100x100' }}" alt="Profile Picture">

                <div class="flex flex-col space-y-3 sm:ml-8">
                    <button type="button" id="change-photo-button"
                        class="py-2.5 px-5 text-sm font-medium text-white focus:outline-none bg-blue-600 rounded-lg border border-blue-200 hover:bg-blue-700 focus:z-10 focus:ring-4 focus:ring-blue-200">
                        Ganti Foto
                    </button>
                    <form action="{{ route('profile.pengajar.deletePhoto') }}" method="POST" class="inline-block" id="delete-photo-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="delete-photo-button"
                            class="py-2.5 px-5 text-sm font-medium text-red-600 focus:outline-none bg-white rounded-lg border border-red-200 hover:bg-red-100 focus:z-10 focus:ring-4 focus:ring-red-200"
                            @if(auth()->user()->photo == null) disabled @endif>
                            Hapus Foto
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Tanda Tangan</h3>
                <div class="flex flex-col items-center space-y-4">
                    @if($user->signature)
                        <div class="border-2 border-gray-300 rounded-lg p-2 sm:p-4 bg-gray-50 w-full max-w-[300px] sm:max-w-[456px]">
                            <img src="{{ Storage::url($user->signature) }}" alt="Tanda Tangan" class="max-w-full h-auto max-h-[150px] sm:max-h-[231px]">
                        </div>
                    @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-8 text-center bg-gray-50 w-full max-w-[300px] sm:max-w-[456px]">
                            <p class="text-gray-500 text-sm sm:text-base">Belum ada tanda tangan</p>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
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

            <div class="mt-6 border-t pt-6">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->full_name }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" id="nip" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->id }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->username }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" id="phone" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->phone }}" readonly />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->email }}" readonly />
                </div>
                <div class="flex justify-end">
                    <button type="button" id="edit-button" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit Profile -->
    <div id="edit-profile-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full sm:w-11/12 max-w-md max-h-[90vh] overflow-y-auto">
            <span id="close-edit-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold">Edit Profile</h2>
            <form id="edit-profile-form" action="{{ route('profile.pengajar.update') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="edit-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="full_name" id="edit-name-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->full_name }}" required />
                    <span id="edit-name-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-username-input" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit-username-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->username }}" required />
                    <span id="edit-username-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-phone-input" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" name="phone" id="edit-phone-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->phone }}" required />
                    <span id="edit-phone-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="mb-4">
                    <label for="edit-email-input" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit-email-input" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" value="{{ $user->email }}" required />
                    <span id="edit-email-input_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-edit-button" class="px-4 py-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Ganti Foto -->
    <div id="change-photo-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full sm:w-11/12 max-w-md max-h-[90vh] overflow-y-auto">
            <span id="close-change-photo-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold">Ganti Foto Profil</h2>
            <form id="change-photo-form" action="{{ route('profile.pengajar.uploadPhoto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="photo" id="photo-input" accept="image/*,.heic,.heif" required class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 mt-4 text-sm" />
                    <span id="photo_error" class="text-red-500 text-sm hidden"></span>
                    <img id="photo-preview" class="mt-2 w-full rounded-md shadow-md hidden" alt="Pratinjau Foto Profil">
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="cancel-change-photo" class="px-4 py-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Ganti Foto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Upload Tanda Tangan -->
    <div id="upload-signature-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full sm:w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto">
            <span id="close-signature-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
            <h2 class="text-lg font-semibold">Upload Tanda Tangan</h2>
            <p class="text-sm text-gray-600 mb-4">Pilih gambar tanda tangan, kemudian crop sesuai area yang diinginkan (akan diresize ke 912x462px)</p>
            <form id="signature-form" action="{{ route('profile.pengajar.uploadSignature') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="signature" id="signature-input" accept="image/*" required class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                    <span id="signature_error" class="text-red-500 text-sm hidden"></span>
                </div>
                <div id="cropper-container" class="hidden mb-4">
                    <img id="signature-preview" class="max-w-full" alt="Pratinjau Tanda Tangan" />
                </div>
                <input type="hidden" name="x" id="crop-x" />
                <input type="hidden" name="y" id="crop-y" />
                <input type="hidden" name="width" id="crop-width" />
                <input type="hidden" name="height" id="crop-height" />
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-signature-upload" class="px-4 py-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" id="upload-signature-btn" class="px-4 py-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700" disabled>Upload Tanda Tangan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let cropper;
    const maxSizeMB = 2; // Batas ukuran file dalam MB
    const photoMaxWidth = 300; // Lebar maksimum untuk foto profil
    const photoMaxHeight = 300; // Tinggi maksimum untuk foto profil
    const quality = 0.5; // Kualitas gambar tetap 50%

    // Fungsi untuk memeriksa apakah file adalah HEIC/HEIF
    function isHEIC(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        return ext === 'heic' || ext === 'heif' || file.type === 'image/heic' || file.type === 'image/heif';
    }

    // Fungsi untuk mengompresi dan mengubah ukuran gambar (untuk foto profil)
    async function compressImage(file, maxWidth, maxHeight, quality) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);

            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                let width = img.width;
                let height = img.height;

                // Hitung dimensi baru dengan mempertahankan rasio aspek
                if (width > height) {
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        if (blob && blob.size / (1024 * 1024) > maxSizeMB) {
                            Swal.fire('Peringatan!', 'Ukuran gambar masih terlalu besar meskipun sudah dikompresi.', 'warning');
                            reject(new Error('Ukuran gambar melebihi batas'));
                        } else {
                            resolve(blob);
                        }
                    },
                    'image/jpeg',
                    quality
                );
            };
            img.onerror = reject;
        });
    }

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
        document.getElementById('change-photo-form').reset();
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('photo_error').classList.add('hidden');
        document.getElementById('photo-input').classList.remove('border-red-500');
        document.getElementById('change-photo-modal').classList.remove('hidden');
    });

    document.getElementById('close-change-photo-modal').addEventListener('click', () => {
        document.getElementById('change-photo-modal').classList.add('hidden');
    });

    document.getElementById('cancel-change-photo').addEventListener('click', () => {
        document.getElementById('change-photo-modal').classList.add('hidden');
    });

    // Menangani pratinjau dan kompresi foto profil
    document.getElementById('photo-input').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('photo-preview');

        try {
            let blob = file;
            // Cek jika file adalah HEIC
            if (isHEIC(file)) {
                if (typeof window.heic2any === 'undefined') {
                    throw new Error('Pustaka heic2any tidak dimuat.');
                }
                blob = await window.heic2any({
                    blob: file,
                    toType: 'image/jpeg',
                    quality: quality
                });
            }

            // Kompresi dan resize gambar
            const compressedBlob = await compressImage(blob, photoMaxWidth, photoMaxHeight, quality);
            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(compressedBlob);

            // Ganti file input dengan file yang sudah dikompresi
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([compressedBlob], file.name.replace(/\.heic$/i, '.jpg').replace(/\.heif$/i, '.jpg'), { type: 'image/jpeg' }));
            event.target.files = dataTransfer.files;
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal memproses foto profil. Pastikan file valid dan coba lagi.', 'error');
        }
    });

    // Menangani modal upload tanda tangan
    document.getElementById('upload-signature-button').addEventListener('click', () => {
        document.getElementById('signature-form').reset();
        document.getElementById('cropper-container').classList.add('hidden');
        document.getElementById('signature_error').classList.add('hidden');
        document.getElementById('signature-input').classList.remove('border-red-500');
        document.getElementById('upload-signature-btn').disabled = true;
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
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
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('signature-preview');
            preview.src = e.target.result;
            document.getElementById('cropper-container').classList.remove('hidden');

            if (cropper) {
                cropper.destroy();
            }

            cropper = new window.Cropper(preview, {
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
        document.getElementById('photo-input').classList.remove('border-red-500');

        const fileInput = document.getElementById('photo-input');
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

        if (!document.getElementById('crop-x').value) {
            document.getElementById('signature_error').textContent = 'Harap crop gambar terlebih dahulu.';
            document.getElementById('signature_error').classList.remove('hidden');
            fileInput.classList.add('border-red-500');
            Swal.fire('Perhatian!', 'Harap crop gambar terlebih dahulu.', 'warning');
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
});
</script>
@endsection
