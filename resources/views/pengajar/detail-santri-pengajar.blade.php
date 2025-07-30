@extends('layouts.pengajar')

@section('title', 'Detail Santri')

@section('content')
    <div class="container mx-auto p-4">
        <!-- Navigation Path -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="{{ route('pengajar.santri.index') }}" class="text-blue-600 hover:underline">Data Santri</a> > <span class="text-gray-800">Detail Data Santri</span>
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
            <!-- Left Column - Photo and Quick Info -->
            <div class="w-full lg:w-1/4">
                <!-- Photo Section -->
                <div class="mb-4">
                    <div class="w-36 h-44 mb-3 border border-gray-200 rounded-md overflow-hidden mx-auto">
                        <img src="{{ $santri->pas_foto_path ? Storage::url($santri->pas_foto_path) : 'https://placehold.co/50x50' }}" alt="Foto Santri" class="w-full h-full object-cover" id="santri-photo">
                    </div>
                </div>

                <!-- Quick Info Box -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 mb-6">
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
                                        $namaSantri = str_replace(' ', '_', $santri->nama_lengkap);
                                        $namaSantri = preg_replace('/[^A-Za-z0-9_]/', '', $namaSantri);
                                        $extension = pathinfo($santri->akta_path, PATHINFO_EXTENSION);
                                        $displayName = "Akta_{$namaSantri}.{$extension}";
                                    @endphp
                                    <span class="font-medium">{{ $displayName }}</span>
                                    <a href="{{ Storage::url($santri->akta_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Preview Akta">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pengajar.download.akta', $santri->id) }}" class="text-green-500 hover:text-green-700" title="Download Akta">
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
    </div>
@endsection
