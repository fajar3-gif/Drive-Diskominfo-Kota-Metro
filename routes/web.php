<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileTrashController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\BulkActionController;

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
    Route::get('/lupa-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendResetCode'])->name('password.email');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');

    // --- ROUTE LOGIN GOOGLE VIA FIREBASE ---
    Route::post('/auth/firebase', [SocialAuthController::class, 'handleFirebaseLogin']);
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // --- DASHBOARD & FAVORIT ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/terbaru', [DashboardController::class, 'terbaru'])->name('terbaru');
    Route::get('/favorit', [DashboardController::class, 'favorit'])->name('favorit');
    
    // --- FOLDER ACTIONS ---
    Route::post('/folder/create', [FolderController::class, 'storeFolder']);
    Route::post('/folder/{id}/update', [FolderController::class, 'updateFolder']);
    Route::get('/folder/show/{id}', [FolderController::class, 'showFolder']);
    Route::post('/folder/{id}/delete', [FolderController::class, 'deleteFolder']);
    Route::post('/folder/{id}/favorite', [FolderController::class, 'toggleFavoriteFolder']);
    
    // --- FILE ACTIONS ---
    Route::post('/file/upload', [FileController::class, 'storeFile']);
    Route::get('/files/{id}', [FileController::class, 'showFile']);
    Route::get('/files/{id}/download', [FileController::class, 'downloadFile']);
    Route::post('/file/{id}/delete', [FileTrashController::class, 'deleteFile']);
    Route::post('/file/{id}/favorite', [FileController::class, 'toggleFavoriteFile']);

    // --- SAMPAH ---
    Route::get('/sampah', [TrashController::class, 'sampah'])->name('sampah');
    Route::post('/sampah/folder/{id}/restore', [FolderController::class, 'restoreFolder']);
    Route::post('/sampah/file/{id}/restore', [FileTrashController::class, 'restoreFile']);
    Route::post('/sampah/folder/{id}/force-delete', [FolderController::class, 'forceDeleteFolder']);
    Route::post('/sampah/file/{id}/force-delete', [FileTrashController::class, 'forceDeleteFile']);

    // --- BULK ACTIONS ---
    Route::post('/bulk/trash', [BulkActionController::class, 'bulkTrash']);
    Route::post('/bulk/restore', [BulkActionController::class, 'bulkRestore']);
    Route::post('/bulk/force-delete', [BulkActionController::class, 'bulkForceDelete']);
    Route::post('/bulk/favorite', [BulkActionController::class, 'bulkFavorite']);
    Route::post('/bulk/download', [BulkActionController::class, 'bulkDownload']);
});

