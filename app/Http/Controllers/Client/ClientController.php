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

        // Stocker les données en session
        session([
            'demande_data' => [
                'service_id'         => $service->id,
                'telephone_visiteur' => $validated['telephone_visiteur'] ?? auth()->user()->telephone,
                'message'            => $validated['message'] ?? null,
            ]
        ]);

        // Si le service est payant
        if ($service->prix && $service->prix > 0) {
            return redirect()->route('client.paiement.form', ['type' => 'service', 'id' => $service->id]);
        }

        // Service gratuit
        $this->enregistrerDemande();
        session()->forget('demande_data');

        return redirect()->route('client.demandes')
            ->with('success', 'Votre demande a été envoyée avec succès !');
    }

    private function enregistrerDemande()
    {
        $data = session('demande_data');
        if (!$data) {
            return;
        }

        DemandeService::create([
            'user_id'            => auth()->id(),
            'service_id'         => $data['service_id'],
            'nom_visiteur'       => auth()->user()->nom_complet,
            'email_visiteur'     => auth()->user()->email,
            'telephone_visiteur' => $data['telephone_visiteur'],
            'message'            => $data['message'],
            'statut'             => 'en_attente',
        ]);
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
        if ($formation->prix && $formation->prix > 0) {
            $aPaye = Paiement::where('user_id', auth()->id())
                ->where('formation_id', $formation->id)
                ->where('statut', 'complete')
                ->exists();

            if (!$aPaye) {
                return redirect()->route('client.paiement.form', ['type' => 'formation', 'id' => $formation->id])
                    ->with('error', 'Vous devez payer cette formation avant d\'accéder aux ressources.');
            }
        }

        $niveaux = $formation->niveaux()
            ->with(['ressources' => function ($q) {
                $q->where('actif', true)->orderBy('created_at', 'desc');
            }])
            ->orderBy('ordre')
            ->get();

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
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
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
        } elseif ($type === 'service' && $id === 'duplicata') {
            $montant = (int) Configuration::get('duplicata_prix', 1000);
            $description = 'Duplicata de certificat';
        } elseif ($type === 'service') {
            $item = Service::findOrFail($id);
            $montant = $item->prix ?? 0;
            $description = $item->titre;
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
            'id'            => 'required|string',
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
        $notes = '';

        if ($request->type === 'formation') {
            $formationId = $request->id;
            $notes = "Inscription à la formation — " . $modePropre;
        } elseif ($request->type === 'service' && $request->id === 'duplicata') {
            $certificatId = session('certificat_id');
            $notes = "Achat de duplicata de certificat — " . $modePropre;
        } elseif ($request->type === 'service') {
            $serviceId = $request->id;
            $notes = "Achat de service — " . $modePropre;
        }

        // ===== CRÉER LE PAIEMENT =====
        $paiement = Paiement::create([
            'user_id'        => auth()->id(),
            'formation_id'   => $formationId,
            'service_id'     => $serviceId,
            'certificat_id'  => $certificatId,
            'montant_total'  => $request->montant,
            'montant_paye'   => $request->montant,
            'statut'         => 'complete',
            'mode_paiement'  => $request->mode_paiement,
            'reference'      => $reference,
            'enregistre_par' => auth()->id(),
            'date_paiement'  => now(),
            'notes'          => $notes,
        ]);

        // ===== SERVICE CLASSIQUE =====
        if ($request->type === 'service' && $request->id !== 'duplicata' && session('demande_data')) {
            $this->enregistrerDemande();
            session()->forget('demande_data');
        }

        // ===== DUPLICATA =====
        if ($request->type === 'service' && $request->id === 'duplicata' && $certificatId) {
            $this->traiterDemandeDuplicata($certificatId, $paiement);
            session()->forget('certificat_id');

            return redirect()->route('client.paiements')
                ->with('success', '✅ Paiement effectué ! Votre demande de duplicata est en attente de validation par l\'administration.');
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
        // Vérifier si déjà inscrit
        $dejaInscrit = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $formation->id)
            ->exists();

        if ($dejaInscrit) {
            return back()->with('error', 'Vous êtes déjà inscrit à cette formation.');
        }

        // Vérifier si la formation est complète
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

        // Notifier l'admin
        $admin = User::role('admin')->first();
        if ($admin) {
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
     */
    private function traiterDemandeDuplicata(int $certificatId, Paiement $paiement)
    {
        $certificat = \App\Models\Certificat::find($certificatId);

        if (!$certificat) {
            Log::error('Certificat non trouvé pour la demande de duplicata', ['certificat_id' => $certificatId]);
            return;
        }

        // Vérifier qu'il n'y a pas déjà une demande en cours
        $demandeExistante = DemandeDuplicata::where('certificat_id', $certificat->id)
            ->whereIn('statut', ['en_attente', 'valide'])
            ->exists();

        if ($demandeExistante) {
            Log::warning('Demande de duplicata déjà existante', ['certificat_id' => $certificat->id]);
            return;
        }

        // Créer la demande de duplicata
        DemandeDuplicata::create([
            'certificat_id' => $certificat->id,
            'user_id'       => auth()->id(),
            'paiement_id'   => $paiement->id,
            'statut'        => 'en_attente',
            'paye'          => true,
            'montant_paye'  => $paiement->montant_paye,
        ]);

        // Notifier l'admin
        $admin = User::role('admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'titre'   => '📄 Demande de duplicata - Paiement reçu',
                'message' => auth()->user()->nom_complet . ' a payé ' . number_format($paiement->montant_paye, 0, ',', ' ') . ' FCFA pour un duplicata (' . ($certificat->formation->titre ?? '') . ').',
                'type'    => 'info',
                'lien'    => route('admin.duplicatas.demandes'),
            ]);
        }

        Log::info('Demande de duplicata créée', [
            'certificat_id' => $certificat->id,
            'user_id' => auth()->id(),
            'paiement_id' => $paiement->id,
        ]);
    }
}