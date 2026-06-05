@extends('layouts.public')
@section('title', 'Accueil  — Excellence Digital Center')

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-24 px-4">
    <div class="max-w-5xl mx-auto text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
            Former • Créer • Réussir 🚀
        </h1>
        <p class="text-blue-200 text-lg md:text-xl mb-10 max-w-2xl mx-auto">
            Votre partenaire en bureautique, solutions digitales et formation pratique à Korhogo / Sirasso.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('demande.form') }}"
                class="bg-white text-blue-900 font-bold px-8 py-4 rounded-full hover:bg-blue-50 transition text-lg">
                Demander un service
            </a>
            <a href="{{ route('formations.index') }}"
                class="border-2 border-white text-white font-bold px-8 py-4 rounded-full hover:bg-white hover:text-blue-900 transition text-lg">
                Voir les formations
            </a>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="bg-blue-50 py-12">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="bg-white rounded-xl p-6 shadow">
            <p class="text-4xl font-bold text-blue-800">500+</p>
            <p class="text-gray-600 mt-1">Clients satisfaits</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow">
            <p class="text-4xl font-bold text-blue-800">10+</p>
            <p class="text-gray-600 mt-1">Formations</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow">
            <p class="text-4xl font-bold text-blue-800">20+</p>
            <p class="text-gray-600 mt-1">Services</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow">
            <p class="text-4xl font-bold text-blue-800">3 ans</p>
            <p class="text-gray-600 mt-1">D'expérience</p>
        </div>
    </div>
</section>

{{-- SERVICES --}}
<section class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-blue-900">Nos Services</h2>
            <p class="text-gray-500 mt-2">Des solutions adaptées à vos besoins</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100">
                <div class="text-4xl mb-4">{{ $service->icone ?? '💼' }}</div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">{{ $service->titre }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    {{ Str::limit($service->description, 100) }}
                </p>
                <a href="{{ route('services.show', $service) }}"
                    class="inline-block mt-4 text-blue-700 font-medium hover:underline text-sm">
                    En savoir plus →
                </a>
            </div>
            @empty
            <p class="text-gray-400 col-span-3 text-center">Aucun service disponible pour le moment.</p>
            @endforelse
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('services.index') }}"
                class="bg-blue-800 text-white px-8 py-3 rounded-full font-semibold hover:bg-blue-900 transition">
                Voir tous les services
            </a>
        </div>
    </div>
</section>

{{-- FORMATIONS --}}
<section class="bg-gray-50 py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-blue-900">Nos Formations</h2>
            <p class="text-gray-500 mt-2">Apprenez à votre rythme, en ligne ou en présentiel</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($formations as $formation)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/' . $formation->image) }}"
                    alt="{{ $formation->titre }}" class="w-full h-40 object-cover">
                @else
                <div class="w-full h-40 bg-gradient-to-br from-blue-700 to-blue-500 flex items-center justify-center">
                    <span class="text-5xl">🎓</span>
                </div>
                @endif
                <div class="p-5">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                        {{ ucfirst($formation->niveau) }}
                    </span>
                    <h3 class="text-lg font-bold text-blue-900 mt-3 mb-2">{{ $formation->titre }}</h3>
                    <p class="text-gray-500 text-sm">{{ Str::limit($formation->description, 80) }}</p>
                    <a href="{{ route('formations.show', $formation) }}"
                        class="inline-block mt-4 bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                        Voir la formation
                    </a>
                </div>
            </div>
            @empty
            <p class="text-gray-400 col-span-3 text-center">Aucune formation disponible pour le moment.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- TEMOIGNAGES --}}
@if($temoignages->count())
<section class="py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-blue-900">Ce que disent nos clients</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($temoignages as $temoignage)
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-700">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold mr-3">
                        {{ strtoupper(substr($temoignage->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900">{{ $temoignage->user->nom_complet }}</p>
                        <div class="text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $temoignage->note ? '★' : '☆' }}
                            @endfor
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic text-sm leading-relaxed">
                    "{{ $temoignage->contenu }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA FINAL --}}
<section class="bg-blue-900 text-white py-16 px-40 text-center">
    <h2 class="text-3xl font-bold mb-4">Prêt à commencer ?</h2>
    <p class="text-blue-200 mb-8 text-lg">Contactez-nous ou faites une demande de service dès maintenant.</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('demande.form') }}"
            class="bg-white text-blue-900 font-bold px-8 py-4 rounded-full hover:bg-blue-50 transition">
            Demander un service
        </a>
        <a href="https://wa.me/2250748746140" target="_blank"
            class="border-2 border-white text-white font-bold px-8 py-4 rounded-full hover:bg-white hover:text-blue-900 transition">
            💬 WhatsApp
        </a>
    </div>
</section>

@endsection