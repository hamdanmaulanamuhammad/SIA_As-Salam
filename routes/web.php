<?php
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasMapelSemesterController;
use App\Http\Controllers\KelasSemesterController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\PengajarAkademikController;
use App\Http\Controllers\PengajarKelasMapelSemesterController;
use App\Http\Controllers\PengajarKelasSemesterController;
use App\Http\Controllers\PengajarMapelController;
use App\Http\Controllers\PengajarRaporController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\SemesterController;
use App\Models\Santri;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;

// Login
Route::get('/', [LoginRegisterController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [LoginRegisterController::class, 'authenticate'])->name('login.authenticate');

// Register
Route::get('/register', [LoginRegisterController::class, 'register'])->name('register')->middleware('guest');
Route::post('/register', [LoginRegisterController::class, 'store'])->name('register.store');

// Logout
Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');

// Rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Admin Route
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard-admin', function () {
            return view('admin.dashboard-admin');
        })->name('dashboard-admin');

        // Presence
        Route::get('/presence-admin', [PresenceController::class, 'index'])->name('presence-admin');
        Route::post('/presence-admin', [PresenceController::class, 'store'])->name('presence.store');
        Route::get('/presence-admin/edit/{id}', [PresenceController::class, 'edit'])->name('presence.edit');
        Route::put('/presence-admin/{id}', [PresenceController::class, 'update'])->name('presence.update');
        Route::delete('/presence-admin/{id}', [PresenceController::class, 'destroy'])->name('presence.destroy');

        // Data Recap
        Route::get('/recaps', [RecapController::class, 'index'])->name('recaps.index');
        Route::post('/recaps', [RecapController::class, 'store'])->name('recaps.store');
        Route::get('/recaps/{id}', [RecapController::class, 'show'])->name('recaps.show');
        Route::get('/recaps/{id}/edit', [RecapController::class, 'edit'])->name('recaps.edit');
        Route::put('/recaps/{id}', [RecapController::class, 'update'])->name('recaps.update');
        Route::delete('/recaps/{id}', [RecapController::class, 'destroy'])->name('recaps.destroy');

        // Data Santri
        Route::get('/data-santri', [SantriController::class, 'index'])->name('santri-admin');
        Route::post('/santri', [SantriController::class, 'store'])->name('santri.store');
        Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
        Route::get('/santri/{id}/edit', [SantriController::class, 'edit'])->name('santri.edit');
        Route::put('/santri/{id}', [SantriController::class, 'update'])->name('santri.update');
        Route::delete('/santri/{id}', [SantriController::class, 'destroy'])->name('santri.destroy');

        //Detail Santri
        Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
        Route::get('/santri/{id}/download-akta', [SantriController::class, 'downloadAkta'])->name('download.akta');


        // Presensi Manual
        Route::get('/manual-attendance-admin', function () {
            return view('admin.manual-attendance-admin');
        })->name('manual-attendance-admin');

        // Data Pengajar
        Route::get('/data-pengajar', [PengajarController::class, 'showTeacherList'])->name('pengajar.show');
        Route::get('/teachers/{id}', [PengajarController::class, 'showTeacherDetail'])->name('teachers.detail');
        Route::put('/teachers/{id}', [PengajarController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}', [PengajarController::class, 'deleteTeacher'])->name('teachers.delete');
        Route::post('/teachers/{id}/contracts', [PengajarController::class, 'storeContract'])->name('contracts.store');
        Route::put('/teachers/{id}/contracts/{contract_id}', [PengajarController::class, 'updateContract'])->name('contracts.update');
        Route::delete('/teachers/{id}/contracts/{contract_id}', [PengajarController::class, 'deleteContract'])->name('contracts.destroy');
        // Permintaan Pendaftaran
        // Rute untuk menampilkan permintaan registrasi
        Route::get('/registration-request-admin', [PengajarController::class, 'showRegistrationRequests'])->name('registration-request-admin');

        // Rute untuk menerima dan menolak pendaftaran
        Route::post('/registration/accept/{id}', [PengajarController::class, 'acceptRegistration'])->name('registration.accept');
        Route::post('/registration/reject/{id}', [PengajarController::class, 'rejectRegistration'])->name('registration.reject');

        // Routes untuk profile dengan prefix
        Route::prefix('profile/admin')->group(function () {
            Route::get('/', [ProfileController::class, 'showProfile'])->name('profile.admin.index');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.admin.update');
            Route::post('/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.admin.uploadPhoto');
            Route::delete('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.admin.deletePhoto');
            Route::post('/upload-signature', [ProfileController::class, 'uploadSignature'])->name('profile.admin.uploadSignature');
            Route::delete('/delete-signature', [ProfileController::class, 'deleteSignature'])->name('profile.admin.deleteSignature');
        });

        Route::prefix('akademik')->group(function () {
            Route::get('/', [AkademikController::class, 'index'])->name('akademik.index');

            // Kelas routes
            Route::get('kelas', [KelasController::class, 'index'])->name('akademik.kelas.index');
            Route::get('kelas/create', [KelasController::class, 'create'])->name('akademik.kelas.create');
            Route::post('kelas', [KelasController::class, 'store'])->name('akademik.kelas.store');
            Route::get('kelas/{id}', [KelasController::class, 'show'])->name('akademik.kelas.show');
            Route::get('kelas/{id}/edit', [KelasController::class, 'edit'])->name('akademik.kelas.edit');
            Route::put('kelas/{id}', [KelasController::class, 'update'])->name('akademik.kelas.update');
            Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('akademik.kelas.destroy');

            // Mapel routes
            Route::resource('mapel', MapelController::class)->names([
                'index' => 'akademik.mapel.index',
                'create' => 'akademik.mapel.create',
                'store' => 'akademik.mapel.store',
                'show' => 'akademik.mapel.show',
                'edit' => 'akademik.mapel.edit',
                'update' => 'akademik.mapel.update',
                'destroy' => 'akademik.mapel.destroy',
            ]);

            // Semester routes
            Route::resource('semester', SemesterController::class)->names([
                'index' => 'akademik.semester.index',
                'create' => 'akademik.semester.create',
                'store' => 'akademik.semester.store',
                'show' => 'akademik.semester.show',
                'edit' => 'akademik.semester.edit',
                'update' => 'akademik.semester.update',
                'destroy' => 'akademik.semester.destroy',
            ]);

            // Route untuk mapel kelas-semester (diletakkan di atas untuk prioritas)
            Route::post('kelas-semester/mapel', [KelasMapelSemesterController::class, 'store'])->name('akademik.kelas-semester.mapel.store');
            Route::delete('kelas-semester/mapel/{id}', [KelasMapelSemesterController::class, 'destroy'])->name('akademik.kelas-semester.mapel.destroy');

            // Route untuk kelas-semester (ubah untuk menghindari konflik)
            Route::get('semester/{semester}/kelas-semester', [KelasSemesterController::class, 'index'])->name('akademik.kelas-semester');
            Route::post('semester/{semester}/kelas-semester/store', [KelasSemesterController::class, 'store'])->name('akademik.kelas-semester.store');
            Route::get('kelas-semester/{id}/edit', [KelasSemesterController::class, 'edit'])->name('akademik.kelas-semester.edit');
            Route::put('kelas-semester/{id}', [KelasSemesterController::class, 'update'])->name('akademik.kelas-semester.update');
            Route::delete('kelas-semester/{id}', [KelasSemesterController::class, 'destroy'])->name('akademik.kelas-semester.destroy');

            // Rapor routes
            Route::get('rapor/{kelasSemesterId}', [RaporController::class, 'index'])->name('akademik.rapor.index');
            Route::get('rapor/{kelasSemesterId}/{santriId}', [RaporController::class, 'show'])->name('akademik.rapor.show');
            Route::put('rapor/{kelasSemesterId}/{santriId}', [RaporController::class, 'update'])->name('akademik.rapor.update');
            Route::get('rapor/{kelasSemesterId}/{santriId}/pdf', [RaporController::class, 'generatePdf'])->name('akademik.rapor.pdf');
            Route::get('rapor/{kelasSemesterId}/{santriId}/preview', [RaporController::class, 'previewRapor'])->name('akademik.rapor.preview');
        });
    });

    // Pengajar Route
    Route::middleware([CheckRole::class . ':pengajar'])->group(function () {
        // Dashboard
        Route::get('/dashboard-pengajar', [PresenceController::class, 'indexPengajar'])->name('dashboard-pengajar');

        // Data Recap
        Route::get('/data-recap-pengajar', function () {
            return view('pengajar.data-recap-pengajar');
        })->name('data-recap-pengajar');

        //Presensi
        Route::get('/presence', [PresenceController::class, 'index'])->name('presence.index');
        Route::post('/presence', [PresenceController::class, 'store'])->name('pengajar.presence.store');
        Route::get('/presence-pengajar/edit/{id}', [PresenceController::class, 'edit'])->name('pengajar.presence.edit');
        Route::put('/presence/{id}', [PresenceController::class, 'update'])->name('pengajar.presence.update');
        Route::delete('/presence/{id}', [PresenceController::class, 'destroy'])->name('pengajar.presence.destroy');

        // Kehadiran
        Route::get('/attendance-pengajar', function () {
            return view('pengajar.attendance-pengajar');
        })->name('attendance-pengajar');

        // Profile
        Route::prefix('profile-pengajar')->group(function () {
            Route::get('/', [ProfileController::class, 'showProfile'])->name('profile.pengajar.index');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.pengajar.update');
            Route::post('/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.pengajar.uploadPhoto');
            Route::delete('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.pengajar.deletePhoto');
            Route::post('/upload-signature', [ProfileController::class, 'uploadSignature'])->name('profile.pengajar.uploadSignature');
            Route::delete('/delete-signature', [ProfileController::class, 'deleteSignature'])->name('profile.pengajar.deleteSignature');
        });

       Route::prefix('pengajar/akademik')->group(function () {
            // Route yang sudah ada (dari implementasi sebelumnya)
            Route::get('/', [PengajarAkademikController::class, 'index'])->name('pengajar.akademik.index');
            Route::get('semester/{semester}/kelas-semester', [PengajarKelasSemesterController::class, 'index'])->name('pengajar.kelas-semester');
            Route::post('semester/{semester}/kelas-semester/store', [PengajarKelasSemesterController::class, 'store'])->name('pengajar.kelas-semester.store');
            Route::get('kelas-semester/{id}/edit', [PengajarKelasSemesterController::class, 'edit'])->name('pengajar.kelas-semester.edit');
            Route::put('kelas-semester/{id}', [PengajarKelasSemesterController::class, 'update'])->name('pengajar.kelas-semester.update');
            Route::delete('kelas-semester/{id}', [PengajarKelasSemesterController::class, 'destroy'])->name('pengajar.kelas-semester.destroy');
            Route::post('kelas-semester/mapel', [PengajarKelasMapelSemesterController::class, 'store'])->name('pengajar.kelas-semester.mapel.store');
            Route::delete('kelas-semester/mapel/{id}', [PengajarKelasMapelSemesterController::class, 'destroy'])->name('pengajar.kelas-semester.mapel.destroy');
            Route::get('mapel/create', [PengajarMapelController::class, 'create'])->name('pengajar.akademik.mapel.create');
            Route::post('mapel', [PengajarMapelController::class, 'store'])->name('pengajar.akademik.mapel.store');
            Route::get('mapel/{id}/edit', [PengajarMapelController::class, 'edit'])->name('pengajar.akademik.mapel.edit');
            Route::put('mapel/{id}', [PengajarMapelController::class, 'update'])->name('pengajar.akademik.mapel.update');
            Route::delete('mapel/{id}', [PengajarMapelController::class, 'destroy'])->name('pengajar.akademik.mapel.destroy');

            // Route baru untuk rapor pengajar
            Route::get('rapor/{kelasSemesterId}', [PengajarRaporController::class, 'index'])->name('pengajar.rapor.index');
            Route::get('rapor/{kelasSemesterId}/{santriId}', [PengajarRaporController::class, 'show'])->name('pengajar.rapor.show');
            Route::put('rapor/{kelasSemesterId}/{santriId}', [PengajarRaporController::class, 'update'])->name('pengajar.rapor.update');
            Route::get('rapor/{kelasSemesterId}/{santriId}/preview', [PengajarRaporController::class, 'previewRapor'])->name('pengajar.rapor.preview');
            Route::get('rapor/{kelasSemesterId}/{santriId}/pdf', [PengajarRaporController::class, 'generatePdf'])->name('pengajar.rapor.pdf');
        });
    });
});
