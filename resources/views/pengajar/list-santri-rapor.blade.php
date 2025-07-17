@extends('layouts.pengajar')

@section('title', 'Daftar Santri Kelas')

@section('content')
<div class="container px-5 mx-auto py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6 mt-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Daftar Santri - {{ $kelasSemester->kelas->nama_kelas }}
            </h3>
            <p class="text-sm text-gray-600">
                {{ $kelasSemester->semester->nama_semester }} ({{ $kelasSemester->semester->tahun_ajaran }})
            </p>
        </div>
        <div>
            <a href="{{ route('pengajar.kelas-semester', $kelasSemester->semester->id) }}" class="text-sm px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Kelas -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600"><strong>Wali Kelas:</strong></p>
                <p class="text-gray-800">{{ $kelasSemester->waliKelas ? $kelasSemester->waliKelas->full_name : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><strong>Mudir:</strong></p>
                <p class="text-gray-800">{{ $kelasSemester->mudir ? $kelasSemester->mudir->full_name : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><strong>Jumlah Santri:</strong></p>
                <p class="text-gray-800">{{ $santriList->count() }} santri</p>
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

    <!-- Tabel Santri -->
    <div class="overflow-x-auto">
        <table class="w-full mt-4 bg-white rounded-lg shadow">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">NIS</th>
                    <th class="px-4 py-3 min-w-52">Nama Lengkap</th>
                    <th class="px-4 py-3 min-w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @if ($santriList->isEmpty())
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Belum ada santri di kelas ini</p>
                        </td>
                    </tr>
                @else
                    @foreach ($santriList as $index => $santriKelas)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-4 py-3 text-sm">{{ ($santriList->currentPage() - 1) * $santriList->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm">{{ $santriKelas->santri->nis }}</td>
                            <td class="px-4 py-3 text-sm">{{ $santriKelas->santri->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('pengajar.rapor.show', [$kelasSemester->id, $santriKelas->santri->id]) }}"
                                   class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700"
                                   title="Input Rapor">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $santriList->links() }}
    </div>
</div>
@endsection
