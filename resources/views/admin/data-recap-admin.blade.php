@extends('layouts.admin')

@section('title', 'Daftar Rekap Presensi')

@section('content')
<div class="container px-6 mx-auto grid">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mt-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 text-center md:text-left">Daftar Rekap Presensi</h1>
        <button id="tambahRekapButton" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200 w-full md:w-auto">
            <i class="fas fa-plus mr-2"></i>Rekap Mukafaah
        </button>
    </div>

    <!-- Search dan Filter -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <input type="text" id="searchInput" placeholder="Cari periode..." class="w-full md:w-64 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            <select id="periodeFilter" class="w-full md:w-48 px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                <option value="">Semua Periode</option>
                @foreach ($recaps as $recap)
                    <option value="{{ $recap->getRawOriginal('periode') }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $recap->getRawOriginal('periode'), 'Asia/Jakarta')->locale('id')->translatedFormat('F Y') }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Tampilkan</span>
            <select id="entriesSelect" class="px-2 py-1 text-sm border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-600">entri</span>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div class="overflow-x-auto">
        <table class="w-full mt-4 bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Batas Keterlambatan</th>
                    <th class="px-4 py-3">Mukafaah</th>
                    <th class="px-4 py-3">Bonus Penuh</th>
                    <th class="px-4 py-3">Jumlah Hari</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="rekapTableBody" class="bg-white divide-y">
                @foreach ($recaps as $index => $recap)
                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $recap->getRawOriginal('periode'), 'Asia/Jakarta')->locale('id')->translatedFormat('F Y') }}
                        </td>
            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($recap->batas_keterlambatan)->format('H:i') }}</td>
                        <td class="px-4 py-3 text-sm">Rp {{ number_format($recap->mukafaah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">

Rp {{ number_format($recap->bonus, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">{{ count(json_decode($recap->dates)) }} hari</td>
                        <td class="px-4 py-3 text-sm flex gap-2">
                            <a href="{{ route('recaps.show', $recap->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center" title="Detail">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <button onclick="editRecap({{ $recap->id }})" class="w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('recaps.destroy', $recap->id) }}" method="POST" onsubmit="return confirmDelete(event)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 text-white bg-red-600 rounded-md flex items-center justify-center" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal untuk Form Rekap -->
    <div id="rekap-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-4xl max-h-screen overflow-y-auto">
            <span id="close-rekap-form-modal" class="float-right cursor-pointer text-gray-500">×</span>
            <h2 class="text-lg font-semibold mb-4" id="modal-title">Form Rekap Presensi</h2>

            <form id="rekap-form" action="{{ route('recaps.store') }}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="id" id="recap-id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                    <div>
                        <label for="batas_keterlambatan" class="block text-sm font-medium text-gray-700 mb-1">Batas Keterlambatan <span class="text-red-600">*</span></label>
                        <input type="time" name="batas_keterlambatan" id="batas_keterlambatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ old('batas_keterlambatan') }}" required>
                        <span id="batas_keterlambatan_error" class="text-red-500 text-sm hidden"></span>
                    </div>
                    <div>
                        <label for="mukafaah" class="block text-sm font-medium text-gray-700 mb-1">Mukafaah (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="mukafaah" id="mukafaah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ old('mukafaah') }}" required>
                        <span id="mukafaah_error" class="text-red-500 text-sm hidden"></span>
                    </div>
                    <div>
                        <label for="bonus" class="block text-sm font-medium text-gray-700 mb-1">Bonus Penuh (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="bonus" id="bonus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ old('bonus') }}" required>
                        <span id="bonus_error" class="text-red-500 text-sm hidden"></span>
                    </div>
                    <div class="md:col-span-2">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Presensi <span class="text-red-600">*</span></label>
                        <input type="text" name="tanggal" id="tanggal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Pilih tanggal" required>
                        <span id="tanggal_error" class="text-red-500 text-sm hidden"></span>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end p-5 border-t gap-4">
                    <button type="button" id="cancel-rekap-form-button" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Batal</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 rounded bg-green-500 text-white hover:bg-green-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editRecap(id) {
        fetch(`/recaps/${id}/edit`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('recap-id').value = data.data.id;
                document.getElementById('batas_keterlambatan').value = data.data.batas_keterlambatan;
                document.getElementById('mukafaah').value = data.data.mukafaah;
                document.getElementById('bonus').value = data.data.bonus;
                document.getElementById('tanggal').value = JSON.parse(data.data.dates).join(',');
                document.getElementById('modal-title').textContent = 'Edit Rekap Presensi';
                document.getElementById('rekap-form').action = `/recaps/${id}`;
                document.getElementById('rekap-form').querySelector('input[name="_method"]').value = 'PUT';
                document.getElementById('rekap-form-modal').classList.remove('hidden');
            } else {
                Swal.fire('Gagal!', data.message || 'Gagal memuat data rekap.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat memuat data rekap.', 'error');
        });
    }

    function confirmDelete(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data rekap akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = event.target.closest('form');
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Terjadi kesalahan saat menghapus data.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        title: data.success ? 'Berhasil!' : 'Gagal!',
                        text: data.message || (data.success ? 'Data berhasil dihapus.' : 'Gagal menghapus data.'),
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
                    Swal.fire('Error!', error.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                });
            }
        });
        return false;
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Flatpickr untuk multiple date picker
        flatpickr("#tanggal", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            locale: {
                weekdays: {
                    shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                    longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                },
                months: {
                    shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                    longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                }
            }
        });

        // Fungsi untuk membuka modal tambah
        document.getElementById('tambahRekapButton')?.addEventListener('click', () => {
            document.getElementById('rekap-form').reset();
            document.getElementById('recap-id').value = '';
            document.getElementById('modal-title').textContent = 'Form Rekap Presensi';
            document.getElementById('rekap-form').action = "{{ route('recaps.store') }}";
            document.getElementById('rekap-form').querySelector('input[name="_method"]').value = 'POST';
            document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('input').forEach(el => el.classList.remove('border-red-500'));
            document.getElementById('rekap-form-modal').classList.remove('hidden');
        });

        // Menangani penutupan modal
        document.getElementById('close-rekap-form-modal')?.addEventListener('click', () => {
            document.getElementById('rekap-form-modal')?.classList.add('hidden');
        });

        document.getElementById('cancel-rekap-form-button')?.addEventListener('click', () => {
            document.getElementById('rekap-form-modal')?.classList.add('hidden');
        });

        // Validasi client-side dan submit form dengan AJAX
        document.getElementById('rekap-form')?.addEventListener('submit', function(event) {
            event.preventDefault();

            // Reset pesan error
            document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('input').forEach(el => el.classList.remove('border-red-500'));

            // Validasi client-side
            const tanggalInput = document.getElementById('tanggal').value;
            const dates = tanggalInput.split(',').map(date => date.trim()).filter(date => date);
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            const isValid = dates.every(date => dateRegex.test(date));
            if (!isValid) {
                document.getElementById('tanggal_error').textContent = 'Tanggal harus dalam format YYYY-MM-DD.';
                document.getElementById('tanggal_error').classList.remove('hidden');
                document.getElementById('tanggal').classList.add('border-red-500');
                Swal.fire('Error!', 'Tanggal harus dalam format YYYY-MM-DD.', 'error');
                return;
            }

            const requiredFields = this.querySelectorAll('[required]');
            let isValidFields = true;
            requiredFields.forEach(field => {
                if (!field.value) {
                    isValidFields = false;
                    field.classList.add('border-red-500');
                    document.getElementById(`${field.id}_error`).textContent = `${field.name} wajib diisi.`;
                    document.getElementById(`${field.id}_error`).classList.remove('hidden');
                    field.addEventListener('input', () => {
                        if (field.value) {
                            field.classList.remove('border-red-500');
                            document.getElementById(`${field.id}_error`).classList.add('hidden');
                        }
                    }, { once: true });
                }
            });

            if (!isValidFields) {
                Swal.fire('Perhatian!', 'Harap isi semua field yang wajib diisi.', 'warning');
                return;
            }

            // Kirim data dengan AJAX
            const formData = new FormData(this);
            const id = document.getElementById('recap-id').value;
            const url = id ? `/recaps/${id}` : this.action;
            formData.append('_method', id ? 'PUT' : 'POST');

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Terjadi kesalahan pada server.');
                    });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: data.success ? 'Berhasil!' : 'Gagal!',
                    text: data.message || (data.success ? 'Operasi berhasil.' : 'Operasi gagal.'),
                    icon: data.success ? 'success' : 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (data.success) {
                        document.getElementById('rekap-form-modal').classList.add('hidden');
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', error.message || 'Terjadi kesalahan pada server.', 'error');
            });
        });

        // Fungsi untuk filter rekap
        document.getElementById('periodeFilter')?.addEventListener('change', function() {
            const periode = this.value;
            fetch(`/recaps/filter?periode=${periode}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('rekapTableBody');
                    tbody.innerHTML = '';
                    data.data.forEach((recap, index) => {
                        const row = `
                            <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                <td class="px-4 py-3 text-sm">${index + 1}</td>
                                <td class="px-4 py-3 text-sm">${new Date(recap.periode + '-01').toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}</td>
                                <td class="px-4 py-3 text-sm">${recap.batas_keterlambatan}</td>
                                <td class="px-4 py-3 text-sm">Rp ${new Intl.NumberFormat('id-ID').format(recap.mukafaah)}</td>
                                <td class="px-4 py-3 text-sm">Rp ${new Intl.NumberFormat('id-ID').format(recap.bonus)}</td>
                                <td class="px-4 py-3 text-sm">${JSON.parse(recap.dates).length} hari</td>
                                <td class="px-4 py-3 text-sm flex gap-2">
                                    <a href="/recaps/${recap.id}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center" title="Detail">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <button onclick="editRecap(${recap.id})" class="w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/recaps/${recap.id}" method="POST" onsubmit="return confirmDelete(event)">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                        <button type="submit" class="w-8 h-8 text-white bg-red-600 rounded-md flex items-center justify-center" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>`;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal memuat data.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat memfilter data.', 'error');
            });
        });

        // Fungsi untuk pencarian (client-side)
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#rekapTableBody tr');
            rows.forEach(row => {
                const periode = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                row.style.display = periode.includes(searchTerm) ? '' : 'none';
            });
        });

        // Fungsi untuk mengatur jumlah entri (client-side)
        document.getElementById('entriesSelect')?.addEventListener('change', function() {
            const limit = parseInt(this.value);
            const rows = document.querySelectorAll('#rekapTableBody tr');
            rows.forEach((row, index) => {
                row.style.display = index < limit ? '' : 'none';
            });
        });
    });
</script>
@endsection
