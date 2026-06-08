@extends('layouts.public')
@section('title', 'Accueil — Excellence Digital Center')

@section('content')

{{-- ============================================================
     SECTION 1 : HERO
     Section principale en pleine largeur avec un gradient
     identique à la section CTA final, un effet de lumière
     subtil, les stats clés et deux boutons d'appel à l'action.
     ============================================================ --}}

<section class="relative text-white py-20 sm:py-28 px-4 overflow-hidden">

    {{-- Image 16:9 en arrière-plan --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/edc-banner.png') }}"
             alt="Excellence Digital Center"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'">
        {{-- Overlay sombre pour lisibilité du texte --}}
        <div class="absolute inset-0" style="background: rgba(11, 15, 26, 0.80);"></div>
    </div>

    <div class="max-w-5xl mx-auto text-center relative z-10">

        {{-- Titre principal avec effet gradient --}}
        <h1 class="text-hero mb-6">
            <span class="text-gradient">Former</span> •
            <span class="text-gradient">Créer</span> •
            <span class="text-gradient">Réussir</span>
        </h1>

        {{-- Description --}}
        <p class="text-base sm:text-lg max-w-2xl mx-auto mb-10 leading-relaxed"
           style="color: #94A3B8;">
            Votre centre digital de référence à Korhogo / Sirasso.
            Services bureautiques, développement web, design graphique
            et formations pratiques pour tous les niveaux.
        </p>

        {{-- Boutons CTA --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('demande.form') }}"
               class="btn-primary animate-glow">
                <span>💼</span>
                <span>Demander un service</span>
            </a>
            <a href="{{ route('formations.index') }}"
               class="btn-secondary">
                <span>🎓</span>
                <span>Voir les formations</span>
            </a>
        </div>

        {{-- Stats : grille de 4 chiffres clés --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-16">
            @foreach([
                ['500+', 'Clients satisfaits'],
                ['10+', 'Formations actives'],
                ['20+', 'Services proposés'],
                ['3 ans', "D'expertise"],
            ] as $s)
            <div class="rounded-xl py-5 px-3"
                 style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.10);">
                <p class="text-2xl sm:text-3xl font-black" style="color: #F1F5F9;">
                    {{ $s[0] }}
                </p>
                <p class="text-xs sm:text-sm mt-1" style="color: #64748B;">
                    {{ $s[1] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     SECTION 1B : CARROUSEL D'IMAGES 9:16
     Défilement automatique toutes les 2 secondes.
     Affiche des images au format portrait (9:16) sur desktop
     comme sur mobile. Utilise un scroll horizontal natif
     avec snap CSS pour un rendu fluide.
     ============================================================ --}}
<section class="py-12 sm:py-16 px-4" style="background-color: var(--edc-bg-deep);">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #3B82F6;">
                Galerie
            </p>
            <h2 class="text-section">Découvrez notre univers</h2>
        </div>

        {{-- Carrousel avec effet "coverflow" : 3 visibles, celui du centre plus grand --}}
        <div class="relative"
             x-data="{
                images: ['galerie-1.png','galerie-2.png','galerie-3.png','galerie-4.png'],
                current: 0,
                total: 4,
                init() {
                    setInterval(() => {
                        this.next();
                    }, 2500);
                },
                next() {
                    this.current = (this.current + 1) % this.total;
                },
                prev() {
                    this.current = (this.current - 1 + this.total) % this.total;
                },
                getClass(index) {
                    const diff = (index - this.current + this.total) % this.total;
                    if (diff === 0) return 'z-20 scale-100 opacity-100';
                    if (diff === 1 || diff === this.total - 1) return 'z-10 scale-90 opacity-60';
                    return 'z-0 scale-75 opacity-0 hidden';
                }
             }">

            {{-- Images --}}
            <div class="flex items-center justify-center gap-2 sm:gap-4 min-h-[380px] sm:min-h-[450px]">
                <template x-for="(img, index) in images" :key="index">
                    <div class="flex-shrink-0 transition-all duration-500 ease-in-out rounded-2xl overflow-hidden shadow-2xl"
                         :class="getClass(index)"
                         style="width: clamp(180px, 25vw, 280px); aspect-ratio: 9/16; border: 1px solid var(--edc-border);">
                        <img :src="'{{ asset('images') }}/' + img"
                             :alt="'Galerie EDC ' + (index + 1)"
                             class="w-full h-full object-cover"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\' style=\'background:linear-gradient(135deg,#1e3a8a,#2563eb);\'><span class=\'text-4xl\'>🖼️</span></div>'">
                    </div>
                </template>
            </div>

            {{-- Flèches --}}
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

            {{-- Dots --}}
            <div class="flex justify-center space-x-2 mt-6">
                <template x-for="i in total" :key="i">
                    <button @click="current = i - 1"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                        :class="current === i - 1 ? 'bg-blue-500 w-8' : 'bg-gray-600 hover:bg-gray-400'">
                    </button>
                </template>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 2 : NOS SERVICES
     Grille de cartes présentant les services digitaux.
     Chaque carte a une icône, un titre, une description
     tronquée et un prix si disponible.
     ============================================================ --}}
