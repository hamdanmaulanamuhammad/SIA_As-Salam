@extends('layouts.admin')

@section('title', 'Detail Buku Kas')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 mt-6 gap-3">
        <h1 class="text-2xl font-bold">Detail Buku Kas Tahun {{ $bukuKas->tahun }}</h1>

        <div class="flex gap-2">
            <a href="{{ route('keuangan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
                <i class="fa fa-arrow-left mr-2"></i>Kembali
            </a>

            <button id="tambahTransaksiKasButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Tambah Transaksi
            </button>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Rekap Keuangan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-100 rounded-md">
                <p class="text-sm font-medium text-gray-600">Total Debet</p>
                <p class="text-lg font-bold text-blue-600">{{ 'Rp ' . number_format($totalDebet, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-red-100 rounded-md">
                <p class="text-sm font-medium text-gray-600">Total Kredit</p>
                <p class="text-lg font-bold text-red-600">{{ 'Rp ' . number_format($totalKredit, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-green-100 rounded-md">
                <p class="text-sm font-medium text-gray-600">Saldo Akhir</p>
                <p class="text-lg font-bold text-green-600">{{ 'Rp ' . number_format($totalDebet - $totalKredit, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Cash Flow Table -->
    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Sumber/Tujuan</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Bukti</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="transaksiKasTableBody" class="bg-white divide-y">
                @foreach($transaksiKas as $index => $item)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm">{{ ($transaksiKas->currentPage() - 1) * $transaksiKas->perPage() + $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->keterangan }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->sumber ?? $item->tujuan ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm {{ $item->jenis == 'debet' ? 'text-blue-600' : 'text-red-600' }}">{{ ucfirst($item->jenis) }}</td>
                    <td class="px-4 py-3 text-sm">{{ 'Rp ' . number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($item->bukti)
                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex space-x-2">
                            <button class="edit-transaksi-kas-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form action="{{ route('keuangan.buku-kas.transaksi.destroy', [$bukuKas->id, $item->id]) }}?tab=buku-kas" method="POST" class="delete-transaksi-kas-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $transaksiKas->appends(['tab' => 'buku-kas'])->links() }}
    </div>

    <!-- Modal for Transaksi Kas Form -->
    <div id="transaksi-kas-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-screen overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Form Transaksi Kas</h3>
                <button id="close-transaksi-kas-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="transaksi-kas-form" action="{{ route('keuangan.buku-kas.transaksi.store', $bukuKas->id) }}?tab=buku-kas" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="transaksi-kas-id" name="id">
                <input type="hidden" name="_method" id="transaksi-kas-method" value="POST">

                <!-- Field: Tanggal -->
                <div class="mb-4">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>

                <!-- Field: Jenis Transaksi -->
                <div class="mb-4">
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi <span class="text-red-600">*</span></label>
                    <select name="jenis" id="jenis" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Jenis Transaksi</option>
                        <option value="debet">Debet (Uang Masuk)</option>
                        <option value="kredit">Kredit (Uang Keluar)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih jenis transaksi untuk melanjutkan</p>
                </div>

                <!-- Field: Keterangan -->
                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                    <input type="text" name="keterangan" id="keterangan" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required placeholder="Masukkan keterangan transaksi">
                </div>

                <!-- Field: Sumber -->
                <div class="mb-4 hidden" id="sumber-field">
                    <label for="sumber" class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana <span class="text-red-600">*</span></label>
                    <input type="text" name="sumber" id="sumber" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Contoh: Donasi, Iuran, dll">
                    <p class="text-xs text-gray-500 mt-1">Dari mana uang ini berasal</p>
                </div>

                <!-- Field: Tujuan -->
                <div class="mb-4 hidden" id="tujuan-field">
                    <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengeluaran <span class="text-red-600">*</span></label>
                    <input type="text" name="tujuan" id="tujuan" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Contoh: Pembelian alat, Operasional, dll">
                    <p class="text-xs text-gray-500 mt-1">Untuk apa uang ini digunakan</p>
                </div>

                <!-- Field: Jumlah -->
                <div class="mb-4">
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-600">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number" name="jumlah" id="jumlah" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-8 py-1" required min="0" placeholder="0">
                    </div>
                </div>

                <!-- Field: Bukti -->
                <div class="mb-4">
                    <label for="bukti" class="block text-sm font-medium text-gray-700 mb-1">Bukti Transaksi (Nota/Kuitansi)</label>
                    <input type="file" name="bukti" id="bukti" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" accept="image/jpeg,image/png,application/pdf">
                    <p class="text-xs text-gray-500 mt-1">Upload gambar (JPEG/PNG) atau PDF, maksimal 2MB (opsional)</p>
                    <p id="bukti-preview" class="text-sm text-blue-600 mt-1"></p>
                    <p id="bukti-error" class="text-sm text-red-600 mt-1 hidden">File terlalu besar! Maksimal 2MB.</p>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-transaksi-kas-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 w-full sm:w-auto">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 w-full sm:w-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')@section('scripts')
<script>
    const routes = {
        transaksiKasStore: "{{ route('keuangan.buku-kas.transaksi.store', $bukuKas->id) }}",
        transaksiKasEdit: "{{ route('keuangan.buku-kas.transaksi.edit', [$bukuKas->id, ':id']) }}",
        transaksiKasUpdate: "{{ route('keuangan.buku-kas.transaksi.update', [$bukuKas->id, ':id']) }}",
        transaksiKasDestroy: "{{ route('keuangan.buku-kas.transaksi.destroy', [$bukuKas->id, ':id']) }}",
        keuanganBukuKasIndex: "{{ route('keuangan.buku-kas.transaksi.index', $bukuKas->id) }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Function to toggle field visibility based on jenis
        function toggleSumberTujuanFields() {
            const jenis = document.getElementById('jenis').value;
            const sumberField = document.getElementById('sumber-field');
            const tujuanField = document.getElementById('tujuan-field');
            const sumberInput = document.getElementById('sumber');
            const tujuanInput = document.getElementById('tujuan');

            // Reset visibility and required attributes
            sumberField.classList.add('hidden');
            tujuanField.classList.add('hidden');
            sumberInput.removeAttribute('required');
            tujuanInput.removeAttribute('required');
            sumberInput.value = '';
            tujuanInput.value = '';

            if (jenis === 'debet') {
                sumberField.classList.remove('hidden');
                sumberInput.setAttribute('required', 'required');
            } else if (jenis === 'kredit') {
                tujuanField.classList.remove('hidden');
                tujuanInput.setAttribute('required', 'required');
            }
        }

        // Event listener for jenis change
        document.getElementById('jenis').addEventListener('change', toggleSumberTujuanFields);

        // Reset form function
        function resetForm() {
            document.getElementById('transaksi-kas-form').reset();
            document.getElementById('transaksi-kas-id').value = '';
            document.getElementById('transaksi-kas-method').value = 'POST';
            document.getElementById('transaksi-kas-form').action = routes.transaksiKasStore + '?tab=buku-kas';
            document.querySelector('#transaksi-kas-form-modal h3').textContent = 'Form Transaksi Kas';
            document.getElementById('bukti-preview').textContent = '';
            document.getElementById('bukti-error').classList.add('hidden');
            document.getElementById('bukti').value = '';
            toggleSumberTujuanFields(); // Reset field visibility
        }

        // Modal Handlers
        document.getElementById('tambahTransaksiKasButton').addEventListener('click', () => {
            resetForm();
            document.getElementById('transaksi-kas-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-transaksi-kas-form-modal').addEventListener('click', () => {
            document.getElementById('transaksi-kas-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-transaksi-kas-form-button').addEventListener('click', () => {
            document.getElementById('transaksi-kas-form-modal').classList.add('hidden');
        });

        // File Preview and Size Validation
        document.getElementById('bukti').addEventListener('change', (e) => {
            const file = e.target.files[0];
            const preview = document.getElementById('bukti-preview');
            const error = document.getElementById('bukti-error');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (file) {
                if (file.size > maxSize) {
                    error.classList.remove('hidden');
                    preview.textContent = '';
                    e.target.value = ''; // Clear the file input
                    Swal.fire({
                        title: 'File Terlalu Besar!',
                        text: 'Ukuran file maksimal adalah 2MB.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    error.classList.add('hidden');
                    preview.textContent = `File terpilih: ${file.name}`;
                }
            } else {
                error.classList.add('hidden');
                preview.textContent = '';
            }
        });

        // Submit Form with validation
        const form = document.getElementById('transaksi-kas-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Custom validation
            const jenis = document.getElementById('jenis').value;
            const sumber = document.getElementById('sumber').value;
            const tujuan = document.getElementById('tujuan').value;
            const file = document.getElementById('bukti').files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (jenis === 'debet' && !sumber.trim()) {
                Swal.fire({
                    title: "Validasi Gagal!",
                    text: "Sumber dana harus diisi untuk transaksi debet (uang masuk).",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                document.getElementById('sumber').focus();
                return;
            }

            if (jenis === 'kredit' && !tujuan.trim()) {
                Swal.fire({
                    title: "Validasi Gagal!",
                    text: "Tujuan pengeluaran harus diisi untuk transaksi kredit (uang keluar).",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                document.getElementById('tujuan').focus();
                return;
            }

            if (file && file.size > maxSize) {
                Swal.fire({
                    title: "Validasi Gagal!",
                    text: "Ukuran file maksimal adalah 2MB.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                document.getElementById('bukti').value = '';
                document.getElementById('bukti-error').classList.remove('hidden');
                return;
            }

            sessionStorage.setItem('activeTab', 'buku-kas');

            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

            const id = form.querySelector('input[name="id"]').value;
            const formData = new FormData(form);
            if (id) formData.append('_method', 'PUT');

            const url = id ? routes.transaksiKasUpdate.replace(':id', id) + `?tab=buku-kas` : routes.transaksiKasStore + `?tab=buku-kas`;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';

                if (data.success) {
                    document.getElementById('transaksi-kas-form-modal').classList.add('hidden');
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location = routes.keuanganBukuKasIndex + `?tab=buku-kas`;
                    });
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
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
                console.error('Fetch error:', error);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';
                Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
            });
        });

        // Edit Data
        document.querySelectorAll('.edit-transaksi-kas-button').forEach(button => {
            button.addEventListener('click', () => {
                sessionStorage.setItem('activeTab', 'buku-kas');
                const itemId = button.getAttribute('data-id');

                fetch(routes.transaksiKasEdit.replace(':id', itemId), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const form = document.getElementById('transaksi-kas-form');
                        form.querySelector('input[name="id"]').value = data.data.id;
                        document.getElementById('tanggal').value = data.data.tanggal;
                        document.getElementById('keterangan').value = data.data.keterangan;
                        document.getElementById('jenis').value = data.data.jenis;
                        document.getElementById('jumlah').value = data.data.jumlah;

                        // Set sumber/tujuan after setting jenis
                        toggleSumberTujuanFields();

                        if (data.data.jenis === 'debet') {
                            document.getElementById('sumber').value = data.data.sumber || '';
                        } else if (data.data.jenis === 'kredit') {
                            document.getElementById('tujuan').value = data.data.tujuan || '';
                        }

                        document.getElementById('bukti-preview').textContent = data.data.bukti ? `File saat ini: ${data.data.bukti}` : '';
                        document.getElementById('bukti-error').classList.add('hidden');
                        form.querySelector('input[name="_method"]').value = 'PUT';
                        form.action = routes.transaksiKasUpdate.replace(':id', data.data.id) + `?tab=buku-kas`;
                        document.querySelector('#transaksi-kas-form-modal h3').textContent = 'Edit Transaksi Kas';
                        document.getElementById('transaksi-kas-form-modal').classList.remove('hidden');
                    } else {
                        Swal.fire('Gagal!', data.message || 'Data tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Edit error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data.', 'error');
                });
            });
        });

        // Delete Data
        document.querySelectorAll('.delete-transaksi-kas-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                sessionStorage.setItem('activeTab', 'buku-kas');

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus transaksi kas ini?",
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
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Transaksi kas berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location = routes.keuanganBukuKasIndex + `?tab=buku-kas`;
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message || 'Gagal menghapus transaksi kas.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus transaksi kas.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            });
        });

        // Initialize field visibility on page load
        toggleSumberTujuanFields();
    });
</script>
@endsection