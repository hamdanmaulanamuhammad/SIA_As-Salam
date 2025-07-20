@extends('layouts.admin')

@section('title', 'Detail Santri')

@section('content')
    <div class="container mx-auto p-4">
        <!-- Navigation Path -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="{{ route('santri.index') }}" class="text-blue-600 hover:underline">Santri Admin</a> > <span class="text-gray-800">Detail Data Santri</span>
        </nav>

        <!-- Header with NIS and Status -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Data Santri</h1>
            <div class="flex space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">NIS: {{ $santri->nis }}</span>
                <span class="px-3 py-1 {{ $santri->status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-sm rounded-full">{{ $santri->status }}</span>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Photo and Actions -->
            <div class="w-full lg:w-1/4">
                <!-- Photo Section -->
                <div class="mb-4">
                    <div class="w-36 h-44 mb-3 border border-gray-200 rounded-md overflow-hidden mx-auto">
                        <img src="{{ $santri->pas_foto_path ? Storage::url($santri->pas_foto_path) : 'https://placehold.co/50x50' }}" alt="Foto Santri" class="w-full h-full object-cover" id="santri-photo">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col space-y-2 mb-6">
                    <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm flex items-center justify-center edit-santri-button" data-id="{{ $santri->id }}">
                        <i class="fas fa-edit mr-2"></i> Edit Data
                    </button>
                    <form class="delete-santri-form" action="{{ route('santri.destroy', $santri->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm flex items-center justify-center w-full">
                            <i class="fas fa-trash mr-2"></i> Hapus Data
                        </button>
                    </form>
                </div>

                <!-- Quick Info Box -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-medium text-gray-700 mb-2">Informasi Singkat</h3>
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm text-gray-500">Umur</span>
                            <p class="font-medium">{{ $santri->umur }} tahun</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Jenis Kelamin</span>
                            <p class="font-medium">{{ $santri->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Alamat</span>
                            <p class="font-medium">{{ $santri->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Detailed Information -->
            <div class="w-full lg:w-3/4">
                <!-- Personal Information -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 pb-2 border-b border-gray-200">Informasi Pribadi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-500">Nama Lengkap</label>
                            <p class="font-medium">{{ $santri->nama_lengkap }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Nama Panggilan</label>
                            <p class="font-medium">{{ $santri->nama_panggilan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Tempat, Tanggal Lahir</label>
                            <p class="font-medium">{{ $santri->tempat_lahir }}, {{ \Carbon\Carbon::parse($santri->tanggal_lahir)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Hobi</label>
                            <p class="font-medium">{{ $santri->hobi ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-500">Akta Kelahiran</label>
                            <div class="flex items-center space-x-2 mt-1">
                                @if($santri->akta_path)
                                    @php
                                        // Format nama file untuk display
                                        $namaSantri = str_replace(' ', '_', $santri->nama_lengkap);
                                        $namaSantri = preg_replace('/[^A-Za-z0-9_]/', '', $namaSantri);
                                        $extension = pathinfo($santri->akta_path, PATHINFO_EXTENSION);
                                        $displayName = "Akta_{$namaSantri}.{$extension}";
                                    @endphp
                                    <span class="font-medium">{{ $displayName }}</span>
                                    <a href="{{ Storage::url($santri->akta_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Preview Akta">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('download.akta', $santri->id) }}" class="text-green-500 hover:text-green-700" title="Download Akta">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <span class="font-medium">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 pb-2 border-b border-gray-200">Informasi Akademik</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-500">Sekolah</label>
                            <p class="font-medium">{{ $santri->sekolah ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Kelas</label>
                            <p class="font-medium">{{ $santri->kelas }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Jilid/Juz</label>
                            <p class="font-medium">{{ $santri->jilid_juz ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Kelas TPA</label>
                            <p class="font-medium">{{ $santri->kelasRelation->nama_kelas ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Health Information -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 pb-2 border-b border-gray-200">Informasi Kesehatan</h2>
                    <div>
                        <label class="block text-sm text-gray-500">Riwayat Penyakit</label>
                        <p class="font-medium">{{ $santri->riwayat_penyakit ?? '-' }}</p>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="bg-white p-5 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 pb-2 border-b border-gray-200">Informasi Wali</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-500">Nama Wali</label>
                            <p class="font-medium">{{ $santri->nama_wali ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">Pekerjaan</label>
                            <p class="font-medium">{{ $santri->pekerjaan_wali ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">No. HP</label>
                            <p class="font-medium">{{ $santri->no_hp_wali ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk Form Santri -->
        <div id="santri-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-4xl max-h-screen overflow-y-auto">
                <span id="close-santri-form-modal" class="float-right cursor-pointer text-gray-500">×</span>
                <h2 class="text-lg font-semibold mb-4">Form Data Santri</h2>

                <form id="santri-form" action="{{ route('santri.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="santri-id" name="id">
                    <input type="hidden" name="_method" id="santri-method" value="POST">

                    <!-- Stepper Indicator -->
                    <div class="flex justify-center space-x-4 py-4">
                        <button type="button" id="btnStep1" class="px-4 py-2 rounded-md step-btn active bg-blue-600 text-white font-medium">Identitas</button>
                        <button type="button" id="btnStep2" class="px-4 py-2 rounded-md step-btn bg-gray-200 text-gray-700 font-medium">Wali</button>
                        <button type="button" id="btnStep3" class="px-4 py-2 rounded-md step-btn bg-gray-200 text-gray-700 font-medium">Dokumen</button>
                    </div>

                    <!-- Step Content -->
                    <div id="step-1" class="step-content p-5">
                        <!-- Step 1: Identitas Santri -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            </div>

                            <div>
                                <label for="nama_panggilan" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
                                <input type="text" name="nama_panggilan" id="nama_panggilan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-600">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            </div>

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-600">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                            </div>

                            <div>
                                <label for="tahun_bergabung" class="block text-sm font-medium text-gray-700 mb-1">Tahun Bergabung <span class="text-red-600">*</span></label>
                                <input type="number" name="tahun_bergabung" id="tahun_bergabung" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required min="2000" max="{{ date('Y') }}" value="{{ date('Y') }}">
                            </div>

                            <div>
                                <label for="umur" class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                                <input type="number" name="umur" id="umur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" readonly>
                            </div>

                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label for="hobi" class="block text-sm font-medium text-gray-700 mb-1">Hobi</label>
                                <input type="text" name="hobi" id="hobi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div class="md:col-span-2">
                                <label for="riwayat_penyakit" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit</label>
                                <textarea name="riwayat_penyakit" id="riwayat_penyakit" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-600">*</span></label>
                                <textarea name="alamat" id="alamat" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required></textarea>
                            </div>

                            <div>
                                <label for="sekolah" class="block text-sm font-medium text-gray-700 mb-1">Sekolah</label>
                                <input type="text" name="sekolah" id="sekolah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas Sekolah <span class="text-red-600">*</span></label>
                                <select name="kelas" id="kelas" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                                    <option value="">Pilih Kelas</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>

                            <div>
                                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas TPA <span class="text-red-600">*</span></label>
                                <select name="kelas_id" id="kelas_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                                    <option value="">Pilih Kelas TPA</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="jilid_juz" class="block text-sm font-medium text-gray-700 mb-1">Jilid/Juz</label>
                                <input type="text" name="jilid_juz" id="jilid_juz" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                                <select name="status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="step-2" class="step-content p-5 hidden">
                        <!-- Step 2: Data Wali -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_wali" class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
                                <input type="text" name="nama_wali" id="nama_wali" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div>
                                <label for="pekerjaan_wali" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Wali</label>
                                <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>

                            <div>
                                <label for="no_hp_wali" class="block text-sm font-medium text-gray-700 mb-1">No HP Wali</label>
                                <input type="text" name="no_hp_wali" id="no_hp_wali" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                            </div>
                        </div>
                    </div>

                    <div id="step-3" class="step-content p-5 hidden">
                        <!-- Step 3: Dokumen -->
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="pas_foto" class="block text-sm font-medium text-gray-700 mb-1">Pas Foto <span class="text-red-600">*</span></label>
                                <div class="flex items-center">
                                    <input type="file" name="pas_foto" id="pas_foto" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" accept="image/*" required>
                                    <input type="hidden" id="pas_foto_existing" name="pas_foto_existing">
                                    <div class="ml-2 w-24">
                                        <img id="pasFotoPreview" class="hidden w-full h-24 object-cover border rounded" alt="Pas Foto Preview">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG. Max: 2MB</p>
                            </div>

                            <div>
                                <label for="akta" class="block text-sm font-medium text-gray-700 mb-1">Akta Kelahiran <span class="text-red-600">*</span></label>
                                <input type="file" name="akta" id="akta" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" accept="image/*,application/pdf" required>
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
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;
    let flatpickrInstance;

    flatpickrInstance = flatpickr("#tanggal_lahir", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });

    // Fungsi untuk membuka modal santri
    document.querySelector('.edit-santri-button')?.addEventListener('click', function() {
        const santriId = this.getAttribute('data-id');
        document.getElementById('santri-form-modal').classList.remove('hidden');
        showStep(1);

        // Remove required attribute for file inputs in edit mode
        document.getElementById('pas_foto').removeAttribute('required');
        document.getElementById('akta').removeAttribute('required');

        // Load data santri setelah modal terbuka
        setTimeout(() => {
            loadSantriData(santriId);
        }, 200); // Delay untuk memastikan modal sudah terbuka sepenuhnya
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
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value || (field.type === 'date' && !/^\d{4}-\d{2}-\d{2}$/.test(field.value))) {
                isValid = false;
                field.classList.add('border-red-500');

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
                text: 'Harap isi semua field yang wajib diisi dengan format yang benar.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    });

    // Previous button handler
    document.getElementById('prevBtn')?.addEventListener('click', function() {
        showStep(currentStep - 1);
    });

    function loadSantriData(santriId) {
        fetch(`/santri/${santriId}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const santri = data.data;

                    // Format tanggal_lahir ke YYYY-MM-DD dengan penanganan yang lebih robust
                    let tanggalLahir = '';
                    if (santri.tanggal_lahir) {
                        // Buat object Date dari string tanggal
                        const dateObj = new Date(santri.tanggal_lahir);

                        // Pastikan tanggal valid
                        if (!isNaN(dateObj.getTime())) {
                            // Format ke YYYY-MM-DD untuk input date
                            tanggalLahir = dateObj.toISOString().split('T')[0];
                        }
                    }

                    // Set form action dan method untuk update
                    document.getElementById('santri-form').action = `/santri/${santri.id}`;
                    document.getElementById('santri-method').value = 'PUT';
                    document.getElementById('santri-id').value = santri.id;

                    // Tunggu sedikit untuk memastikan DOM sudah ready, lalu set nilai
                    setTimeout(() => {
                        // Load data ke form
                        const setFieldValue = (id, value) => {
                            const element = document.getElementById(id);
                            if (element) {
                                element.value = value || '';
                                // Trigger change event untuk field yang mungkin punya event listener
                                element.dispatchEvent(new Event('change'));
                            }
                        };

                        setFieldValue('nama_lengkap', santri.nama_lengkap);
                        setFieldValue('nama_panggilan', santri.nama_panggilan);
                        setFieldValue('tempat_lahir', santri.tempat_lahir);
                        setFieldValue('tahun_bergabung', santri.tahun_bergabung);
                        setFieldValue('umur', santri.umur);
                        setFieldValue('jenis_kelamin', santri.jenis_kelamin);
                        setFieldValue('hobi', santri.hobi);
                        setFieldValue('riwayat_penyakit', santri.riwayat_penyakit);
                        setFieldValue('alamat', santri.alamat);
                        setFieldValue('sekolah', santri.sekolah);
                        setFieldValue('kelas', santri.kelas);
                        setFieldValue('kelas_id', santri.kelas_id);
                        setFieldValue('jilid_juz', santri.jilid_juz);
                        setFieldValue('status', santri.status);
                        setFieldValue('nama_wali', santri.nama_wali);
                        setFieldValue('pekerjaan_wali', santri.pekerjaan_wali);
                        setFieldValue('no_hp_wali', santri.no_hp_wali);

                        // Set tanggal lahir dengan penanganan khusus
                        const tanggalLahirElement = document.getElementById('tanggal_lahir');
                        if (tanggalLahirElement && tanggalLahir) {
                            tanggalLahirElement.value = tanggalLahir;

                            // Jika menggunakan flatpickr, set nilai melalui flatpickr instance
                            if (tanggalLahirElement._flatpickr) {
                                tanggalLahirElement._flatpickr.setDate(tanggalLahir);
                            }

                            // Trigger change event
                            tanggalLahirElement.dispatchEvent(new Event('change'));

                            console.log('Tanggal lahir element value after set:', tanggalLahirElement.value);
                        }

                        // Set existing files untuk hidden input
                        if (santri.pas_foto_path) {
                            const pasFotoExisting = document.getElementById('pas_foto_existing');
                            if (pasFotoExisting) {
                                pasFotoExisting.value = santri.pas_foto_path;
                            }

                            // Show existing photo preview
                            const preview = document.getElementById('pasFotoPreview');
                            if (preview) {
                                preview.src = `/storage/${santri.pas_foto_path}`;
                                preview.classList.remove('hidden');
                            }
                        }

                        if (santri.akta_path) {
                            const aktaExisting = document.getElementById('akta_existing');
                            if (aktaExisting) {
                                aktaExisting.value = santri.akta_path;
                            }
                        }

                    }, 100); // Delay 100ms untuk memastikan DOM ready

                    console.log('Data santri berhasil dimuat:', santri);
                    console.log('Tanggal lahir yang di-set:', tanggalLahir);
                }
            })
            .catch(error => {
                console.error('Error loading santri data:', error);
                Swal.fire('Error!', 'Gagal memuat data santri.', 'error');
            });
    }

    // Menangani pengiriman form santri dengan AJAX
    document.getElementById('santri-form')?.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validasi semua step
        let isValid = true;
        let invalidStep = null;

        for (let step = 1; step <= totalSteps; step++) {
            const stepElement = document.getElementById(`step-${step}`);
            const requiredFields = stepElement.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value || (field.type === 'date' && !/^\d{4}-\d{2}-\d{2}$/.test(field.value))) {
                    isValid = false;
                    invalidStep = step;
                    field.classList.add('border-red-500');
                }
            });
        }

        if (!isValid) {
            showStep(invalidStep);
            Swal.fire({
                title: 'Perhatian!',
                text: 'Harap isi semua field yang wajib diisi dengan format yang benar.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        const formData = new FormData(this);
        const url = this.action;

        // Jika edit mode dan tidak ada file baru, kirim informasi file existing
        const isEdit = document.getElementById('santri-method').value === 'PUT';

        if (isEdit) {
            // Jika tidak ada file pas_foto baru, kirim existing path
            if (!document.getElementById('pas_foto').files.length) {
                const existingPasFoto = document.getElementById('pas_foto_existing').value;
                if (existingPasFoto) {
                    formData.append('pas_foto_existing', existingPasFoto);
                }
            }

            // Jika tidak ada file akta baru, kirim existing path
            if (!document.getElementById('akta').files.length) {
                const existingAkta = document.getElementById('akta_existing').value;
                if (existingAkta) {
                    formData.append('akta_existing', existingAkta);
                }
            }
        }

        fetch(url, {
            method: 'POST',
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
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
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
                    title: 'Gagal!',
                    html: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
        });
    });

    flatpickr("#tanggal_lahir", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });

    // Menangani tombol delete
    document.querySelectorAll('.delete-santri-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data santri ini?',
                icon: 'warning',
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
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                                window.location.href = '{{ route("santri.index") }}';
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
