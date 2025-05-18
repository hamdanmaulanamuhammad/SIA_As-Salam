<?php
use App\Http\Controllers\SantriController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecapController;
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

        // Profile
        Route::get('/profile-admin', [ProfileController::class, 'showProfile'])->name('view-admin-profile');

        // Rute untuk mengupdate profil admin
        Route::post('/profile-admin/update', [ProfileController::class, 'updateProfile'])->name('profile.adminupdate');

        // Rute untuk mengupload foto profil admin
        Route::post('/profile-admin/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.adminuploadPhoto');

        // Rute untuk menghapus foto profil admin
        Route::delete('/profile-admin/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.admindeletePhoto');
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
        Route::get('/profile-pengajar', [ProfileController::class, 'showProfile'])->name('view-pengajar-profile');

        // Rute untuk mengupdate profil pengajar
        Route::post('/profile-pengajar/update', [ProfileController::class, 'updateProfile'])->name('profile.pengajar-update');

        // Rute untuk mengupload foto profil pengajar
        Route::post('/profile-pengajar/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.pengajar-uploadPhoto');

        // Rute untuk menghapus foto profil pengajar
        Route::post('/profile-pengajar/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.pengajar-deletePhoto');
    });
});
