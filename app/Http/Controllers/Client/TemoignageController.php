<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Service;
use App\Models\Temoignage;
use Illuminate\Http\Request;

class TemoignageController extends Controller
{
    public function index()
    {
        $temoignages = Temoignage::where('user_id', auth()->id())
            ->latest()->get();

        $formations = Formation::whereHas('inscriptions', fn($q) =>
            $q->where('user_id', auth()->id())->where('statut', 'valide')
        )->get();

        $services = Service::whereHas('demandes', fn($q) =>
            $q->where('user_id', auth()->id())->where('statut', 'termine')
        )->get();

        return view('client.temoignages', compact('temoignages', 'formations', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contenu'      => 'required|string|min:10|max:500',
            'note'         => 'required|integer|min:1|max:5',
            'formation_id' => 'nullable|exists:formations,id',
            'service_id'   => 'nullable|exists:services,id',
        ]);

        // Vérifier qu'il n'a pas déjà témoigné pour ce sujet
        $existe = Temoignage::where('user_id', auth()->id())
            ->where('formation_id', $request->formation_id)
            ->where('service_id', $request->service_id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà soumis un avis pour cet élément.');
        }

        Temoignage::create([
            'user_id'            => auth()->id(),
            'contenu'            => $request->contenu,
            'note'               => $request->note,
            'formation_id'       => $request->formation_id,
            'service_id'         => $request->service_id,
            'statut_validation'  => 'en_attente',
        ]);

        return back()->with('success', '✅ Votre avis a été soumis et sera publié après modération.');
    }

    public function destroy(Temoignage $temoignage)
    {
        abort_if($temoignage->user_id !== auth()->id(), 403);
        $temoignage->delete();
        return back()->with('success', 'Avis supprimé.');
    }
}