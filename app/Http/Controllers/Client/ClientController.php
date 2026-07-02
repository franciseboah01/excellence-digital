<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\DemandeDuplicata;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Ressource;
use App\Models\Service;
use App\Models\User;
use App\Http\Requests\UpdateProfilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * ============================================================
     * 1. DASHBOARD
     * ============================================================
     */
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'demandes_total'   => DemandeService::where('user_id', $user->id)->count(),
            'demandes_attente' => DemandeService::where('user_id', $user->id)->where('statut', 'en_attente')->count(),
            'demandes_cours'   => DemandeService::where('user_id', $user->id)->where('statut', 'en_cours')->count(),
            'demandes_termine' => DemandeService::where('user_id', $user->id)->where('statut', 'termine')->count(),
            'demandes_annule'  => DemandeService::where('user_id', $user->id)->where('statut', 'annule')->count(),
            'formations'       => InscriptionFormation::where('user_id', $user->id)->count(),
            'notifications'    => Notification::where('user_id', $user->id)->where('lu', false)->count(),
        ];

        $dernieres_demandes = DemandeService::with('service')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $mes_formations = InscriptionFormation::with('formation')
            ->where('user_id', $user->id)
            ->where('statut', 'valide')
            ->latest()
            ->take(3)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', compact(
            'stats',
            'dernieres_demandes',
            'mes_formations',
            'notifications'
        ));
    }

    /**
     * ============================================================
     * 2. DEMANDE DE SERVICE
     * ============================================================
     */
    public function demandeForm()
    {
        $services = Service::where('actif', true)->get();
        return view('client.nouvelle-demande', compact('services'));
    }

    public function demandeStore(Request $request)
    {
        $validated = $request->validate([
            'service_id'         => 'required|exists:services,id',
            'telephone_visiteur' => 'nullable|string|max:20',
            'message'            => 'nullable|string|max:2000',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // ✅ CHANGEMENT DE RÈGLE MÉTIER : la demande est désormais créée
        // immédiatement, qu'elle soit gratuite ou payante — comme le service
        // gratuit l'était déjà. Le paiement (partiel ou total) n'est plus
        // exigé à la création : il devient la condition que l'admin doit
        // vérifier avant de faire passer la demande à "en_cours"
        // (cf. Admin\DemandeController::changerStatut()). Le client peut
        // payer à tout moment depuis "Mes Paiements" une fois la demande créée.
        $demande = DemandeService::create([
            'user_id'            => auth()->id(),
            'service_id'         => $service->id,
            'nom_visiteur'       => auth()->user()->nom_complet,
            'email_visiteur'     => auth()->user()->email,
            'telephone_visiteur' => $validated['telephone_visiteur'] ?? auth()->user()->telephone,
            'message'            => $validated['message'] ?? null,
            'statut'             => 'en_attente',
        ]);

        if ($service->prix && $service->prix > 0) {
            return redirect()->route('client.demandes')
                ->with('success', '✅ Votre demande a été envoyée ! Un paiement (partiel ou total) sera nécessaire avant le démarrage du service — rendez-vous dans "Mes Paiements" pour régler.');
        }

        return redirect()->route('client.demandes')
            ->with('success', 'Votre demande a été envoyée avec succès !');
    }

    /**
     * ============================================================
     * 3. MES DEMANDES
     * ============================================================
     */
    public function demandes()
    {
        $demandes = DemandeService::with('service')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('client.demandes', compact('demandes'));
    }

    /**
     * ============================================================
     * 4. MES FORMATIONS
     * ============================================================
     */
    public function formations()
    {
        $inscriptions = InscriptionFormation::with(['formation.niveaux'])
            ->where('user_id', auth()->id())
            ->get();

        return view('client.formations', compact('inscriptions'));
    }

    /**
     * ============================================================
     * 5. RESSOURCES D'UNE FORMATION
     * ============================================================
     */
    public function ressources(Formation $formation)
    {
        $inscription = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $formation->id)
            ->where('statut', 'valide')
            ->firstOrFail();

        // Vérifier paiement pour formations payantes
        if ($formation->est_payante) {
            $aPaye = Paiement::where('user_id', auth()->id())
                ->where('formation_id', $formation->id)
                ->where('statut', 'complete')
                ->exists();

            if (!$aPaye) {
                return redirect()->route('client.paiement.form', ['type' => 'formation', 'id' => $formation->id])
                    ->with('error', 'Vous devez payer cette formation avant d\'accéder aux ressources.');
            }
        }

        // ✅ Progression par niveau : chaque niveau se voit attacher son statut
        // (validé / accessible) pour que la vue puisse afficher ou masquer ses
        // ressources en conséquence. Logique centralisée dans le modèle
        // NiveauFormation (estValidePar / estAccessiblePar) pour rester
        // cohérente avec Client\QcmController qui applique la même règle
        // pour le verrouillage des QCMs.
        $niveaux = $formation->niveaux()
            ->with(['ressources' => function ($q) {
                $q->where('actif', true)->orderBy('created_at', 'desc');
            }])
            ->orderBy('ordre')
            ->get();

        foreach ($niveaux as $niveau) {
            $niveau->est_valide = $niveau->estValidePar(auth()->id());
            $niveau->est_accessible = $niveau->estAccessiblePar(auth()->id());
        }

        $ressources_generales = Ressource::where('formation_id', $formation->id)
            ->whereNull('niveau_id')
            ->where('actif', true)
            ->get();

        return view('client.ressources', compact('formation', 'niveaux', 'ressources_generales'));
    }

    /**
     * ============================================================
     * 6. VISUALISER UN PDF
     * ============================================================
     */
    public function voirPdf(Ressource $ressource)
    {
        $inscription = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $ressource->formation_id)
            ->where('statut', 'valide')
            ->firstOrFail();

        // ✅ Empêche de contourner le verrouillage de niveau en accédant
        // directement à l'URL d'une ressource dont le niveau n'est pas
        // encore débloqué (le niveau précédent n'a pas été validé).
        if ($ressource->niveau_id) {
            $niveau = $ressource->niveau ?? \App\Models\NiveauFormation::find($ressource->niveau_id);

            if ($niveau && !$niveau->estAccessiblePar(auth()->id())) {
                abort(403, 'Vous devez d\'abord valider le niveau précédent pour accéder à cette ressource.');
            }
        }

        if (!$ressource->fichier_path) {
            return back()->with('error', 'Aucun fichier associé à cette ressource.');
        }

        if (!Storage::exists($ressource->fichier_path)) {
            return back()->with('error', 'Le fichier est introuvable sur le serveur.');
        }

        return response()->file(Storage::path($ressource->fichier_path), [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $ressource->titre . '.pdf"',
        ]);
    }

    /**
     * ============================================================
     * 7. NOTIFICATIONS
     * ============================================================
     */
    public function notifications()
    {
        Notification::where('user_id', auth()->id())
            ->where('lu', false)
            ->update(['lu' => true]);

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('client.notifications', compact('notifications'));
    }

    /**
     * ============================================================
     * 8. PROFIL
     * ============================================================
     */
    public function profil()
    {
        $formations_disponibles = Formation::where('statut', 'publie')->get();
        return view('client.profil', compact('formations_disponibles'));
    }

    public function profilUpdate(UpdateProfilRequest $request)
    {
        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'telephone' => $request->telephone,
            'avatar'    => $user->avatar,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }


    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * ============================================================
     * 9. MES PAIEMENTS
     * ============================================================
     */
    public function paiements()
    {
        $paiements = Paiement::where('user_id', auth()->id())
            ->with(['formation', 'service', 'certificat.formation'])
            ->latest()
            ->paginate(10);

        return view('client.paiements', compact('paiements'));
    }

    /**
     * ============================================================
     * 10. FORMULAIRE DE PAIEMENT
     * ============================================================
     */
    public function paiementForm(Request $request, $type, $id)
    {
        $montant = 0;
        $description = '';
        $item = null;

        if ($type === 'formation') {
            $item = Formation::findOrFail($id);
            $montant = $item->prix ?? 0;
            $description = $item->titre;
        } elseif ($type === 'duplicata') {
            // ✅ L'ID dans l'URL est directement le certificat_id (plus de session).
            $certificat = \App\Models\Certificat::findOrFail($id);
            abort_if($certificat->user_id !== auth()->id(), 403);

            $demande = DemandeDuplicata::where('certificat_id', $certificat->id)
                ->where('user_id', auth()->id())
                ->where('statut', 'en_attente')
                ->latest()
                ->first();

            if (!$demande) {
                return redirect()->route('client.certificats.index')
                    ->with('error', 'Aucune demande de duplicata en attente de paiement pour ce certificat. Merci de relancer la demande depuis "Mes Certificats".');
            }

            $item = $certificat;
            $montant = (int) ($demande->montant_paye ?? Configuration::get('duplicata_prix', 1000));
            $description = 'Duplicata de certificat — ' . ($certificat->formation->titre ?? $certificat->numero_certificat);
        } elseif ($type === 'service') {
            // ✅ CHANGEMENT : $id est désormais l'ID de la DemandeService précise
            // (pas du service générique). Nécessaire pour savoir exactement quelle
            // demande le paiement concerne, indispensable maintenant que le
            // paiement est vérifié par demande_id avant de démarrer un service
            // (cf. Admin\DemandeController::changerStatut()).
            $demande = DemandeService::with('service')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            abort_if(!$demande->service || !($demande->service->prix > 0), 404, 'Ce service ne nécessite aucun paiement.');

            $item = $demande;
            $montant = $demande->service->prix;
            $description = $demande->service->titre;
        } else {
            abort(404, 'Type de paiement invalide.');
        }

        return view('client.paiement-form', compact('type', 'id', 'montant', 'description', 'item'));
    }

    /**
     * ============================================================
     * 11. TRAITEMENT DU PAIEMENT
     * ============================================================
     */
    public function paiementProcess(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:formation,service,duplicata',
            'id'            => 'required',
            'montant'       => 'required|numeric|min:100',
            'mode_paiement' => 'required|in:orange_money,mtn_money,moov_money,visa,mastercard',
            'telephone'     => 'nullable|string|max:20',
        ]);

        $reference = 'EDC-PAY-' . strtoupper(uniqid());
        $modePropre = strtoupper(str_replace('_', ' ', $request->mode_paiement));

        // Déterminer les champs selon le type
        $formationId = null;
        $serviceId = null;
        $certificatId = null;
        $demandeId = null;
        $notes = '';

        if ($request->type === 'formation') {
            $formationId = $request->id;
            $notes = "Inscription à la formation — " . $modePropre;
        } elseif ($request->type === 'duplicata') {
            // ✅ L'ID est directement le certificat_id transmis dans l'URL/formulaire,
            // plus de dépendance à la session (source du bug précédent).
            $certificat = \App\Models\Certificat::find($request->id);

            if (!$certificat || $certificat->user_id !== auth()->id()) {
                return redirect()->route('client.certificats.index')
                    ->with('error', 'Certificat introuvable pour cette demande de duplicata.');
            }

            $certificatId = $certificat->id;
            $notes = "Achat de duplicata de certificat — " . $modePropre;
        } elseif ($request->type === 'service') {
            // ✅ CHANGEMENT : on retrouve la DemandeService précise pour lier
            // le paiement via demande_id — indispensable pour qu'Admin\
            // DemandeController::changerStatut() puisse vérifier qu'*cette*
            // demande précise a bien été payée avant de démarrer le service.
            $demande = DemandeService::with('service')
                ->where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$demande) {
                return redirect()->route('client.demandes')
                    ->with('error', 'Demande de service introuvable.');
            }

            $demandeId = $demande->id;
            $serviceId = $demande->service_id;
            $notes = "Paiement du service \"{$demande->service->titre}\" — " . $modePropre;
        }

        // ===== CRÉER LE PAIEMENT =====
        $paiement = Paiement::create([
            'user_id'        => auth()->id(),
            'formation_id'   => $formationId,
            'service_id'     => $serviceId,
            'demande_id'     => $demandeId,
            'certificat_id'  => $certificatId,
            'type'           => $request->type,
            'montant_total'  => $request->montant,
            'montant_paye'   => $request->montant,
            'statut'         => 'complete',
            'mode_paiement'  => $request->mode_paiement,
            'reference'      => $reference,
            'enregistre_par' => auth()->id(),
            'date_paiement'  => now(),
            'notes'          => $notes,
        ]);

        // ===== DUPLICATA =====
        if ($request->type === 'duplicata' && $certificatId) {
            $this->traiterDemandeDuplicata($certificatId, $paiement);

            return redirect()->route('client.paiements')
                ->with('success', '✅ Paiement effectué ! Votre demande de duplicata est en attente de validation par l\'administration.');
        }

        // ===== SERVICE =====
        if ($request->type === 'service' && $demandeId) {
            return redirect()->route('client.demandes')
                ->with('success', '✅ Paiement effectué ! Votre demande peut maintenant être prise en charge par notre équipe.');
        }

        return redirect()->route('client.paiements')
            ->with('success', '✅ Paiement de ' . number_format($request->montant, 0, ',', ' ') . ' FCFA effectué !');
    }

    /**
     * ============================================================
     * 12. FORMATIONS DISPONIBLES
     * ============================================================
     */
    public function formationsDisponibles()
    {
        $formations = Formation::with('module')
            ->where('statut', 'publie')
            ->get();

        return view('client.formations-disponibles', compact('formations'));
    }

    /**
     * ============================================================
     * 13. INSCRIPTION À UNE FORMATION
     * ============================================================
     */
    public function inscrireFormation(Formation $formation)
    {
        $dejaInscrit = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $formation->id)
            ->exists();

        if ($dejaInscrit) {
            return back()->with('error', 'Vous êtes déjà inscrit à cette formation.');
        }

        if ($formation->places_max && $formation->places_max > 0) {
            $inscriptions = InscriptionFormation::where('formation_id', $formation->id)
                ->where('statut', 'valide')
                ->count();

            if ($inscriptions >= $formation->places_max) {
                return back()->with('error', 'Cette formation est complète. Plus de places disponibles.');
            }
        }

        InscriptionFormation::create([
            'user_id'          => auth()->id(),
            'formation_id'     => $formation->id,
            'statut'           => 'en_attente',
            'date_inscription' => now(),
        ]);

        // Notifier tous les admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'titre'   => '📝 Nouvelle inscription en attente',
                'message' => auth()->user()->nom_complet . ' s\'est inscrit à la formation "' . $formation->titre . '".',
                'type'    => 'info',
                'lien'    => route('admin.inscriptions.index'),
            ]);
        }

        return redirect()->route('client.formations')
            ->with('success', 'Inscription à "' . $formation->titre . '" envoyée ! En attente de validation.');
    }

    /**
     * ============================================================
     * 14. MÉTHODES PRIVÉES
     * ============================================================
     */

    /**
     * Traiter une demande de duplicata après paiement
     *
     * ✅ CORRECTION CRITIQUE : on récupère la DemandeDuplicata déjà créée
     * (statut 'en_attente') au moment du clic du client sur "Mes Certificats",
     * et on la marque payée — au lieu d'en créer une nouvelle.
     * L'ancien code vérifiait l'existence d'une demande 'en_attente' et,
     * la trouvant, abandonnait silencieusement (juste un log) sans jamais
     * la faire passer à 'paye'. Résultat : le bouton "Valider" de l'admin
     * ne s'activait jamais, même après paiement réel du client.
     */
    private function traiterDemandeDuplicata(int $certificatId, Paiement $paiement)
    {
        $certificat = \App\Models\Certificat::find($certificatId);

        if (!$certificat) {
            Log::error('Certificat non trouvé pour la demande de duplicata', ['certificat_id' => $certificatId]);
            return;
        }

        $demande = DemandeDuplicata::where('certificat_id', $certificat->id)
            ->where('user_id', auth()->id())
            ->where('statut', 'en_attente')
            ->latest()
            ->first();

        if ($demande) {
            $demande->marquerPayee((int) $paiement->montant_paye, $paiement->id);
        } else {
            // Fallback défensif : aucune demande préalable trouvée (paiement initié
            // sans passer par "Mes Certificats"). On évite tout doublon si une
            // demande payée/validée existe déjà pour ce certificat.
            $demandeDejaTraitee = DemandeDuplicata::where('certificat_id', $certificat->id)
                ->whereIn('statut', ['paye', 'valide'])
                ->exists();

            if ($demandeDejaTraitee) {
                Log::warning('Demande de duplicata déjà payée/validée, paiement ignoré pour la création', ['certificat_id' => $certificat->id]);
                return;
            }

            $demande = DemandeDuplicata::create([
                'certificat_id' => $certificat->id,
                'user_id'       => auth()->id(),
                'paiement_id'   => $paiement->id,
                'statut'        => 'paye',
                'paye'          => true,
                'montant_paye'  => $paiement->montant_paye,
            ]);
        }

        // Notifier TOUS les admins (au lieu d'un seul via ->first())
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'titre'   => '📄 Demande de duplicata - Paiement reçu',
                'message' => auth()->user()->nom_complet . ' a payé ' . number_format($paiement->montant_paye, 0, ',', ' ') . ' FCFA pour un duplicata (' . ($certificat->formation->titre ?? '') . ').',
                'type'    => 'info',
                'lien'    => route('admin.duplicatas.demandes'),
            ]);
        }

        Log::info('Demande de duplicata marquée payée', [
            'demande_id' => $demande->id,
            'certificat_id' => $certificat->id,
            'user_id' => auth()->id(),
            'paiement_id' => $paiement->id,
        ]);
    }
}