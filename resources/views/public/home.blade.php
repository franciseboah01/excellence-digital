@extends('layouts.public')
@section('title', 'Accueil — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@php
    // ═══════════════════════════════════════
    // CONFIGURATIONS GLOBALES
    // ═══════════════════════════════════════
    $siteNom         = \App\Models\Configuration::get('site_nom', 'Excellence Digital Center');
    $siteDevise      = \App\Models\Configuration::get('site_devise', 'Former • Créer • Réussir');
    $siteDescription = \App\Models\Configuration::get('site_description', 'Votre centre digital de référence.');
    $siteWhatsapp    = \App\Models\Configuration::get('site_whatsapp', '2250748746140');
    $siteBanniere    = \App\Models\Configuration::get('site_banniere', 'images/edc-banner.png');
    $ctaTitre        = \App\Models\Configuration::get('site_cta_titre', 'Prêt à démarrer ?');
    $ctaSoustitre    = \App\Models\Configuration::get('site_cta_soustitre', 'Rejoignez les centaines de personnes qui nous font confiance.');
    
    $deviseParts = array_map('trim', explode('•', $siteDevise));
    $whatsappUrl = 'https://wa.me/' . $siteWhatsapp;
@endphp

@section('content')

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 1 : HERO --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<section class="relative text-white py-20 sm:py-28 px-4 overflow-hidden">

    {{-- Image 16:9 en arrière-plan --}}
    <div class="absolute inset-0 z-0">
        @if($siteBanniere)
        <img src="{{ asset($siteBanniere) }}"
             alt="{{ $siteNom }}"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'">
        @endif
        <div class="absolute inset-0" style="background: rgba(11, 15, 26, 0.80);"></div>
    </div>

    <div class="max-w-5xl mx-auto text-center relative z-10">

        {{-- Titre avec devise dynamique --}}
        <h1 class="text-hero mb-6">
            @foreach($deviseParts as $part)
                @if(!$loop->first) <span style="color: rgba(255,255,255,0.4);">•</span> @endif
                <span class="text-gradient">{{ $part }}</span>
            @endforeach
        </h1>

        {{-- Description --}}
        <p class="text-base sm:text-lg max-w-2xl mx-auto mb-10 leading-relaxed"
           style="color: #94A3B8;">
            {{ $siteDescription }}
        </p>

        {{-- Boutons CTA --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('demande.form') }}" class="btn-primary animate-glow">
                <span>💼</span>
                <span>Demander un service</span>
            </a>
            <a href="{{ route('formations.index') }}" class="btn-secondary">
                <span>🎓</span>
                <span>Voir les formations</span>
            </a>
        </div>

        {{-- Stats dynamiques --}}
        @if(!empty($stats))
        <div class="grid grid-cols-2 sm:grid-cols-{{ min(count($stats), 4) }} gap-4 mt-16">
            @foreach($stats as $stat)
            <div class="rounded-xl py-5 px-3"
                 style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.10);">
                <p class="text-2xl sm:text-3xl font-black" style="color: #F1F5F9;">
                    {{ $stat['valeur'] ?? '' }}
                </p>
                <p class="text-xs sm:text-sm mt-1" style="color: #64748B;">
                    {{ $stat['description'] ?? '' }}
                </p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 1B : GALERIE DYNAMIQUE --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if(!empty($galeries))
