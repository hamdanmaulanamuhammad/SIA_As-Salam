@extends('layouts.pengajar')

@section('title', 'Data Santri')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6 mt-6">
        <h1 class="text-2xl font-bold">Data Santri</h1>
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
                    <td class="px-4 py-3 text-sm min-w-28">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('pengajar.santri.show', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center">
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

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            fetch(`{{ route("pengajar.santri.index") }}?${params.toString()}`, {
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
                form.action = '{{ route("pengajar.santri.index") }}';

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

        // Event listeners dengan debounce untuk search
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
    });
</script>
@endsection
