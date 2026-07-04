<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('ordre')->get();
        return response()->json($faqs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'reponse' => 'required|string',
            'ordre' => 'nullable|integer',
            'actif' => 'boolean',
        ]);

        $validated['ordre'] = $validated['ordre'] ?? Faq::max('ordre') + 1;

        $faq = Faq::create($validated);

        return response()->json([
            'message' => 'FAQ créée.',
            'faq' => $faq
        ], 201);
    }

    public function update(Faq $faq, Request $request)
    {
        $validated = $request->validate([
            'question' => 'sometimes|string|max:500',
            'reponse' => 'sometimes|string',
            'ordre' => 'nullable|integer',
            'actif' => 'boolean',
        ]);

        $faq->update($validated);

        return response()->json([
            'message' => 'FAQ mise à jour.',
            'faq' => $faq->fresh()
        ]);
    }

    public function toggleActif(Faq $faq)
    {
        $faq->update(['actif' => !$faq->actif]);

        return response()->json([
            'message' => $faq->actif ? 'FAQ activée.' : 'FAQ désactivée.',
            'actif' => $faq->actif
        ]);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return response()->json(['message' => 'FAQ supprimée.']);
    }
}