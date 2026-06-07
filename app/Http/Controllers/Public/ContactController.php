<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use App\Mail\DemandeServiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreDemandeServiceRequest;

class ContactController extends Controller
{
    // Page contact
    public function index()
    {
        return view('public.contact');
    }

    // Envoi message contact
    public function send(Request $request)
    {
        $request->validate([
            'nom'     => 'required|string|max:100',
            'email'   => 'required|email',
            'sujet'   => 'required|string|max:150',
            'message' => 'required|string|min:10',
        ]);

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ContactMail($request->all()));

            Notification::create([
                'user_id' => $admin->id,
                'titre'   => '📩 Nouveau message de contact',
                // ✅ CORRECTION 2 : échappement XSS avec e()
                'message' => "Message de " . e($request->nom) . " (" . e($request->email) . ") : " . e($request->sujet),
                'type'    => 'info',
            ]);
        }

        return back()->with('success', '✅ Votre message a été envoyé ! Nous vous répondrons rapidement.');
    }

    // Page demande de service (visiteur)
    public function demandeForm()
    {
        $services   = Service::where('actif', true)->get();
        $formations = Formation::where('statut', 'publie')->get();
        return view('public.demande-service', compact('services', 'formations'));
    }

    // Traitement demande de service

public function demandeStore(StoreDemandeServiceRequest $request)
{
    // validation automatique (FormRequest)

    $demande = DemandeService::create([
        'user_id'            => auth()->id() ?? null,
        'nom_visiteur'       => $request->nom_visiteur,
        'email_visiteur'     => $request->email_visiteur,
        'telephone_visiteur' => $request->telephone_visiteur,
        'service_id'         => $request->service_id,
        'message'            => $request->message,
        'statut'             => 'en_attente',
    ]);

    Mail::to($request->email_visiteur)
        ->send(new DemandeServiceMail($demande));

    $admins = User::role('admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'titre'   => '🔔 Nouvelle demande de service',
            'message' => "Demande de " . e($request->nom_visiteur) . " pour : " . e($demande->service->titre),
            'type'    => 'info',
        ]);
    }

    return back()->with('success', '✅ Votre demande a bien été enregistrée ! Nous vous contacterons sous 24h.');
}
}