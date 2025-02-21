@extends('layouts.admin')

@section('title', 'Santri')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Manajemen Santri</h1>
        
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow cursor-pointer"  onclick="window.location.href='{{ route('data-santri') }}'">
                <h2 class="text-lg font-semibold">Data Santri</h2>
                <p class="text-gray-600">Lihat dan kelola data santri</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow cursor-pointer">
                <h2 class="text-lg font-semibold">Presensi Santri</h2>
                <p class="text-gray-600">Lihat dan kelola presensi santri</p>
            </div>
        </div>
    </div>
@endsection