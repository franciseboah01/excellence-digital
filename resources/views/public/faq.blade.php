@extends('layouts.public')
@section('title', 'FAQ — Excellence Digital Center')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900">❓ Foire aux Questions</h1>
        <p class="text-gray-500 mt-3">Trouvez rapidement les réponses à vos questions</p>
    </div>

    @forelse($faqs as $categorie => $questions)
    <div class="mb-10">
        <h2 class="text-xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-2">
            📂 {{ ucfirst($categorie) }}
        </h2>

        <div class="space-y-3" x-data="{ open: null }">
            @foreach($questions as $index => $faq)
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                <button
                    @click="open === {{ $index }} ? open = null : open = {{ $index }}"
                    class="w-full text-left px-6 py-4 flex justify-between items-center hover:bg-blue-50 transition">
                    <span class="font-semibold text-gray-800 text-sm pr-4">
                        {{ $faq->question }}
                    </span>
                    <span class="text-blue-700 text-xl flex-shrink-0"
                        x-text="open === {{ $index }} ? '−' : '+'"></span>
                </button>
                <div x-show="open === {{ $index }}"
                    x-transition
                    class="px-6 pb-4 text-gray-600 text-sm leading-relaxed border-t border-gray-100">
                    <div class="pt-3">{{ $faq->reponse }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <p class="text-5xl mb-4">❓</p>
        <p>Aucune question disponible pour le moment.</p>
    </div>
    @endforelse

    {{-- CTA CONTACT --}}
    <div class="bg-blue-800 text-white rounded-2xl p-8 text-center mt-8">
        <p class="text-xl font-bold mb-2">Vous n'avez pas trouvé votre réponse ?</p>
        <p class="text-blue-200 mb-5">Contactez-nous directement !</p>
        <a href="{{ route('contact') }}"
            class="bg-white text-blue-800 font-bold px-8 py-3 rounded-full hover:bg-blue-50 transition">
            📩 Nous contacter
        </a>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection