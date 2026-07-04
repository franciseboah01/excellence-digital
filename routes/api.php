<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\Client\{
    ClientDashboardController,
    ClientFormationController,
    ClientQcmController,
    ClientCertificatController,
    ClientDemandeController,
    ClientPaiementController,
    ClientTemoignageController
};
use App\Http\Controllers\Api\Enseignant\{
    EnseignantDashboardController,
    EnseignantRessourceController,
    EnseignantQcmController
};
use App\Http\Controllers\Api\Admin\{
    AdminDashboardController,
    AdminUserController,
    AdminServiceController,
    AdminFormationController,
    AdminArticleController,
    AdminFaqController,
    AdminDemandeController,
    AdminPaiementController,
    AdminCertificatController,
    AdminCategorieController,
    AdminModuleController,
    AdminNotificationController,
    AdminQcmController
};

/*
|--------------------------------------------------------------------------
| API Routes - Excellence Digital Center v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ========== AUTH ==========
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // ========== ROUTES PUBLIQUES ==========
    Route::get('/home', [PublicController::class, 'home']);
    Route::get('/services', [PublicController::class, 'services']);
    Route::get('/services/{service}', [PublicController::class, 'serviceShow']);
    Route::get('/services/categorie/{categorie}', [PublicController::class, 'servicesByCategorie']);
    Route::get('/formations', [PublicController::class, 'formations']);
    Route::get('/formations/{formation}', [PublicController::class, 'formationShow']);
    Route::get('/formations/module/{slug}', [PublicController::class, 'formationsByModule']);
    Route::get('/blog', [PublicController::class, 'blog']);
    Route::get('/blog/categories', [PublicController::class, 'blogCategories']);
    Route::get('/article/{article:slug}', [PublicController::class, 'articleShow']);
    Route::get('/faq', [PublicController::class, 'faq']);
    Route::get('/about', [PublicController::class, 'about']);
    Route::post('/contact', [PublicController::class, 'contact']);
    Route::post('/demande-service', [PublicController::class, 'demandeService']);
    Route::get('/search', [PublicController::class, 'search']);
    Route::get('/search/autocomplete', [PublicController::class, 'autocomplete']);

    // ========== ROUTES PROTÉGÉES ==========
    Route::middleware('auth:sanctum')->group(function () {

        // Profil (tous rôles)
        Route::get('/user', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Notifications (tous rôles)
        Route::get('/notifications', [ProfileController::class, 'notifications']);
        Route::post('/notifications/mark-read', [ProfileController::class, 'markNotificationsRead']);
        Route::get('/notifications/unread-count', [ProfileController::class, 'unreadCount']);

        // Messagerie (tous rôles)
        Route::get('/messages', [MessageController::class, 'index']);
        Route::get('/messages/{user}', [MessageController::class, 'conversation']);
        Route::post('/messages', [MessageController::class, 'send']);
        Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
        Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

        // ========== CLIENT ==========
        Route::middleware('role:client')->prefix('client')->group(function () {
            Route::get('/dashboard', [ClientDashboardController::class, 'index']);
            
            // Formations
            Route::get('/formations', [ClientFormationController::class, 'index']);
            Route::get('/formations/disponibles', [ClientFormationController::class, 'disponibles']);
            Route::get('/formations/{id}', [ClientFormationController::class, 'show']);
            Route::post('/formations/{formation}/inscrire', [ClientFormationController::class, 'inscrire']);
            Route::get('/formations/{formation}/ressources', [ClientFormationController::class, 'ressources']);
            Route::get('/ressources/{ressource}/pdf', [ClientFormationController::class, 'viewPdf']);
            
            // QCMs
            Route::get('/qcms', [ClientQcmController::class, 'index']);
            Route::get('/qcms/{qcm}/demarrer', [ClientQcmController::class, 'demarrer']);
            Route::post('/qcms/{qcm}/soumettre', [ClientQcmController::class, 'soumettre']);
            Route::get('/sessions/{session}/resultat', [ClientQcmController::class, 'resultat']);
            
            // Certificats
            Route::get('/certificats', [ClientCertificatController::class, 'index']);
            Route::get('/certificats/{certificat}/telecharger/{format?}', [ClientCertificatController::class, 'telecharger']);
            
            // Demandes
            Route::get('/demandes', [ClientDemandeController::class, 'index']);
            Route::post('/demandes', [ClientDemandeController::class, 'store']);
            
            // Paiements
            Route::get('/paiements', [ClientPaiementController::class, 'index']);
            Route::post('/paiements/process', [ClientPaiementController::class, 'process']);
            
            // Témoignages
            Route::get('/temoignages', [ClientTemoignageController::class, 'index']);
            Route::post('/temoignages', [ClientTemoignageController::class, 'store']);
        });

        // ========== ENSEIGNANT ==========
        Route::middleware('role:enseignant')->prefix('enseignant')->group(function () {
            Route::get('/dashboard', [EnseignantDashboardController::class, 'index']);
            
            // Ressources
            Route::get('/ressources', [EnseignantRessourceController::class, 'index']);
            Route::post('/ressources', [EnseignantRessourceController::class, 'store']);
            Route::get('/ressources/{ressource}', [EnseignantRessourceController::class, 'show']);
            Route::put('/ressources/{ressource}', [EnseignantRessourceController::class, 'update']);
            Route::delete('/ressources/{ressource}', [EnseignantRessourceController::class, 'destroy']);
            
            // QCMs
            Route::get('/qcms', [EnseignantQcmController::class, 'index']);
            Route::post('/qcms', [EnseignantQcmController::class, 'store']);
            Route::get('/qcms/{qcm}', [EnseignantQcmController::class, 'show']);
            Route::put('/qcms/{qcm}', [EnseignantQcmController::class, 'update']);
            Route::delete('/qcms/{qcm}', [EnseignantQcmController::class, 'destroy']);
            Route::post('/qcms/{qcm}/questions', [EnseignantQcmController::class, 'storeQuestion']);
            Route::put('/questions/{question}', [EnseignantQcmController::class, 'updateQuestion']);
            Route::delete('/questions/{question}', [EnseignantQcmController::class, 'destroyQuestion']);
            Route::post('/qcms/{qcm}/toggle', [EnseignantQcmController::class, 'toggleActif']);
            Route::get('/qcms/{qcm}/resultats', [EnseignantQcmController::class, 'resultats']);
        });

        // ========== ADMIN ==========
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);
            
            // Users
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::post('/users', [AdminUserController::class, 'store']);
            Route::get('/users/{user}', [AdminUserController::class, 'show']);
            Route::put('/users/{user}', [AdminUserController::class, 'update']);
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
            Route::post('/users/{user}/toggle-statut', [AdminUserController::class, 'toggleStatut']);
            
            // Enseignants
            Route::get('/enseignants', [AdminUserController::class, 'enseignants']);
            Route::post('/enseignants', [AdminUserController::class, 'storeEnseignant']);
            Route::put('/enseignants/{user}', [AdminUserController::class, 'updateEnseignant']);
            
            // Services
            Route::get('/services', [AdminServiceController::class, 'index']);
            Route::post('/services', [AdminServiceController::class, 'store']);
            Route::get('/services/{service}', [AdminServiceController::class, 'show']);
            Route::put('/services/{service}', [AdminServiceController::class, 'update']);
            Route::delete('/services/{service}', [AdminServiceController::class, 'destroy']);
            Route::post('/services/{service}/toggle', [AdminServiceController::class, 'toggleActif']);
            
            // Catégories
            Route::get('/categories', [AdminCategorieController::class, 'index']);
            Route::post('/categories', [AdminCategorieController::class, 'store']);
            Route::put('/categories/{categorie}', [AdminCategorieController::class, 'update']);
            Route::delete('/categories/{categorie}', [AdminCategorieController::class, 'destroy']);
            
            // Modules
            Route::get('/modules', [AdminModuleController::class, 'index']);
            Route::post('/modules', [AdminModuleController::class, 'store']);
            Route::put('/modules/{module}', [AdminModuleController::class, 'update']);
            Route::delete('/modules/{module}', [AdminModuleController::class, 'destroy']);
            
            // Formations
            Route::get('/formations', [AdminFormationController::class, 'index']);
            Route::post('/formations', [AdminFormationController::class, 'store']);
            Route::get('/formations/{formation}', [AdminFormationController::class, 'show']);
            Route::put('/formations/{formation}', [AdminFormationController::class, 'update']);
            Route::delete('/formations/{formation}', [AdminFormationController::class, 'destroy']);
            Route::post('/formations/{formation}/niveaux', [AdminFormationController::class, 'storeNiveau']);
            Route::delete('/niveaux/{niveau}', [AdminFormationController::class, 'destroyNiveau']);
            Route::post('/formations/{formation}/assigner-enseignant', [AdminFormationController::class, 'assignerEnseignant']);
            Route::delete('/formations/{formation}/retirer-enseignant/{enseignant}', [AdminFormationController::class, 'retirerEnseignant']);
            
            // Inscriptions
            Route::post('/inscriptions/{inscription}/valider', [AdminFormationController::class, 'validerInscription']);
            Route::post('/inscriptions/{inscription}/rejeter', [AdminFormationController::class, 'rejeterInscription']);
            
            // Demandes
            Route::get('/demandes', [AdminDemandeController::class, 'index']);
            Route::get('/demandes/{demande}', [AdminDemandeController::class, 'show']);
            Route::post('/demandes/{demande}/statut', [AdminDemandeController::class, 'changerStatut']);
            
            // Paiements
            Route::get('/paiements', [AdminPaiementController::class, 'index']);
            Route::post('/paiements', [AdminPaiementController::class, 'store']);
            Route::get('/paiements/{paiement}', [AdminPaiementController::class, 'show']);
            Route::put('/paiements/{paiement}', [AdminPaiementController::class, 'update']);
            
            // Certificats
            Route::get('/certificats', [AdminCertificatController::class, 'index']);
            Route::post('/certificats/{certificat}/duplicata', [AdminCertificatController::class, 'duplicata']);
            Route::get('/certificats/{certificat}/telecharger', [AdminCertificatController::class, 'telecharger']);
            Route::get('/duplicatas/demandes', [AdminCertificatController::class, 'demandesDuplicata']);
            Route::patch('/duplicatas/{demande}/valider', [AdminCertificatController::class, 'validerDuplicata']);
            Route::patch('/duplicatas/{demande}/rejeter', [AdminCertificatController::class, 'rejeterDuplicata']);
            
            // Blog/Articles
            Route::get('/articles', [AdminArticleController::class, 'index']);
            Route::post('/articles', [AdminArticleController::class, 'store']);
            Route::get('/articles/{article}', [AdminArticleController::class, 'show']);
            Route::put('/articles/{article}', [AdminArticleController::class, 'update']);
            Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy']);
            
            // FAQ
            Route::get('/faqs', [AdminFaqController::class, 'index']);
            Route::post('/faqs', [AdminFaqController::class, 'store']);
            Route::put('/faqs/{faq}', [AdminFaqController::class, 'update']);
            Route::post('/faqs/{faq}/toggle', [AdminFaqController::class, 'toggleActif']);
            Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy']);
            
            // QCMs (supervision)
            Route::get('/qcms', [AdminQcmController::class, 'index']);
            Route::get('/qcms/{qcm}', [AdminQcmController::class, 'show']);
            Route::post('/qcms/{qcm}/toggle', [AdminQcmController::class, 'toggleActif']);
            Route::delete('/qcms/{qcm}', [AdminQcmController::class, 'destroy']);
            
            // Notifications
            Route::post('/notifications/send', [AdminNotificationController::class, 'send']);
            Route::post('/notifications/send-all', [AdminNotificationController::class, 'sendAll']);
        });
    });
});