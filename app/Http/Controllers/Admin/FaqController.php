<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('categorie')->orderBy('ordre')->paginate(20);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question'  => 'required|string|max:300',
            'reponse'   => 'required|string',
            'categorie' => 'required|string|max:50',
            'ordre'     => 'nullable|integer|min:0',
        ]);

        Faq::create([
            'question'  => $request->question,
            'reponse'   => $request->reponse,
            'categorie' => $request->categorie,
            'ordre'     => $request->ordre ?? 0,
            'actif'     => true,
        ]);

        return back()->with('success', 'Question ajoutée !');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question'  => 'required|string|max:300',
            'reponse'   => 'required|string',
            'categorie' => 'required|string|max:50',
        ]);

        $faq->update($request->only(['question', 'reponse', 'categorie', 'ordre']));
        return back()->with('success', 'FAQ mise à jour !');
    }

    public function toggleActif(Faq $faq)
    {
        $faq->update(['actif' => !$faq->actif]);
        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'Question supprimée.');
    }
}