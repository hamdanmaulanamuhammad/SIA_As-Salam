@extends('layouts.admin')          
  
@section('title', 'Acara')          
  
@section('content')          
    <div class="container px-6 mx-auto grid">          
        <div class="flex justify-end mb-6 mt-6">          
            <button id="create-event-button" class="text-sm px-4 py-2 text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 transition duration-200">+ Buat Acara</button>          
        </div>          
  
        <!-- Card Acara Hari Ini -->        
        <div id="event-info" class="{{ $todayEvents->isEmpty() ? 'hidden' : '' }} mb-6">        
            <div class="bg-white rounded-lg shadow p-4 flex justify-between">        
                <div class="mb-2">        
                    <div class="flex items-center mb-2">        
                        <h3 class="text-lg font-semibold text-gray-800" id="event-name-display">{{ $todayEvents->first()->name ?? 'Tidak ada acara hari ini' }}</h3>        
                    </div>        
                    @if ($todayEvents->isNotEmpty())        
                        <div class="space-y-2">        
                            <div>        
                                <p class="text-sm text-gray-800 mb-2">Terlambat dalam</p>        
                                <span class="border bg-yellow-500 text-white text-sm font-bold rounded-md p-2" id="event-late-timer">00:00:00</span>        
                            </div>        
                            <div>        
                                <p class="text-sm text-gray-800 mb-2">Presensi berakhir dalam</p>        
                                <span class="border bg-red-600 text-white text-sm font-bold rounded-md p-2" id="event-presence-timer">00:00:00</span>        
                            </div>        
                        </div>        
                    @endif        
                </div>        
              
                <button id="edit-event-button" class="text-blue-600 text-xl hover:text-blue-800">        
                    <i class="fas fa-edit"></i>        
                </button>        
            </div>        
        </div>        
  
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Riwayat Acara</h3>          
        <!-- Filter dan Entries -->          
        <div class="md:flex md:justify-between">          
            <div class="flex space-x-4 mb-6 lg:mb-2">          
                <select id="year-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">          
                    <option value="">Tahun</option>          
                    <option value="2024">2024</option>          
                    <option value="2023">2023</option>          
                    <option value="2022">2022</option>          
                </select>          
                <select id="month-filter" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm">          
                    <option value="">Bulan</option>          
                    <option value="1">Januari</option>          
                    <option value="2">Februari</option>          
                    <option value="3">Maret</option>          
                    <option value="4">April</option>          
                    <option value="5">Mei</option>          
                    <option value="6">Juni</option>          
                    <option value="7">Juli</option>          
                    <option value="8">Agustus</option>          
                    <option value="9">September</option>          
                    <option value="10">Oktober</option>          
                    <option value="11">November</option>          
                    <option value="12">Desember</option>          
                </select>          
                <button id="filter-button" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 text-sm">Terapkan Filter</button>          
            </div>          
            <div class="flex items-center">          
                <label for="entries" class="text-sm font-medium text-gray-700">Show</label>          
                <select id="entries" class="ml-2 border border-gray-300 rounded-md">          
                    <option value="5">5</option>          
                    <option value="10">10</option>          
                    <option value="20">20</option>          
                </select>          
                <span class="ml-2 text-sm text-gray-600">entries</span>          
            </div>          
        </div>          
  
        <!-- Tabel Riwayat Acara -->          
        <div class="overflow-x-auto">          
            <table class="w-full mt-4 bg-white rounded-lg shadow">          
                <thead>          
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">          
                        <th class="px-4 py-3">No</th>          
                        <th class="px-4 py-3">Nama Acara</th>          
                        <th class="px-4 py-3">Jumlah Hadir</th>          
                        <th class="px-4 py-3">Tidak Hadir</th>          
                        <th class="px-4 py-3">Izin</th>          
                        <th class="px-4 py-3">Waktu Pelaksanaan</th>          
                    </tr>          
                </thead>          
                <tbody class="bg-white divide-y">        
                    <tr class="text-gray-700">        
                        <td class="px-4 py-3">1</td>        
                        <td class="px-4 py-3">Acara 1</td>        
                        <td class="px-4 py-3">20</td>        
                        <td class="px-4 py-3">5</td>        
                        <td class="px-4 py-3">2</td>        
                        <td class="px-4 py-3">10:00 AM, 01/01/2024</td>        
                    </tr>        
                    <tr class="text-gray-700">        
                        <td class="px-4 py-3">2</td>        
                        <td class="px-4 py-3">Acara 2</td>        
                        <td class="px-4 py-3">15</td>        
                        <td class="px-4 py-3">3</td>        
                        <td class="px-4 py-3">1</td>        
                        <td class="px-4 py-3">11:00 AM, 15/01/2024</td>        
                    </tr>        
                </tbody>           
            </table>          
        </div>          
  
        <!-- Tabel Acara Mendatang -->        
        <h3 class="text-lg font-semibold text-gray-800 mb-6 mt-10">Acara Mendatang</h3>        
        <div class="overflow-x-auto">        
            <table class="w-full mt-4 bg-white rounded-lg shadow">        
                <thead>        
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">        
                        <th class="px-4 py-3">No</th>        
                        <th class="px-4 py-3">Nama Acara</th>        
                        <th class="px-4 py-3">Waktu Mulai</th>        
                        <th class="px-4 py-3">Batas Keterlambatan</th>        
                        <th class="px-4 py-3">Waktu Berakhir</th>        
                        <th class="px-4 py-3">Aksi</th>        
                    </tr>        
                </thead>        
                <tbody class="bg-white divide-y">        
                    @foreach ($upcomingEvents as $index => $event)        
                        <tr class="text-gray-700">        
                            <td class="px-4 py-3">{{ $index + 1 }}</td>        
                            <td class="px-4 py-3">{{ $event->name }}</td>        
                            <td class="px-4 py-3">{{ $event->start_time->format('h:i A, d/m/Y') }}</td>        
                            <td class="px-4 py-3">{{ $event->late_limit }} Menit</td>        
                            <td class="px-4 py-3">{{ $event->end_time->format('h:i A, d/m/Y') }}</td>        
                            <td class="px-4 py-3">        
                                <button class="text-blue-600 hover:text-blue-800">        
                                    <i class="fas fa-edit"></i>        
                                </button>        
                                <button class="text-red-600 hover:text-red-800 ml-2">        
                                    <i class="fas fa-trash"></i>        
                                </button>        
                            </td>        
                        </tr>        
                    @endforeach        
                </tbody>        
            </table>        
        </div>          
  
        <!-- Modal untuk Membuat Acara -->          
        <div id="create-event-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">          
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">          
                <span id="close-modal" class="float-right cursor-pointer text-gray-500">&times;</span>          
                <h2 class="text-lg font-semibold">Buat Acara Baru</h2>          
                <form id="event-form" class="mt-4"> <!-- Pastikan ID form ini benar -->    
                    <div class="mb-4">          
                        <label for="event-name-input" class="block text-sm font-medium text-gray-700">Nama Acara</label>          
                        <input type="text" id="event-name-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="event-start-input" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>          
                        <input type="datetime-local" id="event-start-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="event-end-input" class="block text-sm font-medium text-gray-700">Waktu Berakhir</label>          
                        <input type="time" id="event-end-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="event-late-input" class="block text-sm font-medium text-gray-700">Batas Keterlambatan (menit)</label>          
                        <input type="number" id="event-late-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required min="0" />          
                    </div>          
                    <div class="flex justify-end">          
                        <button type="button" id="cancel-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>          
                        <button type="submit" id="save-button" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>          
                    </div>          
                </form>        
            </div>          
        </div>          
  
        <!-- Modal untuk Edit Acara -->          
        <div id="edit-event-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">          
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-md">          
                <span id="close-edit-modal" class="float-right cursor-pointer text-gray-500">&times;</span>          
                <h2 class="text-lg font-semibold">Edit Acara</h2>          
                <form id="edit-event-form" class="mt-4">          
                    <div class="mb-4">          
                        <label for="edit-event-name-input" class="block text-sm font-medium text-gray-700">Nama Acara</label>          
                        <input type="text" id="edit-event-name-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="edit-event-start-input" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>          
                        <input type="datetime-local" id="edit-event-start-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="edit-event-end-input" class="block text-sm font-medium text-gray-700">Waktu Berakhir</label>          
                        <input type="time" id="edit-event-end-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required />          
                    </div>          
                    <div class="mb-4">          
                        <label for="edit-event-late-input" class="block text-sm font-medium text-gray-700">Batas Keterlambatan (menit)</label>          
                        <input type="number" id="edit-event-late-input" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 px-2 py-1" required min="0" />          
                    </div>          
                    <div class="flex justify-end">          
                        <button type="button" id="cancel-edit-button" class="px-4 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md hover:bg-gray-500">Batal</button>          
                        <button type="submit" id="save-button" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan</button>          
                    </div>          
                </form>          
                <button id="delete-event-button" class="mt-4 text-red-600 hover:text-red-800">Batalkan Acara</button>          
            </div>          
        </div>          
    </div>          
