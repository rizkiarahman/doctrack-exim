<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SharedFileController;
use App\Http\Controllers\Auth\LoginController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Required Authentication)
Route::middleware('auth')->group(function () {
    
    // Dashboard (Admin & User)
    Route::get('/', [DocumentController::class, 'dashboard'])->name('dashboard');

    // Document Read-Only List (Admin & User)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // PDF Tool (Admin & User)
    Route::get('/pdf-editor', [PdfController::class, 'index'])->name('pdf.index');

    // Excel Tool (Admin & User)
    Route::view('/transform-excel', 'excel.transform')->name('excel.transform');

    // Shared Files / Drive (Admin & User)
    Route::get('/shared-files', [SharedFileController::class, 'index'])->name('shared-files.index');
    Route::post('/shared-files', [SharedFileController::class, 'store'])->name('shared-files.store');
    Route::get('/shared-files/{file}/download', [SharedFileController::class, 'download'])->name('shared-files.download');
    Route::delete('/shared-files/{file}', [SharedFileController::class, 'destroy'])->name('shared-files.destroy');

    // User Self Account Management (Email & Password)
    Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Admin-Only Routes
    Route::middleware('admin')->group(function () {
        // Document Modification Actions
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // User Management CRUD (Kelola Akun)
        Route::resource('users', UserController::class);
    });

    // Document Wildcard Show Route (Admin & User) - Defined AFTER create to prevent collision
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
});
