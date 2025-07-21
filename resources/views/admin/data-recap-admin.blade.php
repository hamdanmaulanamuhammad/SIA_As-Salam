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

    <!-- Notifikasi -->
    @if (session('success'))
        <script>
            Swal.fire('Berhasil!', '{{ session('success') }}', 'success');
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire('Error!', '{{ session('error') }}', 'error');
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire('Perhatian!', 'Terdapat kesalahan dalam input Anda.', 'warning');
        </script>
    @endif

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
                        <td class="px-4 py-3 text-sm">Rp {{ number_format($recap->bonus, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">{{ count(json_decode($recap->dates)) }} hari</td>
                        <td class="px-4 py-3 text-sm flex gap-2">
                            <a href="{{ route('recaps.show', $recap->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center" title="Detail">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <button onclick="editRecap({{ $recap->id }}, '{{ $recap->batas_keterlambatan }}', {{ $recap->mukafaah }}, {{ $recap->bonus }}, '{{ json_decode($recap->dates, true) ? implode(',', json_decode($recap->dates, true)) : '' }}')" class="w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center" title="Edit">
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
                        @error('batas_keterlambatan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="mukafaah" class="block text-sm font-medium text-gray-700 mb-1">Mukafaah (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="mukafaah" id="mukafaah" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ old('mukafaah') }}" required>
                        @error('mukafaah')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="bonus" class="block text-sm font-medium text-gray-700 mb-1">Bonus Penuh (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="bonus" id="bonus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" value="{{ old('bonus') }}" required>
                        @error('bonus')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Presensi <span class="text-red-600">*</span></label>
                        <input type="text" name="tanggal" id="tanggal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" placeholder="Pilih tanggal" required>
                        @error('tanggal')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
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
            document.getElementById('rekap-form-modal').classList.remove('hidden');
        });

        // Menangani penutupan modal
        document.getElementById('close-rekap-form-modal')?.addEventListener('click', () => {
            document.getElementById('rekap-form-modal')?.classList.add('hidden');
        });

        document.getElementById('cancel-rekap-form-button')?.addEventListener('click', () => {
            document.getElementById('rekap-form-modal')?.classList.add('hidden');
        });

        // Validasi client-side untuk format tanggal
        document.getElementById('rekap-form')?.addEventListener('submit', function(event) {
            const tanggalInput = document.getElementById('tanggal').value;
            const dates = tanggalInput.split(',').map(date => date.trim());
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            const isValid = dates.every(date => dateRegex.test(date));
            if (!isValid) {
                event.preventDefault();
                Swal.fire('Error!', 'Tanggal harus dalam format YYYY-MM-DD.', 'error');
                return;
            }

            const requiredFields = this.querySelectorAll('[required]');
            let isValidFields = true;
            requiredFields.forEach(field => {
                if (!field.value) {
                    isValidFields = false;
                    field.classList.add('border-red-500');
                    field.addEventListener('input', () => {
                        if (field.value) field.classList.remove('border-red-500');
                    }, { once: true });
                }
            });

            if (!isValidFields) {
                event.preventDefault();
                Swal.fire('Perhatian!', 'Harap isi semua field yang wajib diisi.', 'warning');
            }
        });
    });

    // Fungsi untuk edit rekap
    function editRecap(id, batas_keterlambatan, mukafaah, bonus, tanggal) {
        document.getElementById('recap-id').value = id;
        document.getElementById('batas_keterlambatan').value = batas_keterlambatan;
        document.getElementById('mukafaah').value = mukafaah;
        document.getElementById('bonus').value = bonus;
        document.getElementById('tanggal').value = tanggal;
        document.getElementById('modal-title').textContent = 'Edit Rekap Presensi';
        document.getElementById('rekap-form').action = `/recaps/${id}`;
        document.getElementById('rekap-form').querySelector('input[name="_method"]').value = 'PUT';
        document.getElementById('rekap-form-modal').classList.remove('hidden');
    }

    // Fungsi konfirmasi hapus
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
                event.target.submit();
            }
        });
        return false;
    }
</script>
@endsection
