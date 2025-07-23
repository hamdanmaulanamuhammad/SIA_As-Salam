@extends('layouts.pengajar')

@section('title', 'Dashboard')

@section('content')
    <div class="w-full px-4 sm:px-6 max-w-7xl mx-auto">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mt-8">Statistik {{ $monthName }}</h2>
        <!-- Cards -->
        <div class="grid gap-4 sm:gap-6 mb-12 sm:mb-16 grid-cols-1 sm:grid-cols-2 mt-4 sm:mt-6">
            <!-- Card 1: Total Kehadiran Bulan ini -->
            <div class="flex items-center p-3 sm:p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-green-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/total-kehadiran.svg') }}" alt="Total Kehadiran" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-600">Total Kehadiran Bulan ini</p>
                    <p class="text-base sm:text-lg font-semibold text-gray-700">{{ $totalAttendance }}</p>
                </div>
            </div>

            <!-- Card 2: Masa Kontrak -->
            <div class="flex items-center p-3 sm:p-4 bg-white rounded-lg shadow-xs border border-gray-200">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-blue-100 rounded-full">
                    <img src="{{ asset('assets/images/icons/contract-icon.svg') }}" alt="Masa Kontrak" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-600">Masa Kontrak</p>
                    <p class="text-base sm:text-lg font-semibold text-gray-700">
                        {{ $contractDuration ? $contractDuration . ' hari' : 'Belum ada kontrak aktif' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Presensi -->
        <div id="event-info" class="mb-6">
            <div class="bg-white rounded-lg shadow py-6 px-4 sm:py-8 sm:px-6 flex justify-between border border-gray-200">
                <div class="mb-2">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Presensi</h3>
                </div>

                <button id="attendance-button" class="my-auto w-7 h-7 sm:w-8 sm:h-8 text-xs sm:text-sm text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Tabel Riwayat Presensi -->
        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Riwayat Presensi</h3>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] bg-white rounded-lg shadow mb-10">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-3 sm:px-4 py-2 sm:py-3">No</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Timestamp</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Hari Mengajar</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Waktu Kedatangan</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Waktu Selesai</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($recentPresences as $index => $presence)
                        <tr class="text-gray-700 text-xs sm:text-sm">
                            <td class="px-3 sm:px-4 py-2 sm:py-3">{{ $index + 1 }}</td>
                            <td class="px-3 sm:px-4 py-2 sm:py-3">{{ $presence->created_at->format('H:i, d/m/Y') }}</td>
                            <td class="px-3 sm:px-4 py-2 sm:py-3">{{ $presence->date }}</td>
                            <td class="px-3 sm:px-4 py-2 sm:py-3">{{ $presence->arrival_time }}</td>
                            <td class="px-3 sm:px-4 py-2 sm:py-3">{{ $presence->end_time }}</td>
                            <td class="px-3 sm:px-4 py-2 sm:py-3 min-w-24">
                                <button class="edit-button text-blue-500 hover:text-blue-700 mr-2 sm:mr-3" data-id="{{ $presence->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('pengajar.presence.destroy', $presence->id) }}" method="POST" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 sm:px-4 py-2 sm:py-3 text-center text-gray-500 text-xs sm:text-sm">Belum ada data presensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal untuk Form Presensi -->
        <div id="presence-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full sm:w-11/12 max-w-md max-h-[90vh] overflow-y-auto">
                <span id="close-presence-form-modal" class="float-right cursor-pointer text-gray-500 text-2xl">×</span>
                <h2 class="text-base sm:text-lg font-semibold">Form Presensi</h2>
                <form id="presence-form" action="{{ route('pengajar.presence.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="presence-id" name="id">
                    <input type="hidden" name="_method" id="presence-method" value="POST">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengajar</label>
                        <input type="text" id="user_name" value="{{ auth()->user()->full_name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" readonly />
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}" />
                    </div>
                    <input type="hidden" name="type" value="presence">
                    <div class="mb-4">
                        <label for="teaching-day" class="block text-sm font-medium text-gray-700">Hari Mengajar</label>
                        <input type="date" id="teaching-day" name="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" required value="{{ now()->format('Y-m-d') }}" />
                    </div>
                    <div class="mb-4">
                        <label for="arrival-time" class="block text-sm font-medium text-gray-700">Waktu Kedatangan</label>
                        <input type="time" id="arrival-time" name="arrival_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" required />
                    </div>
                    <div class="mb-4">
                        <label for="end-time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" id="end-time" name="end_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" required />
                    </div>
                    <div class="mb-4">
                        <label for="class" class="block text-sm font-medium text-gray-700">Kelas yang Diajar</label>
                        <select id="class" name="class" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" required>
                            <option value="Mustawa 1">Mustawa 1</option>
                            <option value="Mustawa 2">Mustawa 2</option>
                            <option value="Mustawa 3">Mustawa 3</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="material" class="block text-sm font-medium text-gray-700">Materi yang Diajarkan</label>
                        <textarea id="material" name="material" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="presence-proof" class="block text-sm font-medium text-gray-700">Bukti Mengajar</label>
                        <input type="file" id="presence-proof" name="proof" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm" accept="image/*,.heic,.heif" required />
                        <span id="presence-proof-error" class="text-red-500 text-sm hidden"></span>
                        <input type="hidden" id="presence-proof-existing" name="proof_existing">
                        <img id="presence-proof-preview" class="mt-2 w-[150px] h-[150px] mx-auto object-cover rounded-md shadow-md hidden" alt="Pratinjau Bukti Mengajar">
                    </div>
                    <div class="mb-4">
                        <label for="issues" class="block text-sm font-medium text-gray-700">Kendala (Opsional)</label>
                        <textarea id="issues" name="issues" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 text-sm"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-presence-form-button" class="px-4 py-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const maxSizeMB = 2; // Batas ukuran file dalam MB
    const proofMaxWidth = 300; // Lebar maksimum untuk bukti mengajar
    const proofMaxHeight = 300; // Tinggi maksimum untuk bukti mengajar
    const quality = 0.5; // Kualitas gambar tetap 50%

    // Fungsi untuk memeriksa apakah file adalah HEIC/HEIF
    function isHEIC(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        return ext === 'heic' || ext === 'heif' || file.type === 'image/heic' || file.type === 'image/heif';
    }

    // Fungsi untuk mengompresi dan mengubah ukuran gambar
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

    // Fungsi untuk membuka form presensi
    function openPresenceForm() {
        document.getElementById('presence-form').reset();
        document.getElementById('presence-id').value = '';
        document.getElementById('presence-method').value = 'POST';
        document.getElementById('presence-form').action = '{{ route('pengajar.presence.store') }}';
        document.getElementById('user_name').value = '{{ auth()->user()->full_name }}';
        document.getElementById('user_id').value = '{{ auth()->user()->id }}';
        document.getElementById('teaching-day').value = '{{ now()->format('Y-m-d') }}';
        document.getElementById('arrival-time').value = '';
        document.getElementById('end-time').value = '';
        document.getElementById('class').value = 'Mustawa 1';
        document.getElementById('material').value = '';
        document.getElementById('issues').value = '';
        document.getElementById('presence-proof').setAttribute('required', '');
        document.getElementById('presence-proof-existing').value = '';
        document.getElementById('presence-proof-preview').classList.add('hidden');
        document.getElementById('presence-proof-error').classList.add('hidden');
        document.getElementById('presence-proof').classList.remove('border-red-500');
        document.getElementById('presence-form-modal').classList.remove('hidden');
    }

    // Attach event listener to attendance button
    document.getElementById('attendance-button').addEventListener('click', openPresenceForm);

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('presence-form-modal').classList.add('hidden');
    }

    // Menambahkan event listener untuk tombol penutup modal
    document.getElementById('close-presence-form-modal').addEventListener('click', closeModal);

    // Menambahkan event listener untuk tombol batal pada form
    document.getElementById('cancel-presence-form-button').addEventListener('click', closeModal);

    // Menangani pratinjau dan kompresi bukti mengajar
    document.getElementById('presence-proof').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) {
            document.getElementById('presence-proof-preview').classList.add('hidden');
            return;
        }

        const preview = document.getElementById('presence-proof-preview');
        const error = document.getElementById('presence-proof-error');

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
            const compressedBlob = await compressImage(blob, proofMaxWidth, proofMaxHeight, quality);
            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                error.classList.add('hidden');
            };
            reader.readAsDataURL(compressedBlob);

            // Ganti file input dengan file yang sudah dikompresi
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([compressedBlob], file.name.replace(/\.heic$/i, '.jpg').replace(/\.heif$/i, '.jpg'), { type: 'image/jpeg' }));
            event.target.files = dataTransfer.files;
        } catch (error) {
            console.error('Error:', error);
            error.textContent = 'Gagal memproses gambar. Pastikan file valid dan coba lagi.';
            error.classList.remove('hidden');
            event.target.classList.add('border-red-500');
            Swal.fire('Error!', 'Gagal memproses bukti mengajar. Pastikan file valid dan coba lagi.', 'error');
        }
    });

    // Menangani tombol edit
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            fetch(`/presence-pengajar/edit/${button.getAttribute('data-id')}`)
                .then(response => response.json())
                .then(data => {
                    // Isi form presensi
                    document.getElementById('presence-id').value = data.id;
                    document.getElementById('user_id').value = data.user_id;
                    document.getElementById('user_name').value = data.user_name || '{{ auth()->user()->full_name }}';
                    document.getElementById('teaching-day').value = data.date;
                    document.getElementById('arrival-time').value = data.arrival_time.split(':').slice(0, 2).join(':');
                    document.getElementById('end-time').value = data.end_time.split(':').slice(0, 2).join(':');
                    document.getElementById('class').value = data.class;
                    document.getElementById('material').value = data.material;
                    document.getElementById('issues').value = data.issues || '';
                    document.getElementById('presence-form').action = `/presence/${data.id}`;
                    document.getElementById('presence-method').value = 'PUT';

                    // Handle existing proof
                    if (data.proof) {
                        document.getElementById('presence-proof-existing').value = data.proof;
                        document.getElementById('presence-proof-preview').src = '{{ asset('storage') }}/' + data.proof;
                        document.getElementById('presence-proof-preview').classList.remove('hidden');
                        document.getElementById('presence-proof').removeAttribute('required');
                    } else {
                        document.getElementById('presence-proof-existing').value = '';
                        document.getElementById('presence-proof-preview').classList.add('hidden');
                        document.getElementById('presence-proof').setAttribute('required', '');
                    }

                    document.getElementById('presence-proof-error').classList.add('hidden');
                    document.getElementById('presence-proof').classList.remove('border-red-500');
                    document.getElementById('presence-form-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Gagal mengambil data presensi. Pastikan data tersedia atau coba lagi.', 'error');
                });
        });
    });

    // Menangani tombol delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Debug log
                    console.log('Attempting to delete via:', form.action);

                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Pastikan request dikenali sebagai AJAX
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        // Cek apakah response adalah JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // Jika bukan JSON, kemungkinan ada redirect atau error HTML
                            console.error('Expected JSON response but got:', contentType);
                            return response.text().then(text => {
                                console.error('Response text:', text);
                                throw new Error('Server returned non-JSON response');
                            });
                        }
                    })
                    .then(data => {
                        console.log('Response data:', data);

                        Swal.fire({
                            title: data.success ? 'Berhasil!' : 'Gagal!',
                            text: data.message || (data.success ? 'Data presensi berhasil dihapus.' : 'Gagal menghapus data presensi.'),
                            icon: data.success ? 'success' : 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);

                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus data. Error: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });
    // Menangani form presensi
    document.getElementById('presence-form').addEventListener('submit', function(event) {
        event.preventDefault();
        document.getElementById('presence-proof-error').classList.add('hidden');
        document.getElementById('presence-proof').classList.remove('border-red-500');

        const fileInput = document.getElementById('presence-proof');
        const isEdit = document.getElementById('presence-id').value;

        // Validasi file hanya jika bukan mode edit atau tidak ada bukti existing
        if (!isEdit && !fileInput.files[0] && !document.getElementById('presence-proof-existing').value) {
            document.getElementById('presence-proof-error').textContent = 'Bukti mengajar wajib diisi.';
            document.getElementById('presence-proof-error').classList.remove('hidden');
            fileInput.classList.add('border-red-500');
            Swal.fire('Perhatian!', 'Harap unggah bukti mengajar.', 'warning');
            return;
        }

        const formData = new FormData(this);
        const id = document.getElementById('presence-id').value;
        const url = id ? `/presence/${id}` : this.action;
        formData.append('_method', id ? 'PUT' : 'POST');

        fetch(url, {
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
                text: data.message || (data.success ? 'Data presensi berhasil disimpan.' : 'Gagal menyimpan data presensi.'),
                icon: data.success ? 'success' : 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                if (data.success) {
                    closeModal();
                    location.reload();
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
        });
    });
});
</script>
@endsection
