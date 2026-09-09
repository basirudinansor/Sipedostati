<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PilihanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/ganti-password', [ChangePasswordController::class, 'show'])->name('password.change.form');
    Route::post('/ganti-password', [ChangePasswordController::class, 'update'])->name('password.change.update');

    Route::middleware('force.password')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/pilih-dosen', [PilihanController::class, 'index'])->name('pilih-dosen');
        Route::post('/pilih-dosen', [PilihanController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('pilih-dosen.store');
        Route::delete('/pilih-dosen', [PilihanController::class, 'destroy'])
            ->middleware('throttle:6,1')
            ->name('pilih-dosen.destroy');

        Route::get('/api/server-time', [PilihanController::class, 'serverTime']);
        Route::get('/api/pemilihan-status', [PilihanController::class, 'status']);

        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
            Route::post('/dosen', [DosenController::class, 'store'])->name('dosen.store');
            Route::put('/dosen/{dosen}', [DosenController::class, 'update'])->name('dosen.update');
            Route::delete('/dosen/{dosen}', [DosenController::class, 'destroy'])->name('dosen.destroy');

            Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
            Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
            Route::post('/jadwal/{jadwal}/tutup', [JadwalController::class, 'tutup'])->name('jadwal.tutup');

            Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
            Route::get('/mahasiswa/export', [MahasiswaController::class, 'exportExcel'])->name('mahasiswa.export');
            Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
            Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
            Route::post('/mahasiswa/import', [MahasiswaController::class, 'importExcel'])->name('mahasiswa.import');
            Route::post('/mahasiswa/{mahasiswa}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password');
            Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
        });
    });
});
