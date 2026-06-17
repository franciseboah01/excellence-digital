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
use App\Http\Controllers\Admin\PaiementController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\FichierController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Enseignant\QcmController as EnseignantQcmController;
use App\Http\Controllers\Client\QcmController as ClientQcmController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\Admin\QcmController as AdminQcmController;
use App\Http\Controllers\Admin\CategorieController;
use App\Http\Controllers\Admin\ModuleController;



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
Route::get('/recherche', [SearchController::class, 'search'])->name('recherche');
Route::get('/recherche/autocomplete', [SearchController::class, 'autocomplete'])->name('recherche.autocomplete');

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

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{article:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/faq', [BlogController::class, 'faq'])->name('faq');


// ===== ROUTES AUTH (Breeze) =====
require __DIR__.'/auth.php';


// ===== MESSAGERIE =====
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages', [MessageController::class, 'envoyer'])->name('messages.envoyer');
    Route::get('/messages/non-lus/count', [MessageController::class, 'compterNonLus'])->name('messages.non-lus');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/certificats/{certificat}/telecharger', [CertificatController::class, 'telecharger'])->name('certificats.telecharger');
    Route::get('/certificats/{certificat}/apercu', [CertificatController::class, 'apercu'])->name('certificats.apercu');


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
        Route::get('/qcms', [ClientQcmController::class, 'index'])->name('qcms.index');
        Route::get('/qcms/{qcm}/demarrer', [ClientQcmController::class, 'demarrer'])->name('qcms.demarrer');
        Route::post('/qcms/{qcm}/soumettre', [ClientQcmController::class, 'soumettre'])->name('qcms.soumettre');
        Route::get('/sessions/{session}/resultat', [ClientQcmController::class, 'resultat'])->name('qcms.resultat');
        Route::get('/nouvelle-demande', [ClientController::class, 'demandeForm'])->name('demande.form');
        Route::post('/nouvelle-demande', [ClientController::class, 'demandeStore'])->name('demande.store');
        Route::get('/paiements', [ClientController::class, 'paiements'])->name('paiements');
        Route::get('/paiement/{type}/{id}', [ClientController::class, 'paiementForm'])->name('paiement.form');
        Route::post('/paiement/process', [ClientController::class, 'paiementProcess'])->name('paiement.process');
        Route::get('/formations/disponibles', [ClientController::class, 'formationsDisponibles'])->name('formations.disponibles');
        Route::post('/formations/{formation}/inscrire', [ClientController::class, 'inscrireFormation'])->name('formations.inscrire');
        Route::post('/certificats/{certificat}/demande-duplicata', [CertificatController::class, 'demandeDuplicata'])->name('certificats.demande-duplicata');
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

        // QCM
        Route::get('/qcms', [EnseignantQcmController::class, 'index'])->name('qcms.index');
        Route::get('/qcms/creer', [EnseignantQcmController::class, 'create'])->name('qcms.create');
        Route::post('/qcms', [EnseignantQcmController::class, 'store'])->name('qcms.store');
        Route::get('/qcms/{qcm}/questions', [EnseignantQcmController::class, 'questions'])->name('qcms.questions');
        Route::post('/qcms/{qcm}/questions', [EnseignantQcmController::class, 'storeQuestion'])->name('qcms.questions.store');
        Route::delete('/qcms/{qcm}/questions/{question}', [EnseignantQcmController::class, 'destroyQuestion'])->name('qcms.questions.destroy');
        Route::post('/qcms/{qcm}/toggle', [EnseignantQcmController::class, 'toggleActif'])->name('qcms.toggle');
        Route::delete('/qcms/{qcm}', [EnseignantQcmController::class, 'destroy'])->name('qcms.destroy');
        Route::get('/qcms/{qcm}/resultats', [EnseignantQcmController::class, 'resultats'])->name('qcms.resultats');

        Route::get('/qcms/question/{question}/data', [EnseignantQcmController::class, 'questionData'])->name('qcms.question.data');
        Route::put('/qcms/question/{question}', [EnseignantQcmController::class, 'updateQuestion'])->name('qcms.question.update');
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
    
            // Paiement
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/creer', [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
        Route::get('/paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');
        Route::put('/paiements/{paiement}', [PaiementController::class, 'update'])->name('paiements.update');
        Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');
        Route::get('/clients/{user}/paiements', [PaiementController::class, 'historique'])->name('paiements.historique');

        // FAQ
        Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
        Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
        Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
        Route::post('/faqs/{faq}/toggle', [FaqController::class, 'toggleActif'])->name('faqs.toggle');
        Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');

        // Articles/Blog
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/creer', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}/modifier', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

        //Configuration
        Route::get('/configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
        Route::put('/configurations', [ConfigurationController::class, 'update'])->name('configurations.update');

        //CQCM
        Route::get('/qcms', [AdminQcmController::class, 'index'])->name('qcms.index');
        Route::get('/qcms/{qcm}', [AdminQcmController::class, 'show'])->name('qcms.show');
        Route::post('/qcms/{qcm}/toggle', [AdminQcmController::class, 'toggleActif'])->name('qcms.toggle');
        Route::delete('/qcms/{qcm}', [AdminQcmController::class, 'destroy'])->name('qcms.destroy');
        
       // ===== CERTIFICATS & DUPLICATAS =====
        Route::get('/certificats', [CertificatController::class, 'index'])->name('certificats.index');  
        Route::post('/certificats/{certificat}/duplicata', [CertificatController::class, 'duplicata'])->name('certificats.duplicata');
        Route::get('/certificats/{certificat}/telecharger', [CertificatController::class, 'telecharger'])->name('client.certificats.telecharger');


        // Demandes de duplicata
        Route::get('/duplicatas/demandes', [CertificatController::class, 'demandesDuplicata'])->name('duplicatas.demandes');
        Route::patch('/duplicatas/{demande}/valider', [CertificatController::class, 'validerDuplicata'])->name('duplicatas.valider');
        Route::patch('/duplicatas/{demande}/rejeter', [CertificatController::class, 'rejeterDuplicata'])->name('duplicatas.rejeter');

        // Catégories de services
        Route::get('/categories', [CategorieController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategorieController::class, 'store'])->name('categories.store');
        Route::put('/categories/{categorie}', [CategorieController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy'])->name('categories.destroy');

        // Modules
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
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

// ===== FICHIERS SÉCURISÉS =====
// Route avec middleware URL signée + vérification accès
Route::get('/ressources/{ressource}/fichier', [FichierController::class, 'afficher'])
    ->name('ressources.fichier')
    ->middleware(['auth', 'acces_fichier']);

// Route AJAX pour générer une URL signée
Route::get('/ressources/{ressource}/url-signee', [FichierController::class, 'urlSignee'])
    ->name('ressources.url-signee')
    ->middleware('auth');