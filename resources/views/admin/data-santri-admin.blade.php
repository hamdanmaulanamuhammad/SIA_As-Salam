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
        <div class="flex flex-col gap-4">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search Bar -->
                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    placeholder="Cari nama atau NIS..."
                    class="w-full md:w-64 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    value="{{ request('search') }}"
                />

                <!-- Filter Kelas -->
                <select id="kelasFilter" name="kelas" class="w-full md:w-48 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
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
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <!-- Entries Display -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select
                        id="entriesSelect"
                        name="per_page"
                        class="px-2 py-1 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
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
                    <th class="px-4 py-3">Kelas TPA</th>
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
                    <td class="px-4 py-3 text-sm">{{ $item->kelasRelation->nama_kelas ?? '-' }}</td>
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 3;
        const searchInput = document.getElementById('searchInput');
        const kelasFilter = document.getElementById('kelasFilter');
        const statusFilter = document.getElementById('statusFilter');
        const entriesSelect = document.getElementById('entriesSelect');

        let isSubmitting = false; // Flag untuk mencegah submit berulang

        // Fungsi untuk submit form dengan AJAX
        function submitForm() {
            if (isSubmitting) return; // Prevent multiple submissions

            isSubmitting = true;

            // Simpan nilai input dan posisi kursor sebelum submit
            const searchValue = searchInput.value;
            const cursorPosition = searchInput.selectionStart;

            // Buat URL dengan parameter
            const params = new URLSearchParams();

            if (searchInput.value.trim()) {
                params.append('search', searchInput.value.trim());
            }
            if (kelasFilter.value) {
                params.append('kelas', kelasFilter.value);
            }
            if (statusFilter.value) {
                params.append('status', statusFilter.value);
            }
            params.append('per_page', entriesSelect.value);

            // Gunakan fetch untuk AJAX request
            fetch(`{{ route("santri-admin") }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Parse HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update table body
                const newTableBody = doc.querySelector('#santriTableBody');
                if (newTableBody) {
                    document.querySelector('#santriTableBody').innerHTML = newTableBody.innerHTML;
                }

                // Update pagination
                const newPagination = doc.querySelector('.mt-6');
                if (newPagination) {
                    document.querySelector('.mt-6').innerHTML = newPagination.innerHTML;
                }

                // Kembalikan fokus ke searchInput dengan nilai dan posisi kursor
                setTimeout(() => {
                    searchInput.value = searchValue;
                    searchInput.focus();
                    searchInput.setSelectionRange(cursorPosition, cursorPosition);
                    isSubmitting = false;
                }, 50);
            })
            .catch(error => {
                console.error('Error:', error);
                isSubmitting = false;
                // Fallback ke form submission biasa jika AJAX gagal
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route("santri-admin") }}';

                if (searchInput.value.trim()) {
                    const search = document.createElement('input');
                    search.type = 'hidden';
                    search.name = 'search';
                    search.value = searchInput.value.trim();
                    form.appendChild(search);
                }

                if (kelasFilter.value) {
                    const kelas = document.createElement('input');
                    kelas.type = 'hidden';
                    kelas.name = 'kelas';
                    kelas.value = kelasFilter.value;
                    form.appendChild(kelas);
                }

                if (statusFilter.value) {
                    const status = document.createElement('input');
                    status.type = 'hidden';
                    status.name = 'status';
                    status.value = statusFilter.value;
                    form.appendChild(status);
                }

                const perPage = document.createElement('input');
                perPage.type = 'hidden';
                perPage.name = 'per_page';
                perPage.value = entriesSelect.value;
                form.appendChild(perPage);

                document.body.appendChild(form);
                form.submit();
            });
        }

        // Debounce function dengan delay yang lebih panjang
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Event listeners dengan debounce yang lebih panjang untuk search
        const debouncedSearch = debounce(submitForm, 1000); // 1 detik delay

        searchInput.addEventListener('input', function(e) {
            // Jangan submit jika input kosong atau hanya spasi
            if (e.target.value.trim().length === 0 && e.target.value.length > 0) {
                return;
            }
            debouncedSearch();
        });

        // Event listeners untuk filter lainnya (tanpa debounce)
        kelasFilter.addEventListener('change', submitForm);
        statusFilter.addEventListener('change', submitForm);
        entriesSelect.addEventListener('change', submitForm);

        // Prevent form submission on Enter key dalam search input
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitForm();
            }
        });

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
    });
</script>
@endsection
