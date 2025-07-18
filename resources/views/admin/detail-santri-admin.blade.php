@extends('layouts.admin')

@section('title', 'Detail Santri')

@section('content')
    <div class="container mx-auto p-4">
        <!-- Navigation Path -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="{{ route('santri-admin') }}" class="text-blue-600 hover:underline">Santri Admin</a> > <span class="text-gray-800">Detail Data Santri</span>
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
                                <span class="font-medium">{{ $santri->akta_path ? basename($santri->akta_path) : '-' }}</span>
                                @if($santri->akta_path)
                                <a href="{{ Storage::url($santri->akta_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Preview Akta">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('download.akta', $santri->id) }}" class="text-green-500 hover:text-green-700" title="Download Akta">
                                    <i class="fas fa-download"></i>
                                </a>
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
                <h2 class="text-lg font-semibold mb-4">Edit Data Santri</h2>

                <form id="santri-form" action="{{ route('santri.update', $santri->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="santri-id" name="id" value="{{ $santri->id }}">

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
                                <input type="text" name="nis" id="nis" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nis }}" required>
                            </div>
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nama_lengkap }}" required>
                            </div>
                            <div>
                                <label for="nama_panggilan" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
                                <input type="text" name="nama_panggilan" id="nama_panggilan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nama_panggilan ?? '' }}">
                            </div>
                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-600">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->tempat_lahir }}" required>
                            </div>
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-600">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->tanggal_lahir }}" required>
                            </div>
                            <div>
                                <label for="umur" class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                                <input type="number" name="umur" id="umur" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->umur }}" readonly>
                            </div>
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" {{ $santri->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ $santri->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="hobi" class="block text-sm font-medium text-gray-700 mb-1">Hobi</label>
                                <input type="text" name="hobi" id="hobi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->hobi ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label for="riwayat_penyakit" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit</label>
                                <textarea name="riwayat_penyakit" id="riwayat_penyakit" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ $santri->riwayat_penyakit ?? '' }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-600">*</span></label>
                                <textarea name="alamat" id="alamat" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>{{ $santri->alamat }}</textarea>
                            </div>
                            <div>
                                <label for="sekolah" class="block text-sm font-medium text-gray-700 mb-1">Sekolah</label>
                                <input type="text" name="sekolah" id="sekolah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->sekolah ?? '' }}">
                            </div>
                            <div>
                                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-600">*</span></label>
                                <select name="kelas_id" id="kelas_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach(\App\Models\Kelas::all() as $kelas)
                                        <option value="{{ $kelas->id }}" {{ $santri->kelas_id == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jilid_juz" class="block text-sm font-medium text-gray-700 mb-1">Jilid/Juz</label>
                                <input type="text" name="jilid_juz" id="jilid_juz" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->jilid_juz ?? '' }}">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="Aktif" {{ $santri->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ $santri->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="step-2" class="step-content p-5 hidden">
                        <!-- Step 2: Data Orang Tua/Wali -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah <span class="text-red-600">*</span></label>
                                <input type="text" name="nama_ayah" id="nama_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nama_ayah }}" required>
                            </div>
                            <div>
                                <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu <span class="text-red-600">*</span></label>
                                <input type="text" name="nama_ibu" id="nama_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nama_ibu }}" required>
                            </div>
                            <div>
                                <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->pekerjaan_ayah ?? '' }}">
                            </div>
                            <div>
                                <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->pekerjaan_ibu ?? '' }}">
                            </div>
                            <div>
                                <label for="no_hp_ayah" class="block text-sm font-medium text-gray-700 mb-1">No HP Ayah</label>
                                <input type="text" name="no_hp_ayah" id="no_hp_ayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->no_hp_ayah ?? '' }}">
                            </div>
                            <div>
                                <label for="no_hp_ibu" class="block text-sm font-medium text-gray-700 mb-1">No HP Ibu</label>
                                <input type="text" name="no_hp_ibu" id="no_hp_ibu" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->no_hp_ibu ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nama_wali" class="block text-sm font-medium text-gray-700 mb-1">Nama Wali (Jika Ada)</label>
                                <input type="text" name="nama_wali" id="nama_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->nama_wali ?? '' }}">
                            </div>
                            <div>
                                <label for="pekerjaan_wali" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Wali</label>
                                <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->pekerjaan_wali ?? '' }}">
                            </div>
                            <div>
                                <label for="no_hp_wali" class="block text-sm font-medium text-gray-700 mb-1">No HP Wali</label>
                                <input type="text" name="no_hp_wali" id="no_hp_wali" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ $santri->no_hp_wali ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div id="step-3" class="step-content p-5 hidden">
                        <!-- Step 3: Dokumen -->
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="pas_foto" class="block text-sm font-medium text-gray-700 mb-1">Pas Foto</label>
                                <div class="flex items-center">
                                    <input type="file" name="pas_foto" id="pas_foto" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*">
                                    <input type="hidden" id="pas_foto_existing" name="pas_foto_existing" value="{{ $santri->pas_foto_path ?? '' }}">
                                    <div class="ml-2 w-24">
                                        <img id="pasFotoPreview" class="w-full h-24 object-cover border rounded {{ $santri->pas_foto_path ? '' : 'hidden' }}" src="{{ $santri->pas_foto_path ? Storage::url($santri->pas_foto_path) : '' }}" alt="Pas Foto Preview">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG. Max: 2MB</p>
                            </div>
                            <div>
                                <label for="akta" class="block text-sm font-medium text-gray-700 mb-1">Akta Kelahiran</label>
                                <input type="file" name="akta" id="akta" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*,application/pdf">
                                <input type="hidden" id="akta_existing" name="akta_existing" value="{{ $santri->akta_path ?? '' }}">
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

    // Fungsi untuk membuka modal santri
    document.querySelector('.edit-santri-button')?.addEventListener('click', () => {
        document.getElementById('santri-form-modal').classList.remove('hidden');
        // Show first step
        showStep(1);
        // Remove required attribute for file inputs in edit mode
        document.getElementById('pas_foto').removeAttribute('required');
        document.getElementById('akta').removeAttribute('required');
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
    document.getElementById('santri-form')?.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const url = this.action;

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
                                window.location.href = '{{ route("santri-admin") }}';
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
