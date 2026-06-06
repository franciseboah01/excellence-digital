@extends(
    auth()->user()->hasRole('admin') ? 'layouts.admin' :
    (auth()->user()->hasRole('enseignant') ? 'layouts.enseignant' : 'layouts.client')
)
@section('title', 'Messagerie')
@section('page_title', 'Messagerie')

@section('content')
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LISTE CONVERSATIONS --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-bold text-blue-900">💬 Conversations</h2>
            <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                class="text-xs bg-blue-800 text-white px-3 py-1.5 rounded-lg hover:bg-blue-900 transition">
                ✏️ Nouveau
            </button>
        </div>

        @forelse($conversationsData as $conv)
        <a href="{{ route('messages.conversation', $conv['interlocuteur']) }}"
            class="flex items-center space-x-3 px-5 py-4 border-b border-gray-100 hover:bg-blue-50 transition
            {{ $conv['non_lus'] > 0 ? 'bg-blue-50' : '' }}">
            <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($conv['interlocuteur']->prenom, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center">
                    <p class="font-semibold text-gray-800 text-sm truncate">
                        {{ $conv['interlocuteur']->prenom }} {{ $conv['interlocuteur']->nom }}
                    </p>
                    <span class="text-xs text-gray-300 flex-shrink-0 ml-2">
                        {{ $conv['dernier_message']?->created_at->format('H:i') }}
                    </span>
                </div>
                <div class="flex justify-between items-center mt-0.5">
                    <p class="text-xs text-gray-400 truncate">
                        {{ \Str::limit($conv['dernier_message']?->contenu, 35) }}
                    </p>
                    @if($conv['non_lus'] > 0)
                    <span class="ml-2 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold flex-shrink-0">
                        {{ $conv['non_lus'] }}
                    </span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-gray-400">
            <p class="text-4xl mb-3">💬</p>
            <p class="text-sm">Aucune conversation.</p>
            <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                class="mt-3 text-xs text-blue-600 hover:underline">
                Démarrer une conversation
            </button>
        </div>
        @endforelse
    </div>

    {{-- ZONE VIDE --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow flex items-center justify-center">
        <div class="text-center text-gray-400 py-16">
            <p class="text-6xl mb-4">💬</p>
            <p class="font-medium text-lg">Sélectionnez une conversation</p>
            <p class="text-sm mt-2">ou démarrez-en une nouvelle</p>
        </div>
    </div>
</div>

{{-- MODAL NOUVEAU MESSAGE --}}
<div id="newConvModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-blue-900 text-lg">✏️ Nouveau message</h3>
            <button onclick="document.getElementById('newConvModal').classList.add('hidden')"
                class="text-gray-400 hover:text-red-500 text-xl">✕</button>
        </div>
        <form method="POST" action="{{ route('messages.envoyer') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Destinataire *</label>
                <select name="destinataire_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir --</option>
                    @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">
                        {{ $contact->prenom }} {{ $contact->nom }}
                        ({{ $contact->getRoleNames()->first() }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
                <textarea name="contenu" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Écrivez votre message..."></textarea>
            </div>
            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                📤 Envoyer
            </button>
        </form>
    </div>
</div>
@endsection