<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

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

    Route::get('/sampah', [DriveController::class, 'sampah'])->name('sampah');
    Route::post('/sampah/folder/{id}/restore', [DriveController::class, 'restoreFolder']);
    Route::post('/sampah/file/{id}/restore', [DriveController::class, 'restoreFile']);
    Route::post('/sampah/folder/{id}/force-delete', [DriveController::class, 'forceDeleteFolder']);
    Route::post('/sampah/file/{id}/force-delete', [DriveController::class, 'forceDeleteFile']);
});

