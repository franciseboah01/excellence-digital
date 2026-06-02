<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// ===== ROUTES PUBLIQUES =====
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ===== ROUTES AUTH (Breeze) =====
require __DIR__.'/auth.php';

// ===== ROUTES CLIENT =====
Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return view('client.dashboard');
    })->name('dashboard');
});

// ===== ROUTES ENSEIGNANT =====
Route::middleware(['auth', 'verified', 'role:enseignant'])->prefix('enseignant')->name('enseignant.')->group(function () {
    Route::get('/dashboard', function () {
        return view('enseignant.dashboard');
    })->name('dashboard');
});

// ===== ROUTES ADMIN =====
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// ===== PROFIL =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});