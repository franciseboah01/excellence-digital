@extends('layouts.public')
@section('title', 'Services — Excellence Digital Center')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900">Nos Services</h1>
        <p class="text-gray-500 mt-3">Choisissez le service adapté à vos besoins</p>
    </div>

    @foreach($services as $categorie => $liste)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-blue-800 mb-6 border-b-2 border-blue-200 pb-2">
            @if($categorie == 'bureautique') 💼 Bureautique
            @elseif($categorie == 'design') 🌐 Digital & Design
            @else 💻 Développement Web & Mobile
            @endif
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($liste as $service)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100">
                <div class="text-4xl mb-3">{{ $service->icone ?? '⚙️' }}</div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">{{ $service->titre }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ $service->description }}</p>
                @if($service->prix)
                <p class="text-blue-700 font-semibold">À partir de {{ number_format($service->prix, 0, ',', ' ') }} FCFA</p>
                @endif
                <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                    class="inline-block mt-4 bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                    Demander ce service
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection