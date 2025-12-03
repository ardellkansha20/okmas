<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\JanjiTemuController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanPasienController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PeriksaController;
use App\Http\Controllers\AkunPasienController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\BeritaPasienController;
use App\Http\Controllers\KlasterController;
use App\Http\Controllers\ResepObatController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\NotifikasiController;

Route::get('/', function () {
    return view('home', ['title' => 'Home Page']);
})->name('home');

Route::get('/tentangKami', function () {
    return view('tentangKami', ['title' => 'Tentang Kami']);
});

Route::get('/layanan', function () {
    return view('layanan', ['title' => 'Layanan']);
});

Route::get('/dokter', function () {
    $dokters = \App\Models\Dokter::all();
    return view('dokter', ['title' => 'Dokter', 'dokters' => $dokters]);
});

Route::get('/berita', [BeritaPasienController::class, 'index'])->name('berita');
Route::get('/berita/{id}', [BeritaPasienController::class, 'show'])->name('beritaDetail');

Route::get('/detailKlaster/{jenis}', [KlasterController::class, 'detail'])->name('detailKlaster');

// ==================== AUTHENTICATION ROUTES ====================

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// ==================== AUTHENTICATED USER ROUTES ====================


Route::middleware('auth')->group(function () {
    Route::get('/laporan', [LaporanPasienController::class, 'index'])->name('laporan');
    Route::get('/laporan/{id}', [LaporanPasienController::class, 'show'])->name('laporan_detail');
    Route::get('/laporan/{id}/pdf', [LaporanPasienController::class, 'downloadPdf'])->name('laporan_pdf');
});

Route::prefix('admin')->group(function () {
    Route::resource('resep', \App\Http\Controllers\ResepObatController::class);
});

// REGISTER
Route::get('/register', [LoginController::class, 'registerForm'])->name('register.form');
Route::post('/register', [LoginController::class, 'register'])->name('register');

Route::get('/lupa-password', [LoginController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/lupa-password', [LoginController::class, 'sendResetToken'])->name('password.email');

// RESET PASSWORD
Route::get('/reset-password/{token}', [LoginController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

// ==================== AUTHENTICATED USER ROUTES ====================

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    
    // Laporan Pasien
    Route::get('/laporan', [LaporanPasienController::class, 'index'])->name('laporan');
    Route::get('/laporan/{id}', [LaporanPasienController::class, 'show'])->name('laporan_detail');
    Route::get('/laporan/{id}/pdf', [LaporanPasienController::class, 'downloadPdf'])->name('laporan_pdf');
    
    // Janji Temu
    Route::get('/janjiTemu', [JanjiTemuController::class, 'index'])->name('janjiTemu.index');
    Route::post('/janjiTemu', [JanjiTemuController::class, 'store'])->name('janjiTemu.store');
    Route::get('/janjiTemu/{id}/edit', [JanjiTemuController::class, 'edit'])->name('janjiTemu.edit');
    Route::put('/janjiTemu/{id}', [JanjiTemuController::class, 'update'])->name('janjiTemu.update');
    Route::delete('/janjiTemu/{id}', [JanjiTemuController::class, 'destroy'])->name('janjiTemu.destroy');
    
    // Kontak
    Route::resource('kontak', KontakController::class);
    
    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
});

// ==================== ADMIN ROUTES ====================

Route::middleware(['auth', 'admin'])->group(function () {
    
    // Admin Dashboard
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('adminDashboard.index');
    
    // Laporan Admin
    Route::prefix('laporanAdmin')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporanAdmin.index');
        Route::get('/create', [LaporanController::class, 'create'])->name('laporanAdmin.create');
        Route::post('/store', [LaporanController::class, 'store'])->name('laporanAdmin.store');
        Route::get('/{id_akun}', [LaporanController::class, 'show'])->name('laporanAdmin.show');
        Route::get('/edit/{id_akun}', [LaporanController::class, 'edit'])->name('laporanAdmin.edit');
        Route::put('/update/{id_akun}', [LaporanController::class, 'update'])->name('laporanAdmin.update');
        Route::delete('/delete/{id_akun}', [LaporanController::class, 'destroy'])->name('laporanAdmin.destroy');
    });
    
    // Berita Admin
    Route::prefix('updateBeritaAdmin')->group(function () {
        Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
        Route::get('/create', [BeritaController::class, 'create'])->name('berita.create');
        Route::post('/store', [BeritaController::class, 'store'])->name('berita.store');
        Route::get('/edit/{id}', [BeritaController::class, 'edit'])->name('berita.edit');
        Route::put('/update/{id}', [BeritaController::class, 'update'])->name('berita.update');
        Route::get('/show/{id}', [BeritaController::class, 'show'])->name('berita.show');
        Route::delete('/delete/{id}', [BeritaController::class, 'destroy'])->name('berita.destroy');
    });
    
    // Periksa Admin
    Route::prefix('daftarPeriksaAdmin')->group(function () {
        Route::get('/', [PeriksaController::class, 'index'])->name('periksa.index');
        Route::post('/toggle-status/{id}', [PeriksaController::class, 'toggleStatus'])->name('periksa.toggle');
        Route::get('/{id}/edit', [PeriksaController::class, 'edit'])->name('periksa.edit');
        Route::put('/{id}', [PeriksaController::class, 'update'])->name('periksa.update');
    });
    
    // Akun Pasien Admin
    Route::prefix('akunPasienAdmin')->group(function () {
        Route::get('/', [AkunPasienController::class, 'index'])->name('akunPasienAdmin.index');
        Route::get('/create', [AkunPasienController::class, 'create'])->name('akunPasienAdmin.create');
        Route::post('/store', [AkunPasienController::class, 'store'])->name('akunPasienAdmin.store');
        Route::get('/edit/{id}', [AkunPasienController::class, 'edit'])->name('akunPasienAdmin.edit');
        Route::put('/update/{id}', [AkunPasienController::class, 'update'])->name('akunPasienAdmin.update');
        Route::delete('/{id}', [AkunPasienController::class, 'destroy'])->name('akunPasienAdmin.destroy');
    });
    
    // Obat Admin
    Route::prefix('obatAdmin')->group(function () {
        Route::get('/', [ObatController::class, 'index'])->name('obatAdmin.index');
        Route::get('/create', [ObatController::class, 'create'])->name('obatAdmin.create');
        Route::post('/store', [ObatController::class, 'store'])->name('obatAdmin.store');
        Route::get('/edit/{id}', [ObatController::class, 'edit'])->name('obatAdmin.edit');
        Route::put('/update/{id}', [ObatController::class, 'update'])->name('obatAdmin.update');
        Route::delete('/{id}', [ObatController::class, 'destroy'])->name('obatAdmin.destroy');
    });
    
    // Dokter Admin
    Route::prefix('dokterAdmin')->group(function () {
        Route::get('/', [DokterController::class, 'index'])->name('dokterAdmin.index');
        Route::get('/create', [DokterController::class, 'create'])->name('dokterAdmin.create');
        Route::post('/store', [DokterController::class, 'store'])->name('dokterAdmin.store');
        Route::get('/edit/{id}', [DokterController::class, 'edit'])->name('dokterAdmin.edit');
        Route::put('/update/{id}', [DokterController::class, 'update'])->name('dokterAdmin.update');
        Route::delete('/{id}', [DokterController::class, 'destroy'])->name('dokterAdmin.destroy');
    });
    
    // Klaster Admin
    Route::prefix('klaster')->group(function () {
        Route::get('/', [KlasterController::class, 'index'])->name('klaster.index');
        Route::get('/create', [KlasterController::class, 'create'])->name('klaster.create');
        Route::post('/store', [KlasterController::class, 'store'])->name('klaster.store');
        Route::get('/edit/{id}', [KlasterController::class, 'edit'])->name('klaster.edit');
        Route::put('/update/{id}', [KlasterController::class, 'update'])->name('klaster.update');
        Route::delete('/{id}', [KlasterController::class, 'destroy'])->name('klaster.destroy');
    });
    
    // // Resep Admin
    // Route::prefix('resep')->group(function () {
    //     Route::get('/', [ResepObatController::class, 'index'])->name('resep.index');
    //     Route::get('/create', [ResepObatController::class, 'create'])->name('resep.create');
    //     Route::post('/store', [ResepObatController::class, 'store'])->name('resep.store');
    //     Route::get('/{id}', [ResepObatController::class, 'show'])->name('resep.show');
    //     Route::delete('/{id}', [ResepObatController::class, 'destroy'])->name('resep.destroy');
    // }); 
    
    // Periksa Laporan
    Route::get('/periksa/{id}/laporan', [PeriksaController::class, 'formLaporan'])->name('periksa.formLaporan');
    Route::post('/periksa/{id}/laporan', [PeriksaController::class, 'simpanLaporan'])->name('periksa.simpanLaporan');
    
});

// ==================== API ROUTES ====================

Route::get('/get-dokter-by-klaster/{klaster_id}', function ($klaster_id) {
    return \App\Models\Dokter::where('klaster_id', $klaster_id)->get();
});