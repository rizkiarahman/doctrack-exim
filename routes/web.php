<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
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

    // Document Modification Actions (Admin Only)
    // NOTE: Defined BEFORE the wildcard '{document}' route to avoid collisions
    Route::middleware('admin')->group(function () {
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // Document Wildcard Show Route (Admin & User) - Defined AFTER create to prevent collision
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
});
