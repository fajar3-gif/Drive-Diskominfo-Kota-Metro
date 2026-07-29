<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister']);
});
    // --- ROUTE LUPA PASSWORD & RESET PASSWORD ---
    Route::get('/lupa-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetCode'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // --- ROUTE LOGIN GOOGLE ---
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/folder/create', [DriveController::class, 'storeFolder']);
    Route::post('/file/upload', [\App\Http\Controllers\DriveController::class, 'storeFile']);
    Route::post('/folder/{id}/update', [\App\Http\Controllers\DriveController::class, 'updateFolder']);
    Route::post('/folder/{id}/delete', [\App\Http\Controllers\DriveController::class, 'deleteFolder']);
    Route::post('/file/{id}/delete', [\App\Http\Controllers\DriveController::class, 'deleteFile']);
    Route::get('/files/{id}', [DriveController::class, 'showFile']);
    Route::get('/files/{id}/download', [DriveController::class, 'downloadFile']);
    Route::get('/folder/show/{id}', [DriveController::class, 'showFolder']);
    Route::get('/dashboard', [DriveController::class, 'index'])->name('dashboard');
    Route::get('/terbaru', [DriveController::class, 'terbaru'])->name('terbaru');

    Route::get('/sampah', [DriveController::class, 'sampah'])->name('sampah');
    Route::post('/sampah/folder/{id}/restore', [DriveController::class, 'restoreFolder']);
    Route::post('/sampah/file/{id}/restore', [DriveController::class, 'restoreFile']);
    Route::post('/sampah/folder/{id}/force-delete', [DriveController::class, 'forceDeleteFolder']);
    Route::post('/sampah/file/{id}/force-delete', [DriveController::class, 'forceDeleteFile']);

    Route::get('/favorit', [DriveController::class, 'favorit'])->name('favorit');
    Route::post('/folder/{id}/favorite', [DriveController::class, 'toggleFavoriteFolder']);
    Route::post('/file/{id}/favorite', [DriveController::class, 'toggleFavoriteFile']);

    // --- BULK ACTIONS ---
    Route::post('/bulk/trash', [DriveController::class, 'bulkTrash']);
    Route::post('/bulk/restore', [DriveController::class, 'bulkRestore']);
    Route::post('/bulk/force-delete', [DriveController::class, 'bulkForceDelete']);
    Route::post('/bulk/favorite', [DriveController::class, 'bulkFavorite']);
    Route::post('/bulk/download', [DriveController::class, 'bulkDownload']);
});

