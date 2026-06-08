@extends('layouts.public')
@section('title', 'Accueil — Excellence Digital Center')

@section('content')

{{-- HERO --}}
<section class="text-white py-16 sm:py-20 px-4"
    style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%)">
    <div class="max-w-6xl mx-auto">

        {{-- Mobile : centré | Desktop : 2 colonnes --}}
        <div class="text-center lg:text-left grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-block bg-white bg-opacity-20 text-blue-100
                    text-xs font-semibold px-4 py-2 rounded-full mb-5 tracking-wider">
                    🚀 Centre Digital — Korhogo / Sirasso
                </span>
                <h1 class="text-hero mb-5">
                    Former •
                    <span class="text-yellow-400">Créer</span>
                    • Réussir
                </h1>
                <p class="text-blue-200 text-base sm:text-lg leading-relaxed mb-7 mx-auto lg:mx-0 max-w-lg">
                    Votre partenaire en bureautique, solutions digitales et formation pratique.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <a href="{{ route('demande.form') }}"
                        class="bg-yellow-400 text-blue-900 font-bold px-7 py-3.5 rounded-2xl
                               hover:bg-yellow-300 transition text-center btn-touch">
                        💼 Demander un service
                    </a>
                    <a href="{{ route('formations.index') }}"
                        class="border-2 border-white text-white font-bold px-7 py-3.5
                               rounded-2xl hover:bg-white hover:text-blue-900
                               transition text-center btn-touch">
                        🎓 Formations
                    </a>
                </div>
            </div>

            {{-- Stats — visible sur tablette+ --}}
            <div class="hidden sm:grid grid-cols-2 gap-3">
                @foreach([
                    ['500+', 'Clients satisfaits'],
                    ['10+',  'Formations'],
                    ['20+',  'Services'],
                    ['3 ans','D\'expérience'],
                ] as $s)
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 text-center
                            border border-white border-opacity-20">
                    <p class="text-3xl font-black text-yellow-400">{{ $s[0] }}</p>
                    <p class="text-blue-200 text-sm mt-1">{{ $s[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats mobile --}}
        <div class="sm:hidden grid grid-cols-2 gap-3 mt-8">
            @foreach([['500+','Clients'],['10+','Formations'],['20+','Services'],['3 ans','Expérience']] as $s)
            <div class="bg-white bg-opacity-10 rounded-xl p-3 text-center border border-white border-opacity-20">
                <p class="text-2xl font-black text-yellow-400">{{ $s[0] }}</p>
                <p class="text-blue-200 text-xs mt-0.5">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Bandeau WhatsApp --}}
<div class="bg-yellow-400 py-2.5 px-4">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center
                justify-between text-blue-900 text-xs font-semibold gap-1">
        <span>📍 Korhogo / Sirasso — Côte d'Ivoire</span>
        <a href="https://wa.me/2250748746140" target="_blank"
            class="hover:underline flex items-center space-x-1">
            <span>💬</span><span>+225 07 48 74 61 40</span>
        </a>
    </div>
</div>

