@extends('layouts.pengajar')

@section('title', 'Input Rapor')

@section('content')
<div class="container px-5 mx-auto py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800">Input Rapor - {{ $santri->nama_lengkap }}</h3>
        <a href="{{ route('pengajar.rapor.index', $kelasSemester->id) }}" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Identitas Santri -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600"><strong>Nama:</strong></p>
                <p class="text-gray-800">{{ $santri->nama_lengkap }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><strong>NIS:</strong></p>
                <p class="text-gray-800">{{ $santri->nis }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><strong>Mustawa (Kelas):</strong></p>
                <p class="text-gray-800">{{ $kelasSemester->kelas->nama_kelas }}</p>
            </div>
        </div>
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
    <form id="rapor-form" action="{{ route('pengajar.rapor.update', [$kelasSemester->id, $santri->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tabel Nilai per Kategori -->
        @foreach ($mapelsByCategory as $category => $mapels)
            <h4 class="text-md font-semibold text-gray-800 mb-4">{{ $category }}</h4>
            <div class="overflow-x-auto mb-6">
                <table class="w-full bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th class="px-4 py-3 w-24">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach ($mapels as $mapel)
                            @php
                                $nilaiRapor = $nilaiRaporData->where('kelas_mapel_semester_id', $mapel->id)->first();
                                $nilai = $nilaiRapor ? $nilaiRapor->nilai : null;
                            @endphp
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-4 py-3 text-sm">{{ $mapel->mataPelajaran->nama_mapel }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <input type="number" name="nilai[{{ $mapel->id }}]" value="{{ old('nilai.' . $mapel->id, $nilai) }}"
                                           class="w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2"
                                           min="0" max="100" required>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Ringkasan Nilai -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600"><strong>Jumlah Nilai:</strong></p>
                    <p class="text-gray-800" id="jumlah-nilai">{{ $jumlahNilai ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><strong>Rata-rata:</strong></p>
                    <p class="text-gray-800" id="rata-rata">{{ $rataRata ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><strong>Predikat:</strong></p>
                    <p class="text-gray-800" id="predikat">{{ $predikat ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><strong>Terbilang:</strong></p>
                    <p class="text-gray-800" id="terbilang">{{ $terbilang ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Catatan dan Keputusan -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                <textarea id="catatan" name="catatan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2"
                          rows="4">{{ old('catatan', $catatanRapor ? $catatanRapor->catatan : '') }}</textarea>
            </div>
            <div class="mb-4">
                <label for="keputusan_kelas_id" class="block text-sm font-medium text-gray-700">Keputusan Kelas</label>
                <select id="keputusan_kelas_id" name="keputusan_kelas_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-3 py-2">
                    <option value="">Tidak Naik Kelas</option>
                    @foreach (\App\Models\Kelas::all() as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('keputusan_kelas_id', $catatanRapor ? $catatanRapor->keputusan_kelas_id : '') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tombol Simpan dan Lihat Rapor -->
        <div class="flex justify-end space-x-4">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
            <a href="{{ route('pengajar.rapor.preview', [$kelasSemester->id, $santri->id]) }}"
               class="px-4 py-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700">Lihat Rapor</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('rapor-form');
    const nilaiInputs = document.querySelectorAll('input[name^="nilai"]');
    const jumlahNilaiElement = document.getElementById('jumlah-nilai');
    const rataRataElement = document.getElementById('rata-rata');
    const predikatElement = document.getElementById('predikat');
    const terbilangElement = document.getElementById('terbilang');

    function calculatePredikat(rataRata) {
        if (rataRata > 90) return 'MUMTAZ';
        if (rataRata > 80) return 'JAYYID JIDDAN';
        if (rataRata > 70) return 'JAYYID';
        return 'MAQBUL';
    }

    function updateSummary() {
        let total = 0;
        let count = 0;
        nilaiInputs.forEach(input => {
            const nilai = parseFloat(input.value) || 0;
            if (nilai > 0) {
                total += nilai;
                count++;
            }
        });

        const jumlahNilai = total;
        const rataRata = count > 0 ? (total / count).toFixed(2) : 0;
        const predikat = rataRata ? calculatePredikat(rataRata) : '-';
        jumlahNilaiElement.textContent = jumlahNilai || '-';
        rataRataElement.textContent = rataRata || '-';
        predikatElement.textContent = predikat;
        terbilangElement.textContent = rataRata ? numberToWords(Math.round(rataRata)) : '-';
    }

    function numberToWords(number) {
        const units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        const teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        const tens = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];

        if (number === 0) return 'Nol';
        if (number < 10) return units[number];
        if (number < 20) return teens[number - 10];
        if (number < 100) {
            const unit = number % 10;
            const ten = Math.floor(number / 10);
            return `${tens[ten]}${unit ? ' ' + units[unit] : ''}`;
        }
        if (number === 100) return 'Seratus';
        return '';
    }

    nilaiInputs.forEach(input => {
        input.addEventListener('input', updateSummary);
    });

    updateSummary();

    form.addEventListener('submit', function(event) {
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
                return response.json().then(err => { throw new Error(JSON.stringify(err) || 'Terjadi kesalahan pada server.') });
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
