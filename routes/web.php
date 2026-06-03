<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\FormationController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Enseignant\EnseignantController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DemandeController;



// ===== ROUTES PUBLIQUES =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
Route::get('/formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/demande-service', [ContactController::class, 'demandeForm'])->name('demande.form');
Route::post('/demande-service', [ContactController::class, 'demandeStore'])->name('demande.store');


// ===== ROUTES AUTH (Breeze) =====
require __DIR__.'/auth.php';

// ===== CLIENT =====
Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [ClientController::class, 'demandes'])->name('demandes');
        Route::get('/formations', [ClientController::class, 'formations'])->name('formations');
        Route::get('/formations/{formation}/ressources', [ClientController::class, 'ressources'])->name('ressources');
        Route::get('/ressources/{ressource}/pdf', [ClientController::class, 'voirPdf'])->name('pdf');
        Route::get('/notifications', [ClientController::class, 'notifications'])->name('notifications');
        Route::get('/profil', [ClientController::class, 'profil'])->name('profil');
        Route::post('/profil', [ClientController::class, 'profilUpdate'])->name('profil.update');
        Route::post('/profil/password', [ClientController::class, 'passwordUpdate'])->name('password.update');
    });

// ===== ENSEIGNANT =====
Route::middleware(['auth', 'verified', 'role:enseignant'])
    ->prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {
        Route::get('/dashboard', [EnseignantController::class, 'dashboard'])->name('dashboard');

        // Ressources
        Route::get('/ressources', [EnseignantController::class, 'ressourcesIndex'])->name('ressources.index');
        Route::get('/ressources/ajouter', [EnseignantController::class, 'ressourcesCreate'])->name('ressources.create');
        Route::post('/ressources', [EnseignantController::class, 'ressourcesStore'])->name('ressources.store');
        Route::get('/ressources/{ressource}/modifier', [EnseignantController::class, 'ressourcesEdit'])->name('ressources.edit');
        Route::put('/ressources/{ressource}', [EnseignantController::class, 'ressourcesUpdate'])->name('ressources.update');
        Route::delete('/ressources/{ressource}', [EnseignantController::class, 'ressourcesDestroy'])->name('ressources.destroy');

        // Notifications
        Route::get('/notifications', [EnseignantController::class, 'notificationsForm'])->name('notifications.form');
        Route::post('/notifications', [EnseignantController::class, 'notificationsEnvoyer'])->name('notifications.envoyer');

        // AJAX
        Route::get('/formations/{formation}/niveaux', [EnseignantController::class, 'getNiveaux'])->name('formations.niveaux');
    });

// ===== ADMIN =====
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Utilisateurs
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/toggle-statut', [UserController::class, 'toggleStatut'])->name('users.toggle-statut');
        Route::post('/users/inscriptions/{inscription}/valider', [UserController::class, 'validerInscription'])->name('users.inscription.valider');
        Route::post('/users/inscriptions/{inscription}/rejeter', [UserController::class, 'rejeterInscription'])->name('users.inscription.rejeter');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Enseignants
        Route::get('/enseignants', [UserController::class, 'enseignants'])->name('enseignants.index');
        Route::post('/enseignants', [UserController::class, 'storeEnseignant'])->name('enseignants.store');
        Route::put('/enseignants/{user}', [UserController::class, 'updateEnseignant'])->name('enseignants.update');

        
        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/creer', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/modifier', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::post('/services/{service}/toggle', [ServiceController::class, 'toggleActif'])->name('services.toggle');

        // Demandes
        Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
        Route::get('/demandes/{demande}', [DemandeController::class, 'show'])->name('demandes.show');
        Route::post('/demandes/{demande}/statut', [DemandeController::class, 'changerStatut'])->name('demandes.statut');

         // Placeholders pour les jours suivants
        Route::get('/formations', fn() => view('admin.formations.index'))->name('formations.index');
        Route::get('/notifications', fn() => view('admin.notifications'))->name('notifications.form');
        Route::get('/emails', fn() => view('admin.emails'))->name('emails.form');
    });        

// ===== PROFIL =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});