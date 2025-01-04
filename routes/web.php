<?php

use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', [LoginRegisterController::class, 'login'])->name('login');
Route::post('/login', [LoginRegisterController::class, 'authenticate'])->name('login.authenticate');

// Register
Route::get('/register', [LoginRegisterController::class, 'register'])->name('register');
Route::post('/register', [LoginRegisterController::class, 'store'])->name('register.store');

// Logout
Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');

// Admin Route
    // Dashboard
    Route::get('/dashboard-admin', function () {
        return view('admin.dashboard-admin'); 
    })->name('dashboard-admin');

    // Events
    Route::get('/events-admin', function () {
        return view('admin.events-admin'); 
    })->name('events-admin');

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
    Route::get('/teacher-details-admin', function () {
        return view('admin.teacher-details-admin'); 
    })->name('teacher-details-admin');

    // Permintaan Pendaftaran
    Route::get('/registration-request-admin', function () {
        return view('admin.registration-request-admin'); 
    })->name('registration-request-admin');

    // Profile
    Route::get('/profile-admin', [ProfileController::class, 
    'showAdminProfile'])->name('profile-admin');

    
// Pengajar Route
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
    Route::get('/profile-pengajar', [ProfileController::class, 
    'showPengajarProfile'])->name('profile-pengajar');
