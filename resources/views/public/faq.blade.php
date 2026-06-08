@extends('layouts.public')
@section('title', 'FAQ — Excellence Digital Center')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">❓ Foire aux Questions</h1>
        <p class="section-subtitle">Trouvez rapidement les réponses à vos questions</p>
    </div>

    @forelse($faqs as $categorie => $questions)
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-5 pb-2" style="color: var(--edc-primary-light); border-bottom: 2px solid var(--edc-border);">
            📂 {{ ucfirst($categorie) }}
        </h2>

        <div class="space-y-3" x-data="{ open: null }">
            @foreach($questions as $index => $faq)
            <div class="edc-card overflow-hidden">
                <button
                    @click="open === {{ $index }} ? open = null : open = {{ $index }}"
                    class="w-full text-left px-6 py-4 flex justify-between items-center transition"
                    style="color: var(--edc-text-primary);">
                    <span class="font-semibold text-sm pr-4">
                        {{ $faq->question }}
                    </span>
                    <span class="text-xl flex-shrink-0" style="color: var(--edc-primary-light);"
                        x-text="open === {{ $index }} ? '−' : '+'"></span>
                </button>
                <div x-show="open === {{ $index }}"
                    x-transition
                    class="px-6 pb-4 text-sm leading-relaxed border-t"
                    style="color: var(--edc-text-secondary); border-color: var(--edc-border);">
                    <div class="pt-3">{{ $faq->reponse }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-12" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">❓</p>
        <p>Aucune question disponible pour le moment.</p>
    </div>
    @endforelse

    {{-- CTA CONTACT --}}
    <div class="edc-card p-8 text-center mt-8" style="background: linear-gradient(135deg, var(--edc-primary-dark), var(--edc-primary));">
        <p class="text-xl font-bold mb-2" style="color: #fff;">Vous n'avez pas trouvé votre réponse ?</p>
        <p class="text-sm mb-5" style="color: rgba(255,255,255,0.7);">Contactez-nous directement !</p>
        <a href="{{ route('contact') }}" class="btn-secondary" style="border-color: rgba(255,255,255,0.4); color: #fff;">
            📩 Nous contacter
        </a>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection