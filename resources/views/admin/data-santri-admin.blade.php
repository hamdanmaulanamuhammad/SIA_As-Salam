@extends('layouts.admin')

@section('title', 'Santri')
@section('content')
<div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center mb-6 mt-6">
            <h1 class="text-2xl font-bold">Data Santri</h1>
            <div class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <button id="tambahSantriButton">
                    <i class="fa fa-plus mr-2"></i>Data Santri
                </button>
            </div>
        </div>

        <!-- Search dan Filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Kiri: Search dan Filter -->
            <div class="flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Bar -->
                    <input
                        type="text"
                        id="searchInput"
                        name="search"
                        placeholder="Cari nama atau NIS..."
                        class="w-full md:w-64 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    />

                    <!-- Filter Kelas -->
                    <select
                        id="kelasFilter"
                        name="kelas"
                        class="w-full md:w-48 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Semua Kelas</option>
                        <option value="">Mustawa 1</option>
                        <option value="">Mustawa 2</option>
                        <option value="">Mustawa 3</option>
                    </select>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Filter Status -->
                    <select
                        id="statusFilter"
                        name="status"
                        class="w-full md:w-48 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>

                    <!-- Entries Display -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Tampilkan</span>
                        <select
                            id="entriesSelect"
                            class="px-2 py-1 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-600">entries</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Santri -->
        <div class="overflow-x-auto">
            <table class="w-full mt-4 bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3 min-w-52">Nama Lengkap Santri</th>
                        <th class="px-4 py-3">Nama Panggilan</th>
                        <th class="px-4 py-3 min-w-32">Jenis Kelamin</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 min-w-28">Detail</th>
                    </tr>
                </thead>
                <tbody id="santriTableBody" class="bg-white divide-y">
                    @foreach($santri as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($santri->currentPage() - 1) * $santri->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nis }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nama_lengkap }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->nama_panggilan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->jenis_kelamin }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->kelas }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('santri.show', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center">
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $santri->links() }}
        </div>
    </div>
    <!-- Modal untuk Form Santri -->
    <div id="santri-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-4xl max-h-screen overflow-y-auto">
            <span id="close-santri-form-modal" class="float-right cursor-pointer text-gray-500">&times;</span>
            <h2 class="text-lg font-semibold mb-4">Form Data Santri</h2>

            <form id="santri-form" action="{{ route('santri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="santri-id" name="id">
                <input type="hidden" name="_method" id="santri-method" value="POST">

                <!-- Stepper Indicator -->
                <div class="flex justify-center space-x-4 py-4">
                    <button type="button" id="btnStep1" class="px-4 py-2 rounded-md step-btn active bg-blue-600 text-white font-medium">Identitas</button>
                    <button type="button" id="btnStep2" class="px-4 py-2 rounded-md step-btn bg-gray-200 text-gray-700 font-medium">Orang Tua/Wali</button>
                    <button type="button" id="btnStep3" class="px-4 py-2 rounded-md step-btn bg-gray-200 text-gray-700 font-medium">Dokumen</button>
                </div>

                <!-- Step Content -->
                <div id="step-1" class="step-content p-5">
                    <!-- Step 1: Identitas Santri -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">NIS <span class="text-red-600">*</span></label>
                            <input type="text" name="nis" id="nis" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="nama_panggilan" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" id="nama_panggilan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-600">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-600">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="umur" class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                            <input type="number" name="umur" id="umur" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" readonly>
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label for="hobi" class="block text-sm font-medium text-gray-700 mb-1">Hobi</label>
                            <input type="text" name="hobi" id="hobi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div class="md:col-span-2">
                            <label for="riwayat_penyakit" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit</label>
                            <textarea name="riwayat_penyakit" id="riwayat_penyakit" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-600">*</span></label>
                            <textarea name="alamat" id="alamat" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required></textarea>
                        </div>

                        <div>
                            <label for="sekolah" class="block text-sm font-medium text-gray-700 mb-1">Sekolah</label>
                            <input type="text" name="sekolah" id="sekolah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-600">*</span></label>
                            <select name="kelas" id="kelas" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="">Pilih Kelas</option>
                                <option value="Mustawa 1">Mustawa 1</option>
                                <option value="Mustawa 2">Mustawa 2</option>
                                <option value="Mustawa 3">Mustawa 3</option>
                            </select>
                        </div>

                        <div>
                            <label for="jilid_juz" class="block text-sm font-medium text-gray-700 mb-1">Jilid/Juz</label>
                            <input type="text" name="jilid_juz" id="jilid_juz" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="step-2" class="step-content p-5 hidden">
                    <!-- Step 2: Data Orang Tua/Wali -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah <span class="text-red-600">*</span></label>
                            <input type="text" name="nama_ayah" id="nama_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu <span class="text-red-600">*</span></label>
                            <input type="text" name="nama_ibu" id="nama_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        </div>

                        <div>
                            <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="no_hp_ayah" class="block text-sm font-medium text-gray-700 mb-1">No HP Ayah</label>
                            <input type="text" name="no_hp_ayah" id="no_hp_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="no_hp_ibu" class="block text-sm font-medium text-gray-700 mb-1">No HP Ibu</label>
                            <input type="text" name="no_hp_ibu" id="no_hp_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div class="md:col-span-2">
                            <label for="nama_wali" class="block text-sm font-medium text-gray-700 mb-1">Nama Wali (Jika Ada)</label>
                            <input type="text" name="nama_wali" id="nama_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="pekerjaan_wali" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Wali</label>
                            <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="no_hp_wali" class="block text-sm font-medium text-gray-700 mb-1">No HP Wali</label>
                            <input type="text" name="no_hp_wali" id="no_hp_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <div id="step-3" class="step-content p-5 hidden">
                    <!-- Step 3: Dokumen -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="pas_foto" class="block text-sm font-medium text-gray-700 mb-1">Pas Foto <span class="text-red-600">*</span></label>
                            <div class="flex items-center">
                                <input type="file" name="pas_foto" id="pas_foto" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*" required>
                                <input type="hidden" id="pas_foto_existing" name="pas_foto_existing">
                                <div class="ml-2 w-24">
                                    <img id="pasFotoPreview" class="hidden w-full h-24 object-cover border rounded" alt="Pas Foto Preview">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG. Max: 2MB</p>
                        </div>

                        <div>
                            <label for="akta" class="block text-sm font-medium text-gray-700 mb-1">Akta Kelahiran <span class="text-red-600">*</span></label>
                            <input type="file" name="akta" id="akta" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*,application/pdf" required>
                            <input type="hidden" id="akta_existing" name="akta_existing">
                            <p class="text-xs text-gray-500 mt-1">Format: PDF, JPEG, PNG, JPG. Max: 5MB</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Navigasi -->
                <div class="flex justify-between p-5 border-t">
                    <button type="button" id="prevBtn" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 hidden">Sebelumnya</button>
                    <button type="button" id="nextBtn" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Selanjutnya</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 rounded bg-green-500 text-white hover:bg-green-600 hidden">Simpan</button>
                    <button type="button" id="cancel-santri-form-button" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500 hidden">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;

    // Fungsi untuk membuka modal santri
    document.getElementById('tambahSantriButton')?.addEventListener('click', () => {
        // Reset form
        document.getElementById('santri-form').reset();
        document.getElementById('santri-id').value = '';
        document.getElementById('santri-method').value = 'POST';
        document.getElementById('pasFotoPreview').classList.add('hidden');
        document.getElementById('pas_foto').setAttribute('required', 'required');
        document.getElementById('akta').setAttribute('required', 'required');
        document.getElementById('santri-form').action = "{{ route('santri.store') }}";
        document.getElementById('santri-form-modal').classList.remove('hidden');

        // Reset ke step 1
        showStep(1);
    });

    // Menangani penutupan modal form santri
    document.getElementById('close-santri-form-modal')?.addEventListener('click', () => {
        document.getElementById('santri-form-modal')?.classList.add('hidden');
    });

    document.getElementById('cancel-santri-form-button')?.addEventListener('click', () => {
        document.getElementById('santri-form-modal')?.classList.add('hidden');
    });

    // Fungsi untuk perhitungan umur otomatis
    document.getElementById('tanggal_lahir')?.addEventListener('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        document.getElementById('umur').value = age;
    });

    // Fungsi preview Pas Foto
    document.getElementById('pas_foto')?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('pasFotoPreview');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Step navigation for multi-step form
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.add('hidden');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.step-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });

        // Show active step
        document.getElementById(`step-${step}`).classList.remove('hidden');

        // Highlight active step button
        document.getElementById(`btnStep${step}`).classList.add('active', 'bg-blue-600', 'text-white');
        document.getElementById(`btnStep${step}`).classList.remove('bg-gray-200', 'text-gray-700');

        // Update current step
        currentStep = step;

        // Update navigation buttons
        if (step > 1) {
            document.getElementById('prevBtn').classList.remove('hidden');
        } else {
            document.getElementById('prevBtn').classList.add('hidden');
        }

        if (step === totalSteps) {
            document.getElementById('nextBtn').classList.add('hidden');
            document.getElementById('submitBtn').classList.remove('hidden');
            document.getElementById('cancel-santri-form-button').classList.remove('hidden');
        } else {
            document.getElementById('nextBtn').classList.remove('hidden');
            document.getElementById('submitBtn').classList.add('hidden');
            document.getElementById('cancel-santri-form-button').classList.add('hidden');
        }
    }

    // Event listeners for step buttons
    document.getElementById('btnStep1')?.addEventListener('click', () => showStep(1));
    document.getElementById('btnStep2')?.addEventListener('click', () => showStep(2));
    document.getElementById('btnStep3')?.addEventListener('click', () => showStep(3));

    // Next button handler
    document.getElementById('nextBtn')?.addEventListener('click', function() {
        // Add validation for current step
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');

        let isValid = true;
        requiredFields.forEach(field => {
            if (!field.value) {
                isValid = false;
                field.classList.add('border-red-500');

                // Add event listener to remove red border on input
                field.addEventListener('input', function() {
                    if (field.value) {
                        field.classList.remove('border-red-500');
                    }
                }, { once: true });
            }
        });

        if (isValid) {
            showStep(currentStep + 1);
        } else {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Harap isi semua field yang wajib diisi.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    });

    // Previous button handler
    document.getElementById('prevBtn')?.addEventListener('click', function() {
        showStep(currentStep - 1);
    });

    // Menangani pengiriman form santri dengan AJAX
    document.getElementById('santri-form')?.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const id = document.getElementById('santri-id')?.value;
        const url = id ? `/santri/${id}` : this.action;
        formData.append('_method', id ? 'PUT' : 'POST');

        fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Berhasil!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    location.reload();
                });
            } else {
                let errorMessage = data.message || 'Terjadi kesalahan.';
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat();
                    errorMessage = errorList.join('<br>');
                }

                Swal.fire({
                    title: "Gagal!",
                    html: errorMessage,
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
        });
    });

    // Menangani tombol edit
    document.querySelectorAll('.edit-santri-button').forEach(button => {
        button.addEventListener('click', () => {
            fetch(`/santri/edit/${button.getAttribute('data-id')}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const santri = data.data;

                        // Isi form dengan data yang ada
                        document.getElementById('santri-id').value = santri.id;
                        document.getElementById('nis').value = santri.nis;
                        document.getElementById('nama_lengkap').value = santri.nama_lengkap;
                        document.getElementById('nama_panggilan').value = santri.nama_panggilan || '';
                        document.getElementById('jenis_kelamin').value = santri.jenis_kelamin;
                        document.getElementById('tempat_lahir').value = santri.tempat_lahir;
                        document.getElementById('tanggal_lahir').value = santri.tanggal_lahir;
                        document.getElementById('umur').value = santri.umur;
                        document.getElementById('hobi').value = santri.hobi || '';
                        document.getElementById('riwayat_penyakit').value = santri.riwayat_penyakit || '';
                        document.getElementById('alamat').value = santri.alamat;
                        document.getElementById('sekolah').value = santri.sekolah || '';
                        document.getElementById('kelas').value = santri.kelas;
                        document.getElementById('jilid_juz').value = santri.jilid_juz || '';
                        document.getElementById('status').value = santri.status;

                        // Data orangtua
                        document.getElementById('nama_ayah').value = santri.nama_ayah;
                        document.getElementById('nama_ibu').value = santri.nama_ibu;
                        document.getElementById('pekerjaan_ayah').value = santri.pekerjaan_ayah || '';
                        document.getElementById('pekerjaan_ibu').value = santri.pekerjaan_ibu || '';
                        document.getElementById('no_hp_ayah').value = santri.no_hp_ayah || '';
                        document.getElementById('no_hp_ibu').value = santri.no_hp_ibu || '';
                        document.getElementById('nama_wali').value = santri.nama_wali || '';
                        document.getElementById('pekerjaan_wali').value = santri.pekerjaan_wali || '';
                        document.getElementById('no_hp_wali').value = santri.no_hp_wali || '';

                        // Handle pas foto and akta
                        if (santri.pas_foto_path) {
                            document.getElementById('pas_foto_existing').value = santri.pas_foto_path;
                            document.getElementById('pasFotoPreview').src = "{{ asset('storage') }}/" + santri.pas_foto_path;
                            document.getElementById('pasFotoPreview').classList.remove('hidden');
                            document.getElementById('pas_foto').removeAttribute('required');
                        }

                        if (santri.akta_path) {
                            document.getElementById('akta_existing').value = santri.akta_path;
                            document.getElementById('akta').removeAttribute('required');
                        }

                        document.getElementById('santri-method').value = 'PUT';
                        document.getElementById('santri-form').action = `/santri/${santri.id}`;
                        document.getElementById('santri-form-modal').classList.remove('hidden');

                        // Show first step
                        showStep(1);
                    } else {
                        Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                });
        });
    });

    // Menangani tombol delete
    document.querySelectorAll('.delete-santri-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus data santri ini?",
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
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    });
                }
            });
        });
    });
});
</script>
@endsection