{{-- SERVICES --}}
<section class="py-14 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-10">
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold
                         px-3 py-1 rounded-full mb-3">💼 Nos Services</span>
            <h2 class="text-section text-blue-900">Solutions pour vos besoins</h2>
            <p class="text-gray-500 mt-2 text-sm">Bureautique, Digital & Web</p>
        </div>

        {{-- Grille : 1 col mobile, 2 tablette, 3 desktop --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($services as $service)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm
                        hover:shadow-md transition-all duration-200">
                <div class="text-3xl mb-3">{{ $service->icone ?? '⚙️' }}</div>
                <h3 class="font-bold text-blue-900 mb-2">{{ $service->titre }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-3">
                    {{ Str::limit($service->description, 85) }}
                </p>
                @if($service->prix)
                <p class="text-blue-700 font-bold text-sm mb-3">
                    Dès {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                </p>
                @endif
                <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                    class="inline-flex items-center text-blue-700 text-sm font-semibold
                           hover:text-blue-900 space-x-1">
                    <span>Demander</span><span>→</span>
                </a>
            </div>
            @empty
            <p class="text-gray-400 text-center col-span-3 py-8">Aucun service.</p>
            @endforelse
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('services.index') }}"
                class="inline-flex items-center space-x-2 bg-blue-800 text-white
                       font-semibold px-7 py-3 rounded-xl hover:bg-blue-900 transition btn-touch">
                <span>Tous les services</span><span>→</span>
            </a>
        </div>
    </div>
</section>

{{-- POURQUOI NOUS --}}
<section class="py-14 px-4 bg-blue-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-10">
            <h2 class="text-section text-blue-900">Pourquoi nous choisir ?</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['⚡','Rapidité','Travaux rendus dans les délais.'],
                ['🎯','Précision','Qualité professionnelle.'],
                ['🤝','Accompagnement','Suivi personnalisé.'],
                ['💰','Accessibilité','Tarifs adaptés à tous.'],
            ] as $v)
            <div class="bg-white rounded-2xl p-4 sm:p-5 text-center shadow-sm">
                <div class="text-3xl sm:text-4xl mb-2">{{ $v[0] }}</div>
                <h3 class="font-bold text-blue-900 text-sm mb-1">{{ $v[1] }}</h3>
                <p class="text-gray-500 text-xs leading-relaxed">{{ $v[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FORMATIONS --}}
<section class="py-14 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-10">
            <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold
                         px-3 py-1 rounded-full mb-3">🎓 Nos Formations</span>
            <h2 class="text-section text-blue-900">Apprenez à votre rythme</h2>
            <p class="text-gray-500 mt-2 text-sm">En ligne et en présentiel</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($formations as $formation)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm
                        hover:shadow-md transition overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/'.$formation->image) }}"
                    alt="{{ $formation->titre }}"
                    class="w-full h-40 object-cover">
                @else
                <div class="w-full h-40 flex items-center justify-center"
                    style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                    <span class="text-5xl">🎓</span>
                </div>
                @endif
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold
                                     px-2.5 py-1 rounded-full">
                            {{ ucfirst($formation->niveau) }}
                        </span>
                        @if($formation->duree)
                        <span class="text-xs text-gray-400">⏱ {{ $formation->duree }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-blue-900 mb-2">{{ $formation->titre }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">
                        {{ Str::limit($formation->description, 75) }}
                    </p>
                    <a href="{{ route('formations.show', $formation) }}"
                        class="block text-center bg-blue-800 text-white py-2.5 rounded-xl
                               text-sm font-semibold hover:bg-blue-900 transition btn-touch">
                        Voir la formation
                    </a>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-center col-span-3 py-8">Aucune formation.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- TÉMOIGNAGES --}}
@if($temoignages->count())
<section class="py-14 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-10">
            <span class="inline-block bg-yellow-100 text-yellow-700 text-xs font-semibold
                         px-3 py-1 rounded-full mb-3">⭐ Avis clients</span>
            <h2 class="text-section text-blue-900">Ce que disent nos clients</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach($temoignages as $t)
            <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-yellow-400">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center
                                justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr($t->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">
                            {{ $t->user->prenom }} {{ $t->user->nom }}
                        </p>
                        <div class="text-yellow-400 text-xs">
                            @for($i=1;$i<=5;$i++) {{ $i<=$t->note ? '★' : '☆' }} @endfor
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic text-sm leading-relaxed">
                    "{{ $t->contenu }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-16 px-4 text-white text-center"
    style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%)">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-4">Prêt à commencer ?</h2>
        <p class="text-blue-200 mb-7 text-sm sm:text-base">
            Rejoignez les centaines de personnes qui font confiance à EDC.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
                class="bg-yellow-400 text-blue-900 font-bold px-7 py-3.5 rounded-2xl
                       hover:bg-yellow-300 transition btn-touch">
                🚀 Créer mon compte
            </a>
            <a href="https://wa.me/2250748746140" target="_blank"
                class="border-2 border-white text-white font-bold px-7 py-3.5 rounded-2xl
                       hover:bg-white hover:text-blue-900 transition btn-touch">
                💬 WhatsApp
            </a>
        </div>
    </div>
</section>

@endsection