<section class="py-12 sm:py-16 px-4" style="background-color: var(--edc-bg-deep);">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #3B82F6;">
                Galerie
            </p>
            <h2 class="text-section">Découvrez notre univers</h2>
        </div>

        <div class="relative"
             x-data="{
                images: {{ json_encode(array_column($galeries, 'image')) }},
                current: 0,
                total: {{ count($galeries) }},
                init() {
                    if (this.total > 1) {
                        setInterval(() => { this.next(); }, 2500);
                    }
                },
                next() { this.current = (this.current + 1) % this.total; },
                prev() { this.current = (this.current - 1 + this.total) % this.total; },
                getClass(index) {
                    if (this.total <= 1) return 'z-20 scale-100 opacity-100';
                    const diff = (index - this.current + this.total) % this.total;
                    if (diff === 0) return 'z-20 scale-100 opacity-100';
                    if (diff === 1 || diff === this.total - 1) return 'z-10 scale-90 opacity-60';
                    return 'z-0 scale-75 opacity-0 hidden';
                }
             }">

            <div class="flex items-center justify-center gap-2 sm:gap-4 min-h-[380px] sm:min-h-[450px]">
                <template x-for="(img, index) in images" :key="index">
                    <div class="flex-shrink-0 transition-all duration-500 ease-in-out rounded-2xl overflow-hidden shadow-2xl"
                         :class="getClass(index)"
                         style="width: clamp(180px, 25vw, 280px); aspect-ratio: 9/16; border: 1px solid var(--edc-border);">
                        <img :src="'{{ asset('storage') }}/' + img"
                             :alt="'Galerie ' + (index + 1)"
                             class="w-full h-full object-cover"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\' style=\'background:linear-gradient(135deg,#1e3a8a,#2563eb);\'><span class=\'text-4xl\'>🖼️</span></div>'">
                    </div>
                </template>
            </div>

            @if(count($galeries) > 1)
            <button @click="prev()"
                class="absolute left-0 sm:left-2 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 hover:scale-110"
                style="background: rgba(59,130,246,0.7); backdrop-filter: blur(8px);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next()"
                class="absolute right-0 sm:right-2 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 hover:scale-110"
                style="background: rgba(59,130,246,0.7); backdrop-filter: blur(8px);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="flex justify-center space-x-2 mt-6">
                <template x-for="i in total" :key="i">
                    <button @click="current = i - 1"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                        :class="current === i - 1 ? 'bg-blue-500 w-8' : 'bg-gray-600 hover:bg-gray-400'">
                    </button>
                </template>
            </div>
            @endif
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 2 : NOS SERVICES (déjà dynamique) --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<section class="py-16 sm:py-20 px-4" style="background-color: #0F172A;">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #3B82F6;">
                Nos Services
            </p>
            <h2 class="text-section">
                Des solutions pour tous vos besoins
            </h2>
            <p class="mt-3 text-lg" style="color: #64748B;">
                Bureautique, design, web et développement mobile
            </p>
        </div>

        <div class="grid-responsive-3">
            @forelse($services as $service)
            <div class="edc-card p-6">
                <div class="text-3xl mb-4">{{ $service->icone ?? '⚙️' }}</div>
                <h3 class="font-bold mb-2" style="color: #F1F5F9;">{{ $service->titre }}</h3>
                <p class="text-sm leading-relaxed mb-4" style="color: #64748B;">
                    {{ Str::limit($service->description, 85) }}
                </p>
                @if($service->prix)
                <p class="text-sm font-bold mb-3" style="color: #60A5FA;">
                    Dès {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                </p>
                @endif
                <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                   class="text-sm font-bold transition inline-flex items-center space-x-1"
                   style="color: #3B82F6;">
                    <span>Demander ce service</span>
                    <span>→</span>
                </a>
            </div>
            @empty
            <p class="text-center col-span-3 py-10" style="color: #475569;">
                Aucun service pour le moment.
            </p>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('services.index') }}" class="btn-primary">
                <span>Tous les services</span>
                <span>→</span>
            </a>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 3 : POURQUOI NOUS (dynamique) --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if(!empty($pourquoiNous))