<section class="py-16 sm:py-20 px-4" style="background-color: #0F172A;">
    <div class="max-w-5xl mx-auto">

        {{-- En-tête de section --}}
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3"
               style="color: #3B82F6;">
                Nos Services
            </p>
            <h2 class="text-section">
                Des solutions pour tous vos besoins
            </h2>
            <p class="mt-3 text-lg" style="color: #64748B;">
                Bureautique, design, web et développement mobile
            </p>
        </div>

        {{-- Grille de cartes services --}}
        <div class="grid-responsive-3">
            @forelse($services as $service)
            <div class="edc-card p-6">
                {{-- Icône du service --}}
                <div class="text-3xl mb-4">{{ $service->icone ?? '⚙️' }}</div>

                {{-- Titre --}}
                <h3 class="font-bold mb-2" style="color: #F1F5F9;">
                    {{ $service->titre }}
                </h3>

                {{-- Description tronquée --}}
                <p class="text-sm leading-relaxed mb-4" style="color: #64748B;">
                    {{ Str::limit($service->description, 85) }}
                </p>

                {{-- Prix --}}
                @if($service->prix)
                <p class="text-sm font-bold mb-3" style="color: #60A5FA;">
                    Dès {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                </p>
                @endif

                {{-- Lien vers le formulaire de demande --}}
                <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                   class="text-sm font-bold transition inline-flex items-center space-x-1"
                   style="color: #3B82F6;"
                   onmouseover="this.style.color='#60A5FA'"
                   onmouseout="this.style.color='#3B82F6'">
                    <span>Demander ce service</span>
                    <span>→</span>
                </a>
            </div>
            @empty
            {{-- État vide : aucun service disponible --}}
            <p class="text-center col-span-3 py-10" style="color: #475569;">
                Aucun service pour le moment.
            </p>
            @endforelse
        </div>

        {{-- Bouton "Tous les services" --}}
        <div class="text-center mt-10">
            <a href="{{ route('services.index') }}"
               class="btn-primary">
                <span>Tous les services</span>
                <span>→</span>
            </a>
        </div>
    </div>
</section>


{{-- ============================================================
     SECTION 3 : POURQUOI EDC
     4 arguments de confiance en grille responsive.
     Chaque argument a une icône, un titre et une description.
     ============================================================ --}}
<section class="py-16 sm:py-20 px-4" style="background-color: #111827;">
    <div class="max-w-5xl mx-auto">

        {{-- En-tête --}}
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3"
               style="color: #3B82F6;">
                Pourquoi EDC ?
            </p>
            <h2 class="text-section">
                L'excellence au cœur de chaque projet
            </h2>
        </div>

        {{-- Grille 4 colonnes (2 sur mobile) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['⚡', 'Rapidité', 'Délais respectés chaque fois'],
                ['🎯', 'Qualité', 'Travail professionnel garanti'],
                ['🤝', 'Suivi', 'Accompagnement personnalisé'],
                ['💰', 'Prix juste', 'Accessible à tous les budgets'],
            ] as $v)
            <div class="text-center p-5 rounded-2xl transition"
                 style="background-color: rgba(26, 34, 53, 0.50); border: 1px solid #2A3552;">
                <div class="text-3xl mb-3">{{ $v[0] }}</div>
                <h3 class="font-bold text-sm mb-1" style="color: #F1F5F9;">
                    {{ $v[1] }}
                </h3>
                <p class="text-xs" style="color: #64748B;">
                    {{ $v[2] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     SECTION 4 : NOS FORMATIONS
     Grille de cartes formation avec image miniature,
     badge de niveau, durée, titre, description et CTA.
     ============================================================ --}}
<section class="py-16 sm:py-20 px-4" style="background-color: #0F172A;">
    <div class="max-w-5xl mx-auto">

        {{-- En-tête --}}
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3"
               style="color: #10B981;">
                Nos Formations
            </p>
            <h2 class="text-section">
                Apprenez, progressez, réussissez
            </h2>
            <p class="mt-3 text-lg" style="color: #64748B;">
                En ligne et en présentiel, pour tous les niveaux
            </p>
        </div>

        {{-- Grille de cartes formations --}}
        <div class="grid-responsive-3">
            @forelse($formations as $formation)
            <div class="edc-card overflow-hidden">

                {{-- Image ou placeholder gradient --}}
                @if($formation->image)
                <img src="{{ asset('storage/'.$formation->image) }}"
                     alt="{{ $formation->titre }}"
                     class="w-full h-44 object-cover">
                @else
                <div class="w-full h-44 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
                    <span class="text-5xl">🎓</span>
                </div>
                @endif

                <div class="p-5">
                    {{-- Ligne de métadonnées : niveau + durée --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="badge badge-green">
                            {{ ucfirst($formation->niveau) }}
                        </span>
                        @if($formation->duree)
                        <span class="text-xs" style="color: #475569;">
                            ⏱ {{ $formation->duree }}
                        </span>
                        @endif
                    </div>

                    {{-- Titre --}}
                    <h3 class="font-bold mb-2" style="color: #F1F5F9;">
                        {{ $formation->titre }}
                    </h3>

                    {{-- Description tronquée --}}
                    <p class="text-sm leading-relaxed mb-4" style="color: #64748B;">
                        {{ Str::limit($formation->description, 75) }}
                    </p>

                    {{-- Bouton d'action --}}
                    <a href="{{ route('formations.show', $formation) }}"
                       class="block text-center py-2.5 rounded-xl text-sm font-bold transition"
                       style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #ffffff;"
                       onmouseover="this.style.boxShadow='0 4px 16px rgba(59,130,246,0.35)'"
                       onmouseout="this.style.boxShadow='none'">
                        Voir la formation →
                    </a>
                </div>
            </div>
            @empty
            {{-- État vide --}}
            <p class="text-center col-span-3 py-10" style="color: #475569;">
                Aucune formation pour le moment.
            </p>
            @endforelse
        </div>
    </div>
</section>


{{-- ============================================================
     SECTION 5 : TÉMOIGNAGES CLIENTS
     Affichés uniquement s'il y en a. Grille de 2 colonnes
     avec avatar, nom, note étoilée et citation.
     ============================================================ --}}
@if($temoignages->count())
<section class="py-16 sm:py-20 px-4" style="background-color: #111827;">
    <div class="max-w-4xl mx-auto">

        {{-- En-tête --}}
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-3"
               style="color: #FBBF24;">
                Avis Clients
            </p>
            <h2 class="text-section">
                Ce qu'ils disent de nous
            </h2>
        </div>

        {{-- Grille de témoignages --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($temoignages as $t)
            <div class="rounded-2xl p-6"
                 style="background-color: rgba(26, 34, 53, 0.60); border: 1px solid #2A3552;">

                {{-- Ligne utilisateur : avatar + nom + note --}}
                <div class="flex items-center gap-3 mb-4">
                    {{-- Avatar avec initiale --}}
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                font-bold text-sm flex-shrink-0"
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

                {{-- Citation --}}
                <p class="text-sm leading-relaxed italic" style="color: #94A3B8;">
                    "{{ $t->contenu }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ============================================================
     SECTION 6 : CTA FINAL (Call-To-Action de conversion)
     Section marquante avec gradient et lumière subtile
     pour inciter à l'inscription ou au contact.
     ============================================================ --}}
<section class="cta-gradient text-white py-16 sm:py-20 px-4 text-center relative">
    <div class="max-w-2xl mx-auto relative z-10">
        <h2 class="text-section mb-4" style="color: #F1F5F9;">
            Prêt à démarrer ?
        </h2>
        <p class="mb-8 text-lg" style="color: #94A3B8;">
            Rejoignez les centaines de personnes qui nous font confiance
            pour leurs projets digitaux et leur montée en compétences.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            {{-- CTA principal : inscription --}}
            <a href="{{ route('register') }}"
               class="btn-primary">
                <span>✨</span>
                <span>Créer mon compte gratuitement</span>
            </a>

            {{-- CTA secondaire : WhatsApp --}}
            <a href="https://wa.me/2250748746140" target="_blank"
               class="btn-secondary"
               style="border-color: rgba(255,255,255,0.30); color: #F1F5F9;">
                <span>💬</span>
                <span>Contacter sur WhatsApp</span>
            </a>
        </div>
    </div>
</section>

@endsection