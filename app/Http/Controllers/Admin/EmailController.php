<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    // ===== FORMULAIRE =====
    public function form()
    {
        $clients     = User::role('client')->where('statut', 'actif')->get();
        $enseignants = User::role('enseignant')->where('statut', 'actif')->get();

        $logs = MailLog::with(['expediteur', 'destinataire'])
                    ->latest()->paginate(20);

        $stats = [
            'total'   => MailLog::count(),
            'envoyes' => MailLog::where('statut', 'envoye')->count(),
            'echoues' => MailLog::where('statut', 'echoue')->count(),
        ];

        return view('admin.emails', compact(
            'clients', 'enseignants', 'logs', 'stats'
        ));
    }

    // ===== ENVOYER EMAIL =====
    public function envoyer(Request $request)
    {
        $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'sujet'           => 'required|string|max:200',
            'message'         => 'required|string|min:10',
        ]);

        $admin       = auth()->user();
        $destinataire = User::findOrFail($request->destinataire_id);

        $success = MailService::adminVersClient(
            $admin,
            $destinataire,
            $request->sujet,
            $request->message
        );

        return back()->with(
            $success ? 'success' : 'error',
            $success
                ? "✅ Email envoyé à {$destinataire->nom_complet} !"
                : "❌ Échec de l'envoi. Vérifiez la configuration mail."
        );
    }
}