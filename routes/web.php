<?php
use App\Http\Controllers\AdministrasiBulananController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InfaqController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasMapelSemesterController;
use App\Http\Controllers\KelasSemesterController;
use App\Http\Controllers\KeuanganController;
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
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckRole;

Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');
Route::post('/login', [LoginRegisterController::class, 'authenticate'])->name('login.authenticate'); // BARIS INI YANG HILANG

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::fallback([LoginRegisterController::class, 'fallback']);

// Register
Route::get('/register', [LoginRegisterController::class, 'register'])->name('register')->middleware('guest');
Route::post('/register', [LoginRegisterController::class, 'store'])->name('register.store');
Route::get('/admin-access/secure-reg-9f3k2m7p8q4w6z1x', [LoginRegisterController::class, 'adminRegister'])->name('admin.register');
Route::post('/admin-access/secure-reg-9f3k2m7p8q4w6z1x', [LoginRegisterController::class, 'adminStore'])->name('admin.register.store');

// Logout
Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');

// Rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Admin Route
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard-admin', [DashboardController::class, 'indexAdmin'])->name('dashboard-admin');

        // Presence
        Route::get('/presence-admin', [PresenceController::class, 'index'])->name('presence-admin');
        Route::post('/presence-admin', [PresenceController::class, 'store'])->name('presence.store');
        Route::get('/presence-admin/edit/{id}', [PresenceController::class, 'edit'])->name('presence.edit');
        Route::put('/presence-admin/{id}', [PresenceController::class, 'update'])->name('presence.update');
        Route::delete('/presence-admin/{id}', [PresenceController::class, 'destroy'])->name('presence.destroy');

        // Data Recap
        Route::prefix('recaps')->name('recaps.')->group(function() {
            Route::get('/', [RecapController::class, 'index'])->name('index');
            Route::get('/filter', [RecapController::class, 'filter'])->name('filter');
            Route::post('/', [RecapController::class, 'store'])->name('store');
            Route::get('/{id}', [RecapController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [RecapController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RecapController::class, 'update'])->name('update');
            Route::delete('/{id}', [RecapController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/additional-mukafaahs', [RecapController::class, 'storeAdditionalMukafaah'])->name('additional.store');
            Route::get('/{id}/additional-mukafaahs/{mukafaahId}/edit', [RecapController::class, 'editAdditionalMukafaah'])->name('additional.edit');
            Route::put('/{id}/additional-mukafaahs/{mukafaahId}', [RecapController::class, 'updateAdditionalMukafaah'])->name('additional.update');
            Route::delete('/{id}/additional-mukafaahs/{mukafaahId}', [RecapController::class, 'destroyAdditionalMukafaah'])->name('additional.destroy');
        });

        Route::prefix('santri')->group(function () {
            // Data Santri
            Route::get('/data-santri', [SantriController::class, 'index'])->name('santri.index');
            Route::post('/store', [SantriController::class, 'store'])->name('santri.store');
            Route::get('/{id}/edit', [SantriController::class, 'edit'])->name('santri.edit');
            Route::put('/{id}', [SantriController::class, 'update'])->name('santri.update');
            Route::delete('/{id}', [SantriController::class, 'destroy'])->name('santri.destroy');

            // Detail Santri
            Route::get('/{id}', [SantriController::class, 'show'])->name('santri.show');
            Route::get('/{id}/download-akta', [SantriController::class, 'downloadAkta'])->name('download.akta');
        });

        // Data Pengajar
        Route::prefix('teachers')->group(function () {
            Route::get('/data', [PengajarController::class, 'showTeacherList'])->name('pengajar.show');
            Route::get('/{id}', [PengajarController::class, 'showTeacherDetail'])->name('teachers.detail');
            Route::put('/{id}/reset-password', [PengajarController::class, 'resetPassword'])->name('teachers.reset-password');
            Route::delete('/{id}', [PengajarController::class, 'deleteTeacher'])->name('teachers.delete');
            Route::post('/{id}/contracts', [PengajarController::class, 'storeContract'])->name('contracts.store');
            Route::put('/{id}/contracts/{contract_id}', [PengajarController::class, 'updateContract'])->name('contracts.update');
            Route::delete('/{id}/contracts/{contract_id}', [PengajarController::class, 'deleteContract'])->name('contracts.destroy');
        });

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

        Route::prefix('keuangan')->group(function () {
            Route::get('/', [KeuanganController::class, 'index'])->name('keuangan.index');

            Route::prefix('infaq')->group(function () {
                Route::get('/tahunan', [InfaqController::class, 'indexInfaqTahunan'])->name('keuangan.infaq.tahunan.index');
                Route::post('/tahunan', [InfaqController::class, 'storeInfaqTahunan'])->name('keuangan.infaq.tahunan.store');
                Route::get('/tahunan/{id}/edit', [InfaqController::class, 'editInfaqTahunan'])->name('keuangan.infaq.tahunan.edit');
                Route::put('/tahunan/{id}', [InfaqController::class, 'updateInfaqTahunan'])->name('keuangan.infaq.tahunan.update');
                Route::delete('/tahunan/{id}', [InfaqController::class, 'destroyInfaqTahunan'])->name('keuangan.infaq.tahunan.destroy');
                Route::get('/tahunan/{infaqTahunanId}/santri', [InfaqController::class, 'showInfaqSantri'])->name('keuangan.infaq.santri.index');
                Route::post('/tahunan/{infaqTahunanId}/santri', [InfaqController::class, 'storeInfaqSantri'])->name('keuangan.infaq.santri.store');
                Route::get('/tahunan/{infaqTahunanId}/santri/{id}/edit', [InfaqController::class, 'editInfaqSantri'])->name('keuangan.infaq.santri.edit');
                Route::put('/tahunan/{infaqTahunanId}/santri/{id}', [InfaqController::class, 'updateInfaqSantri'])->name('keuangan.infaq.santri.update');
                Route::delete('/tahunan/{infaqTahunanId}/santri/{id}', [InfaqController::class, 'destroyInfaqSantri'])->name('keuangan.infaq.santri.destroy');
            });

            Route::prefix('administrasi-bulanan')->group(function () {
                Route::get('/', [AdministrasiBulananController::class, 'index'])->name('keuangan.administrasi-bulanan.index');
                Route::post('/', [AdministrasiBulananController::class, 'store'])->name('keuangan.administrasi-bulanan.store');
                Route::get('/{id}/edit', [AdministrasiBulananController::class, 'edit'])->name('keuangan.administrasi-bulanan.edit');
                Route::put('/{id}', [AdministrasiBulananController::class, 'update'])->name('keuangan.administrasi-bulanan.update');
                Route::delete('/{id}', [AdministrasiBulananController::class, 'destroy'])->name('keuangan.administrasi-bulanan.destroy');
                Route::get('/{administrasiBulananId}/pengeluaran', [AdministrasiBulananController::class, 'indexPengeluaranBulanan'])->name('keuangan.administrasi-bulanan.pengeluaran.index');
                Route::post('/{administrasiBulananId}/pengeluaran', [AdministrasiBulananController::class, 'storePengeluaranBulanan'])->name('keuangan.administrasi-bulanan.pengeluaran.store');
                Route::get('/{administrasiBulananId}/pengeluaran/{id}/edit', [AdministrasiBulananController::class, 'editPengeluaranBulanan'])->name('keuangan.administrasi-bulanan.pengeluaran.edit');
                Route::put('/{administrasiBulananId}/pengeluaran/{id}', [AdministrasiBulananController::class, 'updatePengeluaranBulanan'])->name('keuangan.administrasi-bulanan.pengeluaran.update');
                Route::delete('/{administrasiBulananId}/pengeluaran/{id}', [AdministrasiBulananController::class, 'destroyPengeluaranBulanan'])->name('keuangan.administrasi-bulanan.pengeluaran.destroy');
                Route::get('/{id}/download-pdf', [AdministrasiBulananController::class, 'downloadPdf'])->name('keuangan.administrasi-bulanan.download-pdf');
            });

            Route::prefix('buku-kas')->group(function () {
                Route::get('/', [BukuKasController::class, 'index'])->name('keuangan.buku-kas.index');
                Route::post('/', [BukuKasController::class, 'store'])->name('keuangan.buku-kas.store');
                Route::get('/{id}/edit', [BukuKasController::class, 'edit'])->name('keuangan.buku-kas.edit');
                Route::put('/{id}', [BukuKasController::class, 'update'])->name('keuangan.buku-kas.update');
                Route::delete('/{id}', [BukuKasController::class, 'destroy'])->name('keuangan.buku-kas.destroy');
                Route::get('/{bukuKasId}/transaksi', [BukuKasController::class, 'indexTransaksiKas'])->name('keuangan.buku-kas.transaksi.index');
                Route::post('/{bukuKasId}/transaksi', [BukuKasController::class, 'storeTransaksiKas'])->name('keuangan.buku-kas.transaksi.store');
                Route::get('/{bukuKasId}/transaksi/{id}/edit', [BukuKasController::class, 'editTransaksiKas'])->name('keuangan.buku-kas.transaksi.edit');
                Route::put('/{bukuKasId}/transaksi/{id}', [BukuKasController::class, 'updateTransaksiKas'])->name('keuangan.buku-kas.transaksi.update');
                Route::delete('/{bukuKasId}/transaksi/{id}', [BukuKasController::class, 'destroyTransaksiKas'])->name('keuangan.buku-kas.transaksi.destroy');
            });

            Route::prefix('bank-accounts')->group(function () {
                Route::get('/', [BankAccountController::class, 'index'])->name('keuangan.bank-accounts.index');
                Route::post('/', [BankAccountController::class, 'store'])->name('keuangan.bank-accounts.store');
                Route::get('/{id}/edit', [BankAccountController::class, 'edit'])->name('keuangan.bank-accounts.edit');
                Route::put('/{id}', [BankAccountController::class, 'update'])->name('keuangan.bank-accounts.update');
                Route::delete('/{id}', [BankAccountController::class, 'destroy'])->name('keuangan.bank-accounts.destroy');
            });
        });
    });

    // Pengajar Route
    Route::middleware([CheckRole::class . ':pengajar'])->group(function () {
        // Dashboard
        Route::get('/dashboard-pengajar', [DashboardController::class, 'indexPengajar'])->name('dashboard-pengajar');

        //Presensi
        Route::get('/presence', [PresenceController::class, 'index'])->name('presence.index');
        Route::post('/presence', [PresenceController::class, 'store'])->name('pengajar.presence.store');
        Route::get('/presence-pengajar/edit/{id}', [PresenceController::class, 'edit'])->name('pengajar.presence.edit');
        Route::put('/presence/{id}', [PresenceController::class, 'update'])->name('pengajar.presence.update');
        Route::delete('/pengajar/presence/{id}', [PresenceController::class, 'destroyOwn'])->name('pengajar.presence.destroy');

        // Kehadiran
       Route::get('/attendance-pengajar', [PresenceController::class, 'attendancePengajar'])->name('attendance-pengajar');

        // Profile
        Route::prefix('profile-pengajar')->group(function () {
            Route::get('/', [ProfileController::class, 'showProfile'])->name('profile.pengajar.index');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.pengajar.update');
            Route::post('/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.pengajar.uploadPhoto');
            Route::delete('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.pengajar.deletePhoto');
            Route::post('/upload-signature', [ProfileController::class, 'uploadSignature'])->name('profile.pengajar.uploadSignature');
            Route::delete('/delete-signature', [ProfileController::class, 'deleteSignature'])->name('profile.pengajar.deleteSignature');
        });

        Route::prefix('pengajar/santri')->group(function () {
            Route::get('/data-santri', [SantriController::class, 'index'])->name('pengajar.santri.index');
            Route::get('/{id}', [SantriController::class, 'show'])->name('pengajar.santri.show');
            Route::get('/{id}/download-akta', [SantriController::class, 'downloadAkta'])->name('pengajar.download.akta');
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
        });
    });
});
