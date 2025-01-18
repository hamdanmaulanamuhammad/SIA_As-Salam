<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\ProfileController;
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

        // Events  
        Route::get('/events-admin', [EventController::class, 'index'])->name('events-admin');  
        Route::get('/events-admin/create', [EventController::class, 'create'])->name('events.create');  
        Route::post('/events-admin', [EventController::class, 'store'])->name('events.store');  
        Route::get('/events-admin/{event}/edit', [EventController::class, 'edit'])->name('events.edit');  
        Route::put('/events-admin/{event}', [EventController::class, 'update'])->name('events.update');  
        Route::delete('/events-admin/{event}', [EventController::class, 'destroy'])->name('events.destroy');  
        // Data Recap
        Route::get('/data-recap-admin', function () {
            return view('admin.data-recap-admin'); 
        })->name('data-recap-admin');

        // Kehadiran
        Route::get('/attendance-admin', function () {
            return view('admin.attendance-admin'); 
        })->name('attendance-admin');

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
        Route::get('/profile-admin', [ProfileController::class, 'showAdminProfile'])->name('profile-admin');

        // Rute untuk mengupdate profil admin
        Route::post('/profile-admin/update', [ProfileController::class, 'updateProfile'])->name('profile.admin.update');

        // Rute untuk mengupload foto profil admin
        Route::post('/profile-admin/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.admin.uploadPhoto');

        // Rute untuk menghapus foto profil admin
        Route::delete('/profile-admin/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.admin.deletePhoto');
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
        Route::get('/profile-pengajar', [ProfileController::class, 'showPengajarProfile'])->name('profile-pengajar');

        // Rute untuk mengupdate profil pengajar
        Route::post('/profile-pengajar/update', [ProfileController::class, 'updateProfile'])->name('profile.pengajar.update');

        // Rute untuk mengupload foto profil pengajar
        Route::post('/profile-pengajar/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.pengajar.uploadPhoto');

        // Rute untuk menghapus foto profil pengajar
        Route::post('/profile-pengajar/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.pengajar.deletePhoto');
    });
});
