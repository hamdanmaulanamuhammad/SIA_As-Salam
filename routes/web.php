<?php
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecapController;
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
        Route::get('/data-recap-admin', [RecapController::class, 'index'])->name('data-recap-admin');
        Route::post('/data-recap-admin', [RecapController::class, 'store'])->name('recap.store');
        Route::get('/data-recap-admin/create', [RecapController::class, 'create'])->name('recap.create');
        Route::get('/data-recap-admin/{id}', [RecapController::class, 'show'])->name('recap.show');
        Route::get('/data-recap-admin/{id}/edit', [RecapController::class, 'edit'])->name('recap.edit');
        Route::put('/data-recap-admin/{id}', [RecapController::class, 'update'])->name('recap.update');
        Route::delete('/data-recap-admin/{id}', [RecapController::class, 'destroy'])->name('recap.destroy');

        Route::get('/details-recap-admin', function () {
            return view('admin.details-recap-admin'); 
        })->name('details-recap-admin');
        
        // Santri
        Route::get('/santri-admin', function () {
            return view('admin.santri-admin'); 
        })->name('santri-admin');

        Route::get('/santri-admin/data-santri', function () {
            return view('admin.data-santri-admin');
        })->name('data-santri');


        // Presensi Manual
        Route::get('/manual-attendance-admin', function () {
            return view('admin.manual-attendance-admin'); 
        })->name('manual-attendance-admin');

        // Detail Pengajar
        Route::get('/teacher-details-admin', [PengajarController::class, 'showTeacherDetails'])->name('teacher-details-admin');
        // Rute untuk menghapus pengajar
        Route::delete('/teachers/{id}', [PengajarController::class, 'deleteTeacher'])->name('teachers.delete');

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
        Route::get('/dashboard-pengajar', function () {
            return view('pengajar.dashboard-pengajar'); 
        })->name('dashboard-pengajar');

        // Data Recap
        Route::get('/data-recap-pengajar', function () {
            return view('pengajar.data-recap-pengajar'); 
        })->name('data-recap-pengajar');

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
