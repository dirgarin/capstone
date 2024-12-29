<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate']);
});

Route::resource('mahasiswa', App\Http\Controllers\MahasiswaController::class);
Route::resource('dosen', App\Http\Controllers\DosenController::class);

Route::middleware('auth')->group(function () {
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::resource('tim', App\Http\Controllers\TimController::class);
    Route::resource('topik_dosen', App\Http\Controllers\TopikDosenController::class);
    Route::resource('daftar_topik_dosen', App\Http\Controllers\DaftarTopikDosenController::class);
    Route::get('topik_mandiri/{topik_mandiri}/pilih_dosen', [App\Http\Controllers\TopikMandiriController::class, 'pilih_dosen'])->name('topik_mandiri.pilih_dosen');
    Route::post('topik_mandiri/{topik_mandiri}/pilih_dosen', [App\Http\Controllers\TopikMandiriController::class, 'proses_dosen'])->name('topik_mandiri.proses_dosen');
    Route::resource('topik_mandiri', App\Http\Controllers\TopikMandiriController::class);
    Route::resource('daftar_topik_mandiri', App\Http\Controllers\DaftarTopikMandiriController::class);
    Route::resource('daftar_topik', App\Http\Controllers\DaftarTopikController::class);
    Route::get('template/{template}/download', [App\Http\Controllers\TemplateController::class, 'download'])->name('template.download');
    Route::get('template/{template}/upload', [App\Http\Controllers\TemplateController::class, 'upload'])->name('template.upload');
    Route::post('template/{template}/upload', [App\Http\Controllers\TemplateController::class, 'proses_upload'])->name('template.proses_upload');
    Route::resource('template', App\Http\Controllers\TemplateController::class);
    Route::resource('mahasiswa_dokumen', App\Http\Controllers\MahasiswaDokumenController::class);
    Route::resource('penilaian_dosen', App\Http\Controllers\PenilaianDosenController::class);
    Route::resource('penilaian_tim', App\Http\Controllers\PenilaianTimController::class);
});