<section class="py-16 sm:py-20 px-4" style="background-color: #111827;">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #3B82F6;">
                Pourquoi {{ $siteNom }} ?
            </p>
            <h2 class="text-section">
                L'excellence au cœur de chaque projet
            </h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($pourquoiNous as $argument)
            <div class="text-center p-5 rounded-2xl transition"
                 style="background-color: rgba(26, 34, 53, 0.50); border: 1px solid #2A3552;">
                <div class="text-3xl mb-3">{{ $argument['icone'] ?? '✅' }}</div>
                <h3 class="font-bold text-sm mb-1" style="color: #F1F5F9;">
                    {{ $argument['titre'] ?? '' }}
                </h3>
                <p class="text-xs" style="color: #64748B;">
                    {{ $argument['description'] ?? '' }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 4 : NOS FORMATIONS (déjà dynamique) --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<section class="py-16 sm:py-20 px-4" style="background-color: #0F172A;">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #10B981;">
                Nos Formations
            </p>
            <h2 class="text-section">Apprenez, progressez, réussissez</h2>
            <p class="mt-3 text-lg" style="color: #64748B;">
                En ligne et en présentiel, pour tous les niveaux
            </p>
        </div>

        <div class="grid-responsive-3">
            @forelse($formations as $formation)
            <div class="edc-card overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/'.$formation->image) }}" alt="{{ $formation->titre }}" class="w-full h-44 object-cover">
                @else
                <div class="w-full h-44 flex items-center justify-center" style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
                    <span class="text-5xl">🎓</span>
                </div>
                @endif

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="badge badge-green text-xs">
                            {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? '—' }}
                        </span>
                        @if($formation->duree)
                        <span class="text-xs" style="color: #475569;">⏱ {{ $formation->duree }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold mb-2" style="color: #F1F5F9;">{{ $formation->titre }}</h3>
                    <p class="text-sm leading-relaxed mb-4" style="color: #64748B;">
                        {{ Str::limit($formation->description, 75) }}
                    </p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold" style="color: #60A5FA;">
                            {{ $formation->prix ? number_format($formation->prix, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}
                        </span>
                    </div>
                    <a href="{{ route('formations.show', $formation) }}"
                       class="block text-center py-2.5 rounded-xl text-sm font-bold transition"
                       style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #ffffff;">
                        Voir la formation →
                    </a>
                </div>
            </div>
            @empty
            <p class="text-center col-span-3 py-10" style="color: #475569;">
                Aucune formation pour le moment.
            </p>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('formations.index') }}" class="btn-primary">
                <span>Voir toutes les formations</span>
                <span>→</span>
            </a>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 5 : TÉMOIGNAGES (déjà dynamique) --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if($temoignages->count())
<section class="py-16 sm:py-20 px-4" style="background-color: #111827;">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #FBBF24;">Avis Clients</p>
            <h2 class="text-section">Ce qu'ils disent de nous</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($temoignages as $t)
            <div class="rounded-2xl p-6" style="background-color: rgba(26, 34, 53, 0.60); border: 1px solid #2A3552;">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
                         style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #ffffff;">
                        {{ strtoupper(substr($t->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm" style="color: #F1F5F9;">
                            {{ $t->user->prenom }} {{ $t->user->nom }}
                        </p>
                        <p class="text-xs" style="color: #FBBF24;">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $t->note ? '★' : '☆' }}
                            @endfor
                        </p>
                    </div>
                </div>
                <p class="text-sm leading-relaxed italic" style="color: #94A3B8;">
                    "{{ $t->contenu }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 6 : CTA FINAL DYNAMIQUE --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<section class="cta-gradient text-white py-16 sm:py-20 px-4 text-center relative">
    <div class="max-w-2xl mx-auto relative z-10">
        <h2 class="text-section mb-4" style="color: #F1F5F9;">
            {{ $ctaTitre }}
        </h2>
        <p class="mb-8 text-lg" style="color: #94A3B8;">
            {{ $ctaSoustitre }}
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-primary">
                <span>✨</span>
                <span>Créer mon compte gratuitement</span>
            </a>

            @if($siteWhatsapp)
            <a href="{{ $whatsappUrl }}" target="_blank"
               class="btn-secondary"
               style="border-color: rgba(255,255,255,0.30); color: #F1F5F9;">
                <span>💬</span>
                <span>Contacter sur WhatsApp</span>
            </a>
            @endif
        </div>
    </div>
</section>

@endsection