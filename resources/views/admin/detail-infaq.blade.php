@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <!-- Header -->
    <div class="flex justify-between items-center mt-6 mb-4">
        <h2 class="text-2xl font-semibold text-gray-700">Detail Infaq Tahunan {{ $infaqTahunan->tahun }}</h2>
        <a href="{{ route('keuangan.index') }}?tab=infaq" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <!-- Total Infaq dan Kekurangan -->
    <div class="mb-6 p-4 bg-white rounded-lg shadow">
        <div class="flex flex-col md:flex-row justify-between gap-4">
            <div>
                <span class="text-sm font-semibold text-gray-600">Total Infaq Wajib:</span>
                <span class="text-sm font-bold text-blue-600">Rp {{ number_format($totalInfaqWajib, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-sm font-semibold text-gray-600">Total Infaq Sukarela:</span>
                <span class="text-sm font-bold text-green-600">Rp {{ number_format($totalInfaqSukarela, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-sm font-semibold text-gray-600">Total Keseluruhan (Wajib + Sukarela):</span>
                <span class="text-sm font-bold text-purple-600">Rp {{ number_format($totalInfaq, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-sm font-semibold text-gray-600">Total Kekurangan Infaq Wajib:</span>
                <span class="text-sm font-bold text-red-600">Rp {{ number_format($totalKekurangan, 0, ',', '.') }}</span>
            </div>
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
        <table class="w-full mt-4 bg-white rounded-lg shadow min-w-max">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 min-w-16">No</th>
                    <th class="px-4 py-3 min-w-20">NIS</th>
                    <th class="px-4 py-3 min-w-52">Nama Lengkap Santri</th>
                    <th class="px-4 py-3 min-w-24">Kelas TPA</th>
                    <th class="px-4 py-3 min-w-80">Bulan Terbayar</th>
                    <th class="px-4 py-3 min-w-32">Total Infaq Wajib</th>
                    <th class="px-4 py-3 min-w-32">Total Infaq Sukarela</th>
                    <th class="px-4 py-3 min-w-28">Total Infaq</th>
                    <th class="px-4 py-3 min-w-32">Kekurangan Infaq</th>
                    <th class="px-4 py-3 min-w-28">Aksi</th>
                </tr>
            </thead>
            <tbody id="santriTableBody" class="bg-white divide-y">
                @foreach($santri as $index => $item)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm">{{ ($santri->currentPage() - 1) * $santri->perPage() + $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->nis }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->nama_lengkap }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->kelasRelation->nama_kelas ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex flex-col gap-1">
                            @php
                                $paidMonths = $item->infaqSantris->pluck('bulan')->toArray();
                                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                $monthsChunks = array_chunk($months, 6);
                            @endphp

                            @foreach($monthsChunks as $chunkIndex => $chunk)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($chunk as $i => $month)
                                        @php
                                            $monthNumber = $chunkIndex * 6 + $i + 1;
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded whitespace-nowrap {{ in_array($monthNumber, $paidMonths) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-800' }}">
                                            {{ $month }}
                                        </span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        {{ 'Rp ' . number_format($item->infaqSantris->sum('infaq_wajib'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        {{ 'Rp ' . number_format($item->infaqSantris->sum('infaq_sukarela'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        {{ 'Rp ' . number_format($item->infaqSantris->sum('infaq_wajib') + $item->infaqSantris->sum('infaq_sukarela'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        {{ 'Rp ' . number_format(12 * 10000 - $item->infaqSantris->sum('infaq_wajib'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2 whitespace-nowrap">
                            <button data-id="{{ $item->id }}" data-paid-months="{{ json_encode($paidMonths) }}" class="add-infaq-button w-8 h-8 text-white bg-blue-600 rounded-md flex items-center justify-center hover:bg-blue-700" title="Tambah Infaq">
                                <i class="fa fa-plus"></i>
                            </button>
                            @if($item->infaqSantris->isNotEmpty())
                                <button data-id="{{ $item->id }}" data-paid-months="{{ json_encode($paidMonths) }}" class="edit-infaq-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" title="Edit Infaq">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button data-id="{{ $item->id }}" data-paid-months="{{ json_encode($paidMonths) }}" class="delete-infaq-button w-8 h-8 text-white bg-red-600 rounded-md flex items-center justify-center hover:bg-red-700" title="Hapus Infaq">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 mb-6">
        {{ $santri->links() }}
    </div>

    <!-- Modal for Infaq Input/Edit -->
    <div id="infaq-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold" id="modal-title">Input Pembayaran Infaq</h3>
                <button id="close-infaq-form-modal" class="text-gray-500 hover:text-gray-700" aria-label="Tutup modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="infaq-form" action="{{ route('keuangan.infaq.santri.store', $infaqTahunan->id) }}" method="POST">
                @csrf
                <input type="hidden" id="infaq_id" name="infaq_id">
                <input type="hidden" id="santri_id" name="santri_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" id="month-label">Pilih Bulan <span class="text-red-600">*</span></label>
                    <div id="month-checkboxes" class="grid grid-cols-3 gap-2">
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                            <label class="flex items-center month-checkbox-label">
                                <input type="checkbox" name="bulan[]" value="{{ $index + 1 }}" class="month-checkbox mr-2">
                                <span>{{ $bulan }}</span>
                            </label>
                        @endforeach
                    </div>
                    <select id="edit-month-select" name="bulan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 hidden">
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                            <option value="{{ $index + 1 }}">{{ $bulan }}</option>
                        @endforeach
                    </select>
                    <p id="month-error" class="text-red-500 text-xs mt-1 hidden">Pilih minimal satu bulan.</p>
                </div>
                <div class="mb-4">
                    <label for="infaq_wajib" class="block text-sm font-medium text-gray-700 mb-1">Infaq Wajib</label>
                    <input type="number" name="infaq_wajib" id="infaq_wajib"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1 bg-gray-100 cursor-not-allowed"
                        min="10000" value="10000" readonly>
                </div>
                <div class="mb-4">
                    <label for="infaq_sukarela" class="block text-sm font-medium text-gray-700 mb-1">Infaq Sukarela <span class="text-gray-500">(opsional)</span></label>
                    <input type="number" name="infaq_sukarela" id="infaq_sukarela" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" min="0" value="0">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-infaq-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" id="submit-infaq-button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div id="delete-infaq-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Konfirmasi Penghapusan</h3>
                <button id="close-delete-infaq-modal" class="text-gray-500 hover:text-gray-700" aria-label="Tutup modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <p class="mb-4">Pilih bulan yang ingin dihapus:</p>
            <form id="delete-infaq-form" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete_santri_id" name="santri_id">
                <div class="mb-4">
                    <select id="delete-month-select" name="bulan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                        <!-- Options akan diisi oleh JavaScript -->
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-delete-infaq-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" id="confirm-delete-infaq-button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const routes = {
        infaqSantriStore: "{{ route('keuangan.infaq.santri.store', $infaqTahunan->id) }}",
        infaqSantriEdit: "{{ route('keuangan.infaq.santri.edit', [$infaqTahunan->id, ':id']) }}",
        infaqSantriUpdate: "{{ route('keuangan.infaq.santri.update', [$infaqTahunan->id, ':id']) }}",
        infaqSantriDestroy: "{{ route('keuangan.infaq.santri.destroy', [$infaqTahunan->id, ':id']) }}",
        keuanganInfaqDetail: "{{ route('keuangan.infaq.santri.index', $infaqTahunan->id) }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk mengupdate tabel berdasarkan filter
        function updateTable() {
            const search = document.getElementById('searchInput').value;
            const kelas = document.getElementById('kelasFilter').value;
            const perPage = document.getElementById('entriesSelect').value;

            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('kelas', kelas);
            url.searchParams.set('per_page', perPage);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                document.getElementById('santriTableBody').innerHTML = doc.getElementById('santriTableBody').innerHTML;
                document.querySelector('.mt-6').innerHTML = doc.querySelector('.mt-6').innerHTML;
                // Perbarui total infaq dan kekurangan
                const totalInfaqWajib = doc.querySelector('.text-blue-600').textContent;
                const totalInfaqSukarela = doc.querySelector('.text-green-600').textContent;
                const totalInfaq = doc.querySelector('.text-purple-600').textContent;
                const totalKekurangan = doc.querySelector('.text-red-600').textContent;
                document.querySelector('.text-blue-600').textContent = totalInfaqWajib;
                document.querySelector('.text-green-600').textContent = totalInfaqSukarela;
                document.querySelector('.text-purple-600').textContent = totalInfaq;
                document.querySelector('.text-red-600').textContent = totalKekurangan;
                // Re-attach event listeners untuk tombol baru
                attachButtonListeners();
            })
            .catch(error => {
                console.error('Error fetching table:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat memuat data.', 'error');
            });
        }

        // Fungsi untuk mengattach event listener ke tombol
        function attachButtonListeners() {
            // Tombol Tambah Infaq
            document.querySelectorAll('.add-infaq-button').forEach(button => {
                button.addEventListener('click', () => {
                    const santriId = button.getAttribute('data-id');
                    const paidMonths = JSON.parse(button.getAttribute('data-paid-months') || '[]');

                    // Convert semua ke number untuk memastikan perbandingan yang tepat
                    const paidMonthsNumbers = paidMonths.map(month => parseInt(month));

                    console.log('Paid months (original):', paidMonths);
                    console.log('Paid months (converted to numbers):', paidMonthsNumbers);

                    document.getElementById('santri_id').value = santriId;
                    document.getElementById('infaq_id').value = '';

                    // Reset form terlebih dahulu
                    const form = document.getElementById('infaq-form');
                    form.reset();

                    // Setup form untuk mode tambah
                    form.action = routes.infaqSantriStore;
                    document.getElementById('infaq_wajib').value = '10000';
                    document.getElementById('infaq_sukarela').value = '0';
                    document.getElementById('modal-title').textContent = 'Input Pembayaran Infaq';
                    document.getElementById('month-label').innerHTML = 'Pilih Bulan <span class="text-red-600">*</span>';
                    document.getElementById('month-checkboxes').classList.remove('hidden');
                    document.getElementById('edit-month-select').classList.add('hidden');
                    document.getElementById('submit-infaq-button').textContent = 'Simpan';
                    document.getElementById('month-error').classList.add('hidden');

                    // Reset dan handle semua checkbox bulan
                    let availableMonthsCount = 0;
                    document.querySelectorAll('.month-checkbox-label').forEach((label, index) => {
                        const checkbox = label.querySelector('.month-checkbox');
                        const span = label.querySelector('span');
                        const monthValue = index + 1; // Bulan 1-12

                        // Reset semua style terlebih dahulu
                        label.style.display = 'flex';
                        label.style.opacity = '1';
                        checkbox.disabled = false;
                        checkbox.checked = false;
                        span.style.textDecoration = 'none';
                        span.style.color = '';

                        // Bandingkan dengan array number
                        const isPaid = paidMonthsNumbers.includes(monthValue);

                        console.log(`Processing month ${monthValue}, is paid:`, isPaid);

                        if (isPaid) {
                            // Sembunyikan bulan yang sudah terbayar
                            label.style.display = 'none';
                            checkbox.disabled = true;
                            checkbox.checked = false;
                        } else {
                            // Bulan yang belum terbayar, tampilkan dan enable
                            label.style.display = 'flex';
                            checkbox.disabled = false;
                            availableMonthsCount++;
                        }
                    });

                    console.log('Available months count:', availableMonthsCount);

                    // Cek apakah masih ada bulan yang bisa dipilih
                    if (availableMonthsCount === 0) {
                        Swal.fire({
                            title: 'Info!',
                            text: 'Semua bulan sudah terbayar untuk santri ini.',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Buka modal
                    document.getElementById('infaq-form-modal').classList.remove('hidden');
                });
            });

            // Tombol Edit Infaq - kode yang sudah ada tidak berubah
            document.querySelectorAll('.edit-infaq-button').forEach(button => {
                button.addEventListener('click', () => {
                    const santriId = button.getAttribute('data-id');
                    const paidMonths = JSON.parse(button.getAttribute('data-paid-months') || '[]');

                    document.getElementById('santri_id').value = santriId;
                    document.getElementById('modal-title').textContent = 'Edit Pembayaran Infaq';
                    document.getElementById('month-label').innerHTML = 'Pilih Bulan <span class="text-red-600">*</span>';
                    document.getElementById('month-checkboxes').classList.add('hidden');
                    document.getElementById('edit-month-select').classList.remove('hidden');
                    document.getElementById('submit-infaq-button').textContent = 'Perbarui';

                    const select = document.getElementById('edit-month-select');
                    select.innerHTML = paidMonths.map(month => {
                        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        return `<option value="${month}">${monthNames[month - 1]}</option>`;
                    }).join('');

                    loadInfaqData(santriId, select.value);
                    select.addEventListener('change', () => loadInfaqData(santriId, select.value));

                    document.getElementById('infaq-form-modal').classList.remove('hidden');
                    document.getElementById('month-error').classList.add('hidden');
                });
            });

            // Tombol Hapus Infaq - kode yang sudah ada tidak berubah
            document.querySelectorAll('.delete-infaq-button').forEach(button => {
                button.addEventListener('click', () => {
                    const santriId = button.getAttribute('data-id');
                    const paidMonths = JSON.parse(button.getAttribute('data-paid-months') || '[]');

                    const deleteSelect = document.getElementById('delete-month-select');
                    deleteSelect.innerHTML = paidMonths.map(month => {
                        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        return `<option value="${month}">${monthNames[month - 1]}</option>`;
                    }).join('');

                    document.getElementById('delete_santri_id').value = santriId;
                    document.getElementById('delete-infaq-modal').classList.remove('hidden');
                });
            });
        }

        // Fungsi untuk load data infaq untuk edit
        function loadInfaqData(santriId, bulan) {
            const url = routes.infaqSantriEdit.replace(':id', `${santriId}-${bulan}`);
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const infaq = data.data;
                    document.getElementById('infaq_id').value = infaq.id;
                    document.getElementById('santri_id').value = infaq.santri_id;
                    document.getElementById('edit-month-select').value = infaq.bulan;
                    document.getElementById('infaq_wajib').value = infaq.infaq_wajib;
                    document.getElementById('infaq_sukarela').value = infaq.infaq_sukarela;
                    document.getElementById('infaq-form').action = routes.infaqSantriUpdate.replace(':id', infaq.id);
                } else {
                    Swal.fire('Error!', data.message || 'Gagal memuat data infaq.', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                Swal.fire('Error!', error.message || 'Terjadi kesalahan saat memuat data.', 'error');
            });
        }

        // Event listener untuk input pencarian dan filter
        document.getElementById('searchInput').addEventListener('input', debounce(updateTable, 300));
        document.getElementById('kelasFilter').addEventListener('change', updateTable);
        document.getElementById('entriesSelect').addEventListener('change', updateTable);

        // Fungsi debounce untuk mencegah request berulang
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

        // Attach listener untuk tombol modal
        attachButtonListeners();

        document.getElementById('close-infaq-form-modal').addEventListener('click', () => {
            document.getElementById('infaq-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-infaq-form-button').addEventListener('click', () => {
            document.getElementById('infaq-form-modal').classList.add('hidden');
        });

        // Submit form infaq
        document.getElementById('infaq-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;
            const submitButton = form.querySelector('#submit-infaq-button');
            const formData = new FormData(form);
            const isEditMode = document.getElementById('infaq_id').value !== '';

            if (!isEditMode) {
                // Hitung checkbox yang checked dan tidak disabled
                const checkedBoxes = form.querySelectorAll('input[name="bulan[]"]:checked');
                const validCheckedBoxes = Array.from(checkedBoxes).filter(cb => !cb.disabled);

                console.log('Checked boxes:', checkedBoxes.length); // Debug log
                console.log('Valid checked boxes:', validCheckedBoxes.length); // Debug log

                if (validCheckedBoxes.length === 0) {
                    document.getElementById('month-error').classList.remove('hidden');
                    document.getElementById('month-error').textContent = 'Pilih minimal satu bulan yang tersedia.';
                    return;
                } else {
                    document.getElementById('month-error').classList.add('hidden');
                }
                formData.delete('bulan');
            } else {
                formData.delete('bulan[]');
                const bulanValue = document.getElementById('edit-month-select').value;
                formData.set('bulan', bulanValue);
                formData.append('_method', 'PUT');
                document.getElementById('month-error').classList.add('hidden');
            }

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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = isEditMode ? 'Perbarui' : 'Simpan';

                if (data.success) {
                    document.getElementById('infaq-form-modal').classList.add('hidden');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        updateTable();
                    });
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
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
                console.error('Fetch error:', error);
                submitButton.disabled = false;
                submitButton.innerHTML = isEditMode ? 'Perbarui' : 'Simpan';
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan pada server.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Submit form delete infaq
        document.getElementById('delete-infaq-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;
            const submitButton = form.querySelector('#confirm-delete-infaq-button');
            const santriId = document.getElementById('delete_santri_id').value;
            const bulan = document.getElementById('delete-month-select').value;

            // Dapatkan infaq_id untuk bulan yang dipilih
            const url = routes.infaqSantriEdit.replace(':id', `${santriId}-${bulan}`);

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Gagal memuat data infaq.');
                }
                const infaqId = data.data.id;
                const deleteUrl = routes.infaqSantriDestroy.replace(':id', infaqId);

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menghapus...';

                return fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Hapus';

                if (data.success) {
                    document.getElementById('delete-infaq-modal').classList.add('hidden');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        updateTable(); // Refresh tabel setelah sukses
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menghapus.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Hapus';
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan pada server.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Tutup modal delete
        document.getElementById('close-delete-infaq-modal').addEventListener('click', () => {
            document.getElementById('delete-infaq-modal').classList.add('hidden');
        });

        document.getElementById('cancel-delete-infaq-button').addEventListener('click', () => {
            document.getElementById('delete-infaq-modal').classList.add('hidden');
        });
    });
</script>
@endsection
