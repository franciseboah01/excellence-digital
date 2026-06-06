<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Public\FormationController as PublicFormationController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Enseignant\EnseignantController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DemandeController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\FormationController as AdminFormationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Client\TemoignageController as ClientTemoignageController;
use App\Http\Controllers\Admin\TemoignageController as AdminTemoignageController;



// ===== ROUTES PUBLIQUES =====


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [PublicServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [PublicServiceController::class, 'show'])->name('services.show');
Route::get('/formations', [PublicFormationController::class, 'index'])->name('formations.index');
Route::get('/formations/{formation}', [PublicFormationController::class, 'show'])->name('formations.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/demande-service', [ContactController::class, 'demandeForm'])->name('demande.form');
Route::post('/demande-service', [ContactController::class, 'demandeStore'])->name('demande.store');

// ===== ROUTE DASHBOARD UNIFIÉE =====
Route::middleware('auth')->get('/dashboard', function () {

    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('enseignant')) {
        return redirect()->route('enseignant.dashboard');
    }

    if ($user->hasRole('client')) {
        return redirect()->route('client.dashboard');
    }

    Auth::logout();

    return redirect()->route('login')
        ->with('error', "Votre compte n'a pas de rôle assigné. Contactez l'administrateur.");

})->name('dashboard');

// ===== ROUTES AUTH (Breeze) =====
require __DIR__.'/auth.php';


// ===== MESSAGERIE =====
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages', [MessageController::class, 'envoyer'])->name('messages.envoyer');
    Route::get('/messages/non-lus/count', [MessageController::class, 'compterNonLus'])->name('messages.non-lus');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
});


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
        Route::get('/temoignages', [ClientTemoignageController::class, 'index'])->name('temoignages.index');
        Route::post('/temoignages', [ClientTemoignageController::class, 'store'])->name('temoignages.store');
        Route::delete('/temoignages/{temoignage}', [ClientTemoignageController::class, 'destroy'])->name('temoignages.destroy');

    
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

        // Mail
        Route::post('/emails', [EnseignantController::class, 'envoyerEmail'])->name('emails.envoyer');

        // Profil
        Route::get('/profil', [EnseignantController::class, 'profil'])->name('profil');
        Route::post('/profil', [EnseignantController::class, 'profilUpdate'])->name('profil.update');
        Route::post('/profil/password', [EnseignantController::class, 'passwordUpdate'])->name('password.update');

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
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/creer', [AdminServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/modifier', [AdminServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');
        Route::post('/services/{service}/toggle', [AdminServiceController::class, 'toggleActif'])->name('services.toggle');

        // Demandes
        Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
        Route::get('/demandes/{demande}', [DemandeController::class, 'show'])->name('demandes.show');
        Route::post('/demandes/{demande}/statut', [DemandeController::class, 'changerStatut'])->name('demandes.statut');

        // Formation
        Route::get('/formations', [AdminFormationController::class, 'index'])->name('formations.index');
        Route::get('/formations/creer', [AdminFormationController::class, 'create'])->name('formations.create');
        Route::post('/formations', [AdminFormationController::class, 'store'])->name('formations.store');
        Route::get('/formations/{formation}', [AdminFormationController::class, 'show'])->name('formations.show');
        Route::get('/formations/{formation}/modifier', [AdminFormationController::class, 'edit'])->name('formations.edit');
        Route::put('/formations/{formation}', [AdminFormationController::class, 'update'])->name('formations.update');
        Route::delete('/formations/{formation}', [AdminFormationController::class, 'destroy'])->name('formations.destroy');

        // Niveaux
        Route::post('/formations/{formation}/niveaux', [AdminFormationController::class, 'storeNiveau'])->name('formations.niveaux.store');
        Route::delete('/niveaux/{niveau}', [AdminFormationController::class, 'destroyNiveau'])->name('formations.niveaux.destroy');

        // Assignation enseignants ↔ formations
        Route::post('/formations/{formation}/assigner-enseignant', [AdminFormationController::class, 'assignerEnseignant'])->name('formations.assigner-enseignant');
        Route::delete('/formations/{formation}/retirer-enseignant/{enseignant}', [AdminFormationController::class, 'retirerEnseignant'])->name('formations.retirer-enseignant');

        // Inscriptions
        Route::post('/inscriptions/{inscription}/valider', [AdminFormationController::class, 'validerInscription'])->name('formations.inscription.valider');
        Route::post('/inscriptions/{inscription}/rejeter', [AdminFormationController::class, 'rejeterInscription'])->name('formations.inscription.rejeter');
        Route::delete('/inscriptions/{inscription}/desinscrire', [AdminFormationController::class, 'desinscrire'])->name('formations.inscription.desinscrire');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'form'])->name('notifications.form');
        Route::post('/notifications/cible', [NotificationController::class, 'envoyerCible'])->name('notifications.cible');
        Route::post('/notifications/groupe', [NotificationController::class, 'envoyerGroupe'])->name('notifications.groupe');
        Route::post('/notifications/tous', [NotificationController::class, 'envoyerTous'])->name('notifications.tous');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Emails
        Route::get('/emails', [EmailController::class, 'form'])->name('emails.form');
        Route::post('/emails', [EmailController::class, 'envoyer'])->name('emails.envoyer');

        // Temoignage
        Route::get('/temoignages', [AdminTemoignageController::class, 'index'])->name('temoignages.index');
        Route::post('/temoignages/{temoignage}/valider', [AdminTemoignageController::class, 'valider'])->name('temoignages.valider');
        Route::post('/temoignages/{temoignage}/refuser', [AdminTemoignageController::class, 'refuser'])->name('temoignages.refuser');
        Route::delete('/temoignages/{temoignage}', [AdminTemoignageController::class, 'destroy'])->name('temoignages.destroy');
    });        

// ===== PROFIL =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== NOTIFICATIONS AJAX =====
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/marquer-lu', [NotificationController::class, 'marquerLu'])->name('notifications.marquer-lu');
    Route::get('/notifications/non-lues', [NotificationController::class, 'compterNonLues'])->name('notifications.non-lues');
    Route::get('/notifications/dernieres', [NotificationController::class, 'dernieres'])->name('notifications.dernieres');
});