@endsection      
  
@section('scripts')            
<script>            
    document.addEventListener('DOMContentLoaded', () => {    
        // JavaScript khusus untuk halaman acara            
        document.getElementById('create-event-button').addEventListener('click', () => {            
            document.getElementById('create-event-modal').classList.remove('hidden');            
        });            
      
        document.getElementById('close-modal').addEventListener('click', () => {            
            document.getElementById('create-event-modal').classList.add('hidden');            
        });            
      
        document.getElementById('close-edit-modal').addEventListener('click', () => {            
            document.getElementById('edit-event-modal').classList.add('hidden');            
        });            
      
        document.getElementById('cancel-button').addEventListener('click', () => {            
            document.getElementById('create-event-modal').classList.add('hidden');            
        });            
      
        document.getElementById('cancel-edit-button').addEventListener('click', () => {            
            document.getElementById('edit-event-modal').classList.add('hidden');            
        });            
      
        // Menangani pengiriman form untuk membuat acara            
        const eventForm = document.getElementById('event-form');  
        if (eventForm) {  
            eventForm.addEventListener('submit', (event) => {            
                event.preventDefault(); // Mencegah pengiriman form default            
      
                // Mengambil nilai dari input            
                const eventName = document.getElementById('event-name-input').value;            
                const eventStart = document.getElementById('event-start-input').value;            
                const eventEnd = document.getElementById('event-end-input').value;            
                const eventLate = document.getElementById('event-late-input').value;            
      
                // Mengambil CSRF token  
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
      
                // Mengirim data ke server            
                fetch('/events-admin', {            
                    method: 'POST',            
                    headers: {            
                        'Content-Type': 'application/json',            
                        'X-CSRF-TOKEN': csrfToken            
                    },            
                    body: JSON.stringify({            
                        name: eventName,            
                        start_time: eventStart,            
                        end_time: eventEnd,            
                        late_limit: eventLate            
                    })            
                })            
                .then(response => {            
                    if (!response.ok) {            
                        return response.json().then(err => { throw err; });            
                    }            
                    return response.json();            
                })            
                .then(data => {            
                    // Menampilkan informasi acara            
                    document.getElementById('event-name-display').textContent = eventName;            
                    document.getElementById('event-late-timer').textContent = formatTime(eventLate * 60);            
                    document.getElementById('event-presence-timer').textContent = formatTime(60);            
                    document.getElementById('event-info').classList.remove('hidden');            
      
                    // Menutup modal            
                    document.getElementById('create-event-modal').classList.add('hidden');            
      
                    // Menampilkan SweetAlert sukses            
                    Swal.fire({            
                        title: 'Sukses!',            
                        text: 'Acara berhasil dibuat.',            
                        icon: 'success',            
                        confirmButtonText: 'OK'            
                    });            
                })            
                .catch(err => {            
                    // Menampilkan error            
                    Swal.fire({            
                        title: 'Error!',            
                        text: err.error || 'Terjadi kesalahan saat membuat acara.',            
                        icon: 'error',            
                        confirmButtonText: 'OK'            
                    });            
                });            
            });  
        } else {  
            console.error('Form acara tidak ditemukan.');  
        }  
      
        // Function to format time in hh:mm:ss            
        function formatTime(seconds) {            
            const hours = Math.floor(seconds / 3600);            
            const minutes = Math.floor((seconds % 3600) / 60);            
            const secs = seconds % 60;            
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;            
        }            
      
        // Menangani tombol edit            
        document.getElementById('edit-event-button').addEventListener('click', () => {            
            document.getElementById('edit-event-modal').classList.remove('hidden');            
            // Mengisi form edit dengan data acara yang ada            
            document.getElementById('edit-event-name-input').value = document.getElementById('event-name-display').textContent;            
            document.getElementById('edit-event-start-input').value = '2025-01-20T09:00'; // Contoh nilai            
            document.getElementById('edit-event-end-input').value = '09:00'; // Contoh nilai            
            document.getElementById('edit-event-late-input').value = 10; // Contoh nilai            
        });            
      
        // Menangani pengiriman form edit            
        document.getElementById('edit-event-form').addEventListener('submit', (event) => {            
            event.preventDefault(); // Mencegah pengiriman form default            
      
            // Mengambil nilai dari input            
            const eventName = document.getElementById('edit-event-name-input').value;            
            const eventLate = document.getElementById('edit-event-late-input').value;            
      
            // Mengirim data ke server            
            fetch(`/events-admin/${eventId}`, { // Ganti eventId dengan ID acara yang sesuai            
                method: 'PUT',            
                headers: {            
                    'Content-Type': 'application/json',            
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')            
                },            
                body: JSON.stringify({            
                    name: eventName,            
                    late_limit: eventLate            
                })            
            })            
            .then(response => {            
                if (!response.ok) {            
                    return response.json().then(err => { throw err; });            
                }            
                return response.json();            
            })            
            .then(data => {            
                // Menampilkan informasi acara yang telah diedit            
                document.getElementById('event-name-display').textContent = eventName;            
                document.getElementById('event-late-timer').textContent = formatTime(eventLate * 60);            
      
                // Menutup modal            
                document.getElementById('edit-event-modal').classList.add('hidden');            
      
                // Menampilkan SweetAlert sukses            
                Swal.fire({            
                    title: 'Sukses!',            
                    text: 'Acara berhasil diedit.',            
                    icon: 'success',            
                    confirmButtonText: 'OK'            
                });            
            })            
            .catch(err => {            
                // Menampilkan error            
                Swal.fire({            
                    title: 'Error!',            
                    text: err.error || 'Terjadi kesalahan saat mengedit acara.',            
                    icon: 'error',            
                    confirmButtonText: 'OK'            
                });            
            });            
        });            
      
        // Menangani pembatalan acara            
        document.getElementById('delete-event-button').addEventListener('click', () => {            
            Swal.fire({            
                title: 'Konfirmasi',            
                text: "Apakah Anda yakin ingin membatalkan acara ini?",            
                icon: 'warning',            
                showCancelButton: true,            
                confirmButtonText: 'Ya, batalkan',            
                cancelButtonText: 'Tidak'            
            }).then((result) => {            
                if (result.isConfirmed) {            
                    fetch(`/events-admin/${eventId}`, { // Ganti eventId dengan ID acara yang sesuai            
                        method: 'DELETE',            
                        headers: {            
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')            
                        }            
                    })            
                    .then(response => {            
                        if (!response.ok) {            
                            return response.json().then(err => { throw err; });            
                        }            
                        return response.json();            
                    })            
                    .then(data => {            
                        // Menyembunyikan card acara            
                        document.getElementById('event-info').classList.add('hidden');            
                        document.getElementById('edit-event-modal').classList.add('hidden');            
                        document.getElementById('create-event-modal').classList.add('hidden');            
      
                        // Menampilkan SweetAlert sukses            
                        Swal.fire('Dibatalkan!', 'Acara telah dibatalkan.', 'success');            
                    })            
                    .catch(err => {            
                        // Menampilkan error            
                        Swal.fire({            
                            title: 'Error!',            
                            text: err.error || 'Terjadi kesalahan saat membatalkan acara.',            
                            icon: 'error',            
                            confirmButtonText: 'OK'            
                        });            
                    });            
                }            
            });            
        });            
    });    
</script>            
@endsection        
