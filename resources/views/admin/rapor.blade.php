@extends('layouts.admin')

@section('title', 'Input Rapor Santri')

@section('content')
<div class="container px-5 mx-auto py-6">
    <!-- Header -->
    <div class="flex justify-between mb-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800">
            Rapor Santri: {{ $santri->nama_lengkap }} (NIS: {{ $santri->nis }}) - {{ $kelasSemester->kelas->nama }} ({{ $kelasSemester->semester->nama_semester }} {{ $kelasSemester->semester->tahun_ajaran }})
        </h3>
        <a href="{{ route('akademik.rapor.index', $kelasSemester->id) }}" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Input Rapor -->
    <form id="rapor-form" action="{{ route('akademik.rapor.store', [$kelasSemester->id, $santri->id]) }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full mt-4 bg-white rounded-lg shadow-sm">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Mata Pelajaran</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($kelasMapelSemesters as $kategori => $mapels)
                        <tr class="bg-gray-100">
                            <td colspan="4" class="px-4 py-2 text-sm font-semibold text-gray-700">{{ $kategori }}</td>
                        </tr>
                        @foreach ($mapels as $index => $kelasMapelSemester)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $kelasMapelSemester->mapel->nama }}</td>
                                <td class="px-4 py-3">{{ $kelasMapelSemester->mapel->kategori }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="nilai[{{ $kelasMapelSemester->id }}]" value="{{ $kelasMapelSemester->nilaiRapor->first()->nilai ?? '' }}" class="w-20 border border-gray-300 rounded-md px-2 py-1" min="0" max="100" required>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-600">Belum ada mata pelajaran untuk kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Rata-rata dan Predikat -->
        <div class="mt-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Rata-rata Nilai</label>
                <p class="text-sm text-gray-600">{{ number_format($averageNilai, 2) }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Predikat</label>
                <p class="text-sm text-gray-600">{{ $predikat }}</p>
            </div>
        </div>

        <!-- Catatan dan Keputusan -->
        <div class="mt-6">
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                <textarea id="catatan" name="catatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" rows="4">{{ $catatan }}</textarea>
            </div>
            <div class="mb-4">
                <label for="keputusan" class="block text-sm font-medium text-gray-700">Keputusan (Kelas Tujuan)</label>
                <select id="keputusan" name="keputusan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2" required>
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $keputusan == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Wali Kelas dan Mudir -->
        <div class="mt-6 grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Wali Kelas</p>
                <p class="text-sm text-gray-600">{{ $kelasSemester->waliKelas->full_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Mudir</p>
                <p class="text-sm text-gray-600">{{ $kelasSemester->mudir->full_name }}</p>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">
                <i class="fas fa-save"></i> Simpan Rapor
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('rapor-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan pada server.'); });
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: data.success ? 'Berhasil!' : 'Gagal!',
                text: data.message,
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
            Swal.fire('Error!', error.message || 'Terjadi kesalahan pada server.', 'error');
        });
    });
});
</script>
@endsection
