<?php

use Illuminate\Support\Facades\Route;

// Login
Route::get('/', function () {
    return view('auth.login'); 
})-> name('login');

// Register
Route::get('/register', function () {
    return view('auth.register'); 
})->name('register');

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
    Route::get('/profile-admin', function () {
        return view('admin.profile-admin'); 
    })->name('profile-admin');

    

