@extends('layouts.admin')

@section('title', 'Menu Keuangan')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6 mt-6">
        <h1 class="text-2xl font-bold">Menu Keuangan</h1>
    </div>
    @php
        use Carbon\Carbon;
    @endphp

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="?tab=infaq" id="tab-infaq" class="tab-btn active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                Infaq Santri
            </a>
            <a href="?tab=administrasi-bulanan" id="tab-administrasi-bulanan" class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Administrasi Bulanan
            </a>
            <a href="?tab=buku-kas" id="tab-buku-kas" class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Buku Kas
            </a>
            <a href="?tab=bank-accounts" id="tab-bank-accounts" class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Rekening
            </a>
        </nav>
    </div>

    <!-- Tab Content Infaq Santri -->
    <div id="content-infaq" class="tab-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Infaq Tahunan</h2>
            <button id="tambahInfaqTahunanButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Infaq
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Total Pembayaran</th>
                        <th class="px-4 py-3">Total Infaq Wajib</th>
                        <th class="px-4 py-3">Total Infaq Sukarela</th>
                        <th class="px-4 py-3">Total Kekurangan</th>
                        <th class="px-4 py-3">Total Infaq</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="infaqTahunanTableBody" class="bg-white divide-y">
                    @foreach($infaqTahunan as $index => $item)
                    @php
                        $totalWajib = $item->infaqSantris->sum('infaq_wajib');
                        $totalSukarela = $item->infaqSantris->sum('infaq_sukarela');
                        $totalInfaqItem = $totalWajib + $totalSukarela;
                        $expectedWajib = $item->infaqSantris->count() * 12 * 10000;
                        $kekurangan = $expectedWajib - $totalWajib;
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($infaqTahunan->currentPage() - 1) * $infaqTahunan->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->infaqSantris->count() }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 font-medium">{{ 'Rp ' . number_format($totalWajib, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-green-600 font-medium">{{ 'Rp ' . number_format($totalSukarela, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-red-600 font-medium">{{ 'Rp ' . number_format($kekurangan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-purple-600 font-bold">{{ 'Rp ' . number_format($totalInfaqItem, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-infaq-tahunan-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('keuangan.infaq.tahunan.destroy', $item->id) }}?tab=infaq" method="POST" class="delete-infaq-tahunan-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('keuangan.infaq.santri.index', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $infaqTahunan->appends(['tab' => 'infaq'])->links() }}
        </div>
    </div>

    <!-- Tab Content Administrasi Bulanan -->
    <div id="content-administrasi-bulanan" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Administrasi Bulanan</h2>
            <button id="tambahAdministrasiBulananButton" class="inline-flex items-center min-w-48 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Administrasi Bulanan
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Bulan</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Rekening</th>
                        <th class="px-4 py-3">Jumlah Pengeluaran</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="administrasiBulananTableBody" class="bg-white divide-y">
                    @foreach($administrasiBulanan as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($administrasiBulanan->currentPage() - 1) * $administrasiBulanan->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->bulan }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 text-sm">
                            {{ $item->bankAccount ? ($item->bankAccount->bank_name . ' - ' . $item->bankAccount->account_number . ' - ' . $item->bankAccount->account_holder) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $item->pengeluaranBulanan->count() }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-administrasi-bulanan-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('keuangan.administrasi-bulanan.destroy', $item->id) }}?tab=administrasi-bulanan" method="POST" class="delete-administrasi-bulanan-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('keuangan.administrasi-bulanan.pengeluaran.index', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $administrasiBulanan->appends(['tab' => 'administrasi-bulanan'])->links() }}
        </div>
    </div>

    <!-- Tab Content Buku Kas -->
    <div id="content-buku-kas" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Buku Kas</h2>
            <button id="tambahBukuKasButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Buku Kas
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Jumlah Transaksi</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bukuKasTableBody" class="bg-white divide-y">
                    @foreach($bukuKas as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ ($bukuKas->currentPage() - 1) * $bukuKas->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->transaksiKas->count() }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-buku-kas-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('keuangan.buku-kas.destroy', $item->id) }}?tab=buku-kas" method="POST" class="delete-buku-kas-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 text-white bg-red-500 rounded-md flex items-center justify-center hover:bg-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('keuangan.buku-kas.transaksi.index', $item->id) }}" class="w-8 h-8 text-white bg-green-600 rounded-md flex items-center justify-center hover:bg-green-700">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $bukuKas->appends(['tab' => 'buku-kas'])->links() }}
        </div>
    </div>

    <!-- Tab Content Rekening -->
    <div id="content-bank-accounts" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Data Rekening</h2>
            <button id="tambahBankAccountButton" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-200">
                <i class="fa fa-plus mr-2"></i>Rekening
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Bank</th>
                        <th class="px-4 py-3">Nomor Rekening</th>
                        <th class="px-4 py-3">Nama Pemilik</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bankAccountsTableBody" class="bg-white divide-y">
                    @foreach($bankAccounts as $index => $account)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm">{{ $account->bank_name }}</td>
                        <td class="px-4 py-3 text-sm">{{ $account->account_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $account->account_holder }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <button class="edit-bank-account-button w-8 h-8 text-white bg-yellow-500 rounded-md flex items-center justify-center hover:bg-yellow-600" data-id="{{ $account->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('keuangan.bank-accounts.destroy', $account->id) }}?tab=bank-accounts" method="POST" class="delete-bank-account-form">
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
    </div>

    <!-- Modal for Infaq Tahunan Form -->
    <div id="infaq-tahunan-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Infaq Tahunan</h3>
                <button id="close-infaq-tahunan-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="infaq-tahunan-form" action="{{ route('keuangan.infaq.tahunan.store') }}?tab=infaq" method="POST">
                @csrf
                <input type="hidden" id="infaq-tahunan-id" name="id">
                <input type="hidden" name="_method" id="infaq-tahunan-method" value="POST">
                <div class="mb-4">
                    <label for="tahun_infaq" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-600">*</span></label>
                    <input type="number" name="tahun" id="tahun_infaq" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-infaq-tahunan-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Administrasi Bulanan Form -->
    <div id="administrasi-bulanan-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Administrasi Bulanan</h3>
                <button id="close-administrasi-bulanan-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="administrasi-bulanan-form" action="{{ route('keuangan.administrasi-bulanan.store') }}?tab=administrasi-bulanan" method="POST">
                @csrf
                <input type="hidden" id="administrasi-bulanan-id" name="id">
                <input type="hidden" name="_method" id="administrasi-bulanan-method" value="POST">
                <div class="mb-4">
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-600">*</span></label>
                    <select name="bulan" id="bulan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                        <option value="">Pilih Bulan</option>
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                            <option value="{{ $bulan }}">{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="tahun_administrasi" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-600">*</span></label>
                    <input type="number" name="tahun" id="tahun_administrasi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" min="2000" max="2100" required>
                </div>
                <div class="mb-4">
                    <label for="bank_account_id" class="block text-sm font-medium text-gray-700 mb-1">Rekening Tujuan</label>
                    <select name="bank_account_id" id="bank_account_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1">
                        <option value="">Pilih Rekening (Opsional)</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }} - {{ $account->account_holder }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-administrasi-bulanan-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Buku Kas Form -->
    <div id="buku-kas-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Buku Kas</h3>
                <button id="close-buku-kas-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="buku-kas-form" action="{{ route('keuangan.buku-kas.store') }}?tab=buku-kas" method="POST">
                @csrf
                <input type="hidden" id="buku-kas-id" name="id">
                <input type="hidden" name="_method" id="buku-kas-method" value="POST">
                <div class="mb-4">
                    <label for="tahun_buku_kas" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-600">*</span></label>
                    <input type="number" name="tahun" id="tahun_buku_kas" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-buku-kas-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Bank Account Form -->
    <div id="bank-account-form-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Rekening</h3>
                <button id="close-bank-account-form-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="bank-account-form" action="{{ route('keuangan.bank-accounts.store') }}?tab=bank-accounts" method="POST">
                @csrf
                <input type="hidden" id="bank-account-id" name="id">
                <input type="hidden" name="_method" id="bank-account-method" value="POST">
                <div class="mb-4">
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank <span class="text-red-600">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-600">*</span></label>
                    <input type="text" name="account_number" id="account_number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label for="account_holder" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik <span class="text-red-600">*</span></label>
                    <input type="text" name="account_holder" id="account_holder" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-bank-account-form-button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const routes = {
        infaqTahunanStore: "{{ route('keuangan.infaq.tahunan.store') }}",
        infaqTahunanEdit: "{{ route('keuangan.infaq.tahunan.edit', ':id') }}",
        infaqTahunanUpdate: "{{ route('keuangan.infaq.tahunan.update', ':id') }}",
        infaqTahunanDestroy: "{{ route('keuangan.infaq.tahunan.destroy', ':id') }}",
        administrasiBulananStore: "{{ route('keuangan.administrasi-bulanan.store') }}",
        administrasiBulananEdit: "{{ route('keuangan.administrasi-bulanan.edit', ':id') }}",
        administrasiBulananUpdate: "{{ route('keuangan.administrasi-bulanan.update', ':id') }}",
        administrasiBulananDestroy: "{{ route('keuangan.administrasi-bulanan.destroy', ':id') }}",
        bukuKasStore: "{{ route('keuangan.buku-kas.store') }}",
        bukuKasEdit: "{{ route('keuangan.buku-kas.edit', ':id') }}",
        bukuKasUpdate: "{{ route('keuangan.buku-kas.update', ':id') }}",
        bukuKasDestroy: "{{ route('keuangan.buku-kas.destroy', ':id') }}",
        bankAccountStore: "{{ route('keuangan.bank-accounts.store') }}",
        bankAccountEdit: "{{ route('keuangan.bank-accounts.edit', ':id') }}",
        bankAccountUpdate: "{{ route('keuangan.bank-accounts.update', ':id') }}",
        bankAccountDestroy: "{{ route('keuangan.bank-accounts.destroy', ':id') }}",
        keuanganIndex: "{{ route('keuangan.index') }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menangani tab
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            const content = document.getElementById('content-' + tabName);
            if (content) content.classList.remove('hidden');

            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) {
                activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
                activeTab.classList.remove('border-transparent', 'text-gray-500');
            }

            sessionStorage.setItem('activeTab', tabName);
        }

        // Inisialisasi tab berdasarkan URL atau sessionStorage
        const urlParams = new URLSearchParams(window.location.search);
        const validTabs = ['infaq', 'administrasi-bulanan', 'buku-kas', 'bank-accounts'];
        const tabFromUrl = urlParams.get('tab');
        const lastTab = sessionStorage.getItem('activeTab');
        const activeTab = tabFromUrl && validTabs.includes(tabFromUrl) ? tabFromUrl : (lastTab && validTabs.includes(lastTab) ? lastTab : 'infaq');
        showTab(activeTab);

        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const tabName = button.id.replace('tab-', '');
                showTab(tabName);
            });
        });

        // Fungsi umum untuk submit form
        function submitForm(formId, tabName, routesConfig) {
            const form = document.getElementById(formId);
            const modal = document.getElementById(`${formId}-modal`);
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                sessionStorage.setItem('activeTab', tabName);

                const id = form.querySelector('input[name="id"]').value;
                const formData = new FormData(form);
                if (id) formData.append('_method', 'PUT');

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';

                const url = id ? routesConfig.update.replace(':id', id) + `?tab=${tabName}` : routesConfig.store + `?tab=${tabName}`;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';

                    if (data.success) {
                        modal.classList.add('hidden');
                        Swal.fire({
                            title: "Berhasil!",
                            text: data.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location = routes.keuanganIndex + `?tab=${tabName}`;
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
        }

        // Fungsi umum untuk edit data
        function editData(buttonClass, tabName, modalId, formId, fields, routesConfig) {
            document.querySelectorAll(`.${buttonClass}`).forEach(button => {
                button.addEventListener('click', () => {
                    sessionStorage.setItem('activeTab', tabName);
                    const itemId = button.getAttribute('data-id');

                    fetch(routesConfig.edit.replace(':id', itemId), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const form = document.getElementById(formId);
                            form.querySelector('input[name="id"]').value = data.data.id;
                            Object.keys(fields).forEach(key => {
                                const element = document.getElementById(fields[key]);
                                // Handle nullable bank_account_id for select elements
                                if (element.tagName === 'SELECT' && key === 'bank_account_id') {
                                    element.value = data.data[key] || '';
                                } else {
                                    element.value = data.data[key];
                                }
                            });
                            form.querySelector('input[name="_method"]').value = 'PUT';
                            form.action = routesConfig.update.replace(':id', data.data.id) + `?tab=${tabName}`;
                            document.querySelector(`#${modalId} h3`).textContent = `Edit ${tabName.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}`;
                            document.getElementById(modalId).classList.remove('hidden');
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
        }

        // Fungsi umum untuk menghapus data
        function deleteData(formClass, tabName, entityName, routeDestroy) {
            document.querySelectorAll(`.${formClass}`).forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    sessionStorage.setItem('activeTab', tabName);

                    Swal.fire({
                        title: "Konfirmasi",
                        text: `Apakah Anda yakin ingin menghapus ${entityName} ini?`,
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
                                        text: `${entityName} berhasil dihapus.`,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location = routes.keuanganIndex + `?tab=${tabName}`;
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || `Gagal menghapus ${entityName}.`,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: `Terjadi kesalahan saat menghapus ${entityName}.`,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                });
            });
        }

        // Validasi tahun
        function validateYearInput(inputId) {
            const input = document.getElementById(inputId);
            input.addEventListener('input', () => {
                const value = parseInt(input.value);
                if (value < 1900 || value > 2100) {
                    input.setCustomValidity('Tahun harus antara 1900 dan 2100');
                } else {
                    input.setCustomValidity('');
                }
            });
        }

        // Inisialisasi fungsi untuk tab yang sudah ada
        submitForm('infaq-tahunan-form', 'infaq', {
            store: routes.infaqTahunanStore,
            update: routes.infaqTahunanUpdate
        });

        submitForm('administrasi-bulanan-form', 'administrasi-bulanan', {
            store: routes.administrasiBulananStore,
            update: routes.administrasiBulananUpdate
        });

        submitForm('buku-kas-form', 'buku-kas', {
            store: routes.bukuKasStore,
            update: routes.bukuKasUpdate
        });

        submitForm('bank-account-form', 'bank-accounts', {
            store: routes.bankAccountStore,
            update: routes.bankAccountUpdate
        });

        editData('edit-infaq-tahunan-button', 'infaq', 'infaq-tahunan-form-modal', 'infaq-tahunan-form', {
            tahun: 'tahun_infaq'
        }, {
            edit: routes.infaqTahunanEdit,
            update: routes.infaqTahunanUpdate
        });

        editData('edit-administrasi-bulanan-button', 'administrasi-bulanan', 'administrasi-bulanan-form-modal', 'administrasi-bulanan-form', {
            bulan: 'bulan',
            tahun: 'tahun_administrasi',
            bank_account_id: 'bank_account_id'
        }, {
            edit: routes.administrasiBulananEdit,
            update: routes.administrasiBulananUpdate
        });

        editData('edit-buku-kas-button', 'buku-kas', 'buku-kas-form-modal', 'buku-kas-form', {
            tahun: 'tahun_buku_kas'
        }, {
            edit: routes.bukuKasEdit,
            update: routes.bukuKasUpdate
        });

        editData('edit-bank-account-button', 'bank-accounts', 'bank-account-form-modal', 'bank-account-form', {
            bank_name: 'bank_name',
            account_number: 'account_number',
            account_holder: 'account_holder'
        }, {
            edit: routes.bankAccountEdit,
            update: routes.bankAccountUpdate
        });

        deleteData('delete-infaq-tahunan-form', 'infaq', 'Infaq Tahunan', routes.infaqTahunanDestroy);
        deleteData('delete-administrasi-bulanan-form', 'administrasi-bulanan', 'Administrasi Bulanan', routes.administrasiBulananDestroy);
        deleteData('delete-buku-kas-form', 'buku-kas', 'Buku Kas', routes.bukuKasDestroy);
        deleteData('delete-bank-account-form', 'bank-accounts', 'Rekening', routes.bankAccountDestroy);

        validateYearInput('tahun_infaq');
        validateYearInput('tahun_administrasi');
        validateYearInput('tahun_buku_kas');

        // Modal Handlers untuk tab yang sudah ada
        document.getElementById('tambahInfaqTahunanButton')?.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'infaq');
            document.getElementById('infaq-tahunan-form').reset();
            document.getElementById('infaq-tahunan-id').value = '';
            document.getElementById('infaq-tahunan-method').value = 'POST';
            document.getElementById('infaq-tahunan-form').action = routes.infaqTahunanStore + '?tab=infaq';
            document.querySelector('#infaq-tahunan-form-modal h3').textContent = 'Form Infaq Tahunan';
            document.getElementById('infaq-tahunan-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-infaq-tahunan-form-modal')?.addEventListener('click', () => {
            document.getElementById('infaq-tahunan-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-infaq-tahunan-form-button')?.addEventListener('click', () => {
            document.getElementById('infaq-tahunan-form-modal').classList.add('hidden');
        });

        document.getElementById('tambahAdministrasiBulananButton')?.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'administrasi-bulanan');
            document.getElementById('administrasi-bulanan-form').reset();
            document.getElementById('administrasi-bulanan-id').value = '';
            document.getElementById('administrasi-bulanan-method').value = 'POST';
            document.getElementById('administrasi-bulanan-form').action = routes.administrasiBulananStore + '?tab=administrasi-bulanan';
            document.querySelector('#administrasi-bulanan-form-modal h3').textContent = 'Form Administrasi Bulanan';
            document.getElementById('administrasi-bulanan-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-administrasi-bulanan-form-modal')?.addEventListener('click', () => {
            document.getElementById('administrasi-bulanan-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-administrasi-bulanan-form-button')?.addEventListener('click', () => {
            document.getElementById('administrasi-bulanan-form-modal').classList.add('hidden');
        });

        document.getElementById('tambahBukuKasButton')?.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'buku-kas');
            document.getElementById('buku-kas-form').reset();
            document.getElementById('buku-kas-id').value = '';
            document.getElementById('buku-kas-method').value = 'POST';
            document.getElementById('buku-kas-form').action = routes.bukuKasStore + '?tab=buku-kas';
            document.querySelector('#buku-kas-form-modal h3').textContent = 'Form Buku Kas';
            document.getElementById('buku-kas-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-buku-kas-form-modal')?.addEventListener('click', () => {
            document.getElementById('buku-kas-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-buku-kas-form-button')?.addEventListener('click', () => {
            document.getElementById('buku-kas-form-modal').classList.add('hidden');
        });

        document.getElementById('tambahBankAccountButton')?.addEventListener('click', () => {
            sessionStorage.setItem('activeTab', 'bank-accounts');
            document.getElementById('bank-account-form').reset();
            document.getElementById('bank-account-id').value = '';
            document.getElementById('bank-account-method').value = 'POST';
            document.getElementById('bank-account-form').action = routes.bankAccountStore + '?tab=bank-accounts';
            document.querySelector('#bank-account-form-modal h3').textContent = 'Form Rekening';
            document.getElementById('bank-account-form-modal').classList.remove('hidden');
        });

        document.getElementById('close-bank-account-form-modal')?.addEventListener('click', () => {
            document.getElementById('bank-account-form-modal').classList.add('hidden');
        });

        document.getElementById('cancel-bank-account-form-button')?.addEventListener('click', () => {
            document.getElementById('bank-account-form-modal').classList.add('hidden');
        });
    });
</script>
@endsection
