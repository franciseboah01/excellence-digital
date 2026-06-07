@extends('layouts.public')
@section('title', 'Accueil — Excellence Digital Center')

@section('content')

{{-- ===== HERO ===== --}}
<section class="hero-gradient text-white relative overflow-hidden">
    {{-- Formes décoratives --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

    <div class="max-w-6xl mx-auto px-4 py-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in-up">
                <span class="inline-block bg-white bg-opacity-20 text-blue-100 text-xs font-semibold px-4 py-2 rounded-full mb-6 tracking-wider uppercase">
                    🚀 Centre Digital de Référence
                </span>
                <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Former<br>
                    <span class="text-yellow-400">Créer</span><br>
                    Réussir
                </h1>
                <p class="text-blue-200 text-lg leading-relaxed mb-8 max-w-lg">
                    Votre partenaire en bureautique, solutions digitales et formation pratique à Korhogo / Sirasso.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('demande.form') }}"
                        class="bg-yellow-400 text-blue-900 font-bold px-8 py-4 rounded-2xl hover:bg-yellow-300 transition text-center shadow-lg">
                        💼 Demander un service
                    </a>
                    <a href="{{ route('formations.index') }}"
                        class="border-2 border-white text-white font-bold px-8 py-4 rounded-2xl hover:bg-white hover:text-blue-900 transition text-center">
                        🎓 Voir les formations
                    </a>
                </div>
            </div>

            {{-- Carte flottante --}}
            <div class="hidden lg:block">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm border border-white border-opacity-20 rounded-3xl p-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white bg-opacity-20 rounded-2xl p-5 text-center">
                            <p class="text-4xl font-black text-yellow-400">500+</p>
                            <p class="text-blue-200 text-sm mt-1">Clients satisfaits</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-2xl p-5 text-center">
                            <p class="text-4xl font-black text-yellow-400">10+</p>
                            <p class="text-blue-200 text-sm mt-1">Formations</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-2xl p-5 text-center">
                            <p class="text-4xl font-black text-yellow-400">20+</p>
                            <p class="text-blue-200 text-sm mt-1">Services</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-2xl p-5 text-center">
                            <p class="text-4xl font-black text-yellow-400">3 ans</p>
                            <p class="text-blue-200 text-sm mt-1">D'expérience</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-white bg-opacity-20 rounded-2xl p-4 text-center">
                        <p class="text-white font-semibold">📍 Korhogo / Sirasso</p>
                        <p class="text-blue-200 text-sm mt-1">📲 +225 07 48 74 61 40</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vague décorative --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,60 C480,0 960,60 1440,0 L1440,60 Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <span class="badge badge-blue mb-3">💼 Nos Services</span>
            <h2 class="section-title">Des solutions pour vos besoins</h2>
            <p class="section-subtitle">Bureautique, Digital, Web & Mobile</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
            <div class="edc-card p-6 group">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-3xl mb-4 group-hover:bg-blue-100 transition">
                    {{ $service->icone ?? '⚙️' }}
                </div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">{{ $service->titre }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4">
                    {{ Str::limit($service->description, 90) }}
                </p>
                @if($service->prix)
                <p class="text-blue-700 font-bold text-sm mb-3">
                    À partir de {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                </p>
                @endif
                <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                    class="text-blue-700 text-sm font-semibold hover:text-blue-900 inline-flex items-center space-x-1">
                    <span>Demander</span><span>→</span>
                </a>
            </div>
            @empty
            <p class="text-gray-400 col-span-3 text-center py-8">Aucun service disponible.</p>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('services.index') }}" class="btn-primary">
                <span>Tous nos services</span><span>→</span>
            </a>
        </div>
    </div>
</section>

{{-- ===== POURQUOI NOUS ===== --}}
<section class="bg-blue-50 py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <span class="badge badge-blue mb-3">🎯 Pourquoi nous choisir ?</span>
            <h2 class="section-title">L'excellence au service de votre réussite</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['⚡', 'Rapidité', 'Travaux rendus dans les délais convenus.'],
                ['🎯', 'Précision', 'Qualité professionnelle garantie.'],
                ['🤝', 'Accompagnement', 'Suivi personnalisé à chaque étape.'],
                ['💰', 'Accessibilité', 'Tarifs adaptés à tous les budgets.'],
            ] as $item)
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                    {{ $item[0] }}
                </div>
                <h3 class="font-bold text-blue-900 mb-2">{{ $item[1] }}</h3>
                <p class="text-gray-500 text-sm">{{ $item[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== FORMATIONS ===== --}}
<section class="py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <span class="badge badge-green mb-3">🎓 Nos Formations</span>
            <h2 class="section-title">Apprenez à votre rythme</h2>
            <p class="section-subtitle">En ligne et en présentiel, pour tous les niveaux</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($formations as $formation)
            <div class="edc-card overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/' . $formation->image) }}"
                    alt="{{ $formation->titre }}" class="w-full h-44 object-cover">
                @else
                <div class="w-full h-44 bg-gradient-to-br from-blue-800 to-blue-500 flex items-center justify-center">
                    <span class="text-6xl">🎓</span>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="badge badge-blue">{{ ucfirst($formation->niveau) }}</span>
                        @if($formation->duree)
                        <span class="text-xs text-gray-400">⏱ {{ $formation->duree }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">{{ $formation->titre }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">
                        {{ Str::limit($formation->description, 80) }}
                    </p>
                    <a href="{{ route('formations.show', $formation) }}"
                        class="btn-primary btn-sm w-full text-center">
                        Voir la formation
                    </a>
                </div>
            </div>
            @empty
            <p class="text-gray-400 col-span-3 text-center py-8">Aucune formation disponible.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== TÉMOIGNAGES ===== --}}
@if($temoignages->count())
<section class="bg-gray-50 py-20 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <span class="badge badge-yellow mb-3">⭐ Témoignages</span>
            <h2 class="section-title">Ce que disent nos clients</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($temoignages as $temoignage)
            <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-400">
                <div class="flex items-center mb-4 space-x-3">
                    <div class="w-12 h-12 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($temoignage->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">{{ $temoignage->user->nom_complet }}</p>
                        <div class="text-yellow-400 text-sm">
                            {!! $temoignage->etoiles_html !!}
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic leading-relaxed text-sm">
                    "{{ $temoignage->contenu }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== CTA FINAL ===== --}}
<section class="hero-gradient text-white py-20 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-4xl font-extrabold mb-4">Prêt à commencer ?</h2>
        <p class="text-blue-200 text-lg mb-10">
            Rejoignez les centaines de personnes qui ont déjà fait confiance à EDC.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
                class="bg-yellow-400 text-blue-900 font-bold px-8 py-4 rounded-2xl hover:bg-yellow-300 transition">
                🚀 Créer mon compte
            </a>
            <a href="https://wa.me/2250748746140" target="_blank"
                class="border-2 border-white text-white font-bold px-8 py-4 rounded-2xl hover:bg-white hover:text-blue-900 transition">
                💬 WhatsApp
            </a>
        </div>
    </div>
</section>

@endsection