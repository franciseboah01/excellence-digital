@extends(
    auth()->user()->hasRole('admin') ? 'layouts.admin' :
    (auth()->user()->hasRole('enseignant') ? 'layouts.enseignant' : 'layouts.client')
)
@section('title', 'Conversation avec ' . $user->prenom)
@section('page_title', 'Messagerie')

@section('content')
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6" style="height: calc(100vh - 180px);">

    {{-- LISTE CONVERSATIONS --}}
    <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <h2 class="font-bold text-blue-900">💬 Conversations</h2>
            <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                class="text-xs bg-blue-800 text-white px-3 py-1.5 rounded-lg hover:bg-blue-900 transition">
                ✏️ Nouveau
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            @forelse($conversationsData as $conv)
            <a href="{{ route('messages.conversation', $conv['interlocuteur']) }}"
                class="flex items-center space-x-3 px-5 py-4 border-b border-gray-100 hover:bg-blue-50 transition
                {{ $conv['interlocuteur']->id === $user->id ? 'bg-blue-100 border-l-4 border-l-blue-700' : '' }}
                {{ $conv['non_lus'] > 0 && $conv['interlocuteur']->id !== $user->id ? 'bg-blue-50' : '' }}">
                <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($conv['interlocuteur']->prenom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-800 text-sm truncate">
                            {{ $conv['interlocuteur']->prenom }} {{ $conv['interlocuteur']->nom }}
                        </p>
                        <span class="text-xs text-gray-300 ml-2 flex-shrink-0">
                            {{ $conv['dernier_message']?->created_at->format('H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-0.5">
                        <p class="text-xs text-gray-400 truncate">
                            {{ \Str::limit($conv['dernier_message']?->contenu, 30) }}
                        </p>
                        @if($conv['non_lus'] > 0 && $conv['interlocuteur']->id !== $user->id)
                        <span class="ml-2 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold flex-shrink-0">
                            {{ $conv['non_lus'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">Aucune conversation.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ZONE CONVERSATION --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow flex flex-col overflow-hidden">

        {{-- Header conversation --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center space-x-3 flex-shrink-0 bg-gray-50">
            <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($user->prenom, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-gray-800">{{ $user->prenom }} {{ $user->nom }}</p>
                <p class="text-xs text-gray-400">
                    {{ ucfirst($user->getRoleNames()->first()) }} —
                    @if($user->statut === 'actif')
                    <span class="text-green-500">● En ligne</span>
                    @else
                    <span class="text-gray-300">● Hors ligne</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Messages --}}
        <div id="messagesContainer"
            class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
            @forelse($messages as $message)
            @php $estMoi = $message->expediteur_id === auth()->id(); @endphp
            <div class="flex {{ $estMoi ? 'justify-end' : 'justify-start' }}" id="msg-{{ $message->id }}">
                <div class="max-w-xs lg:max-w-md">
                    {{-- Bulle message --}}
                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                        {{ $estMoi
                            ? 'bg-blue-800 text-white rounded-br-none'
                            : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                        {{ $message->contenu }}
                    </div>
                    {{-- Heure + statut --}}
                    <div class="flex items-center space-x-1 mt-1
                        {{ $estMoi ? 'justify-end' : 'justify-start' }}">
                        <span class="text-xs text-gray-300">
                            {{ $message->created_at->format('d/m H:i') }}
                        </span>
                        @if($estMoi)
                        <span class="text-xs {{ $message->lu ? 'text-blue-500' : 'text-gray-300' }}">
                            {{ $message->lu ? '✓✓' : '✓' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">👋</p>
                <p class="text-sm">Démarrez la conversation !</p>
            </div>
            @endforelse
        </div>

        {{-- Formulaire envoi --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
            <form method="POST" action="{{ route('messages.envoyer') }}"
                id="messageForm" class="flex space-x-3">
                @csrf
                <input type="hidden" name="destinataire_id" value="{{ $user->id }}">
                <textarea name="contenu" id="messageInput" rows="1" required
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Écrivez votre message..."
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
                <button type="submit"
                    class="bg-blue-800 text-white px-5 py-3 rounded-xl font-semibold hover:bg-blue-900 transition flex-shrink-0">
                    📤
                </button>
            </form>
            <p class="text-xs text-gray-300 mt-1 ml-1">Appuyez sur Entrée pour envoyer</p>
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

@push('scripts')
<script>
    // Scroll automatique vers le bas
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;
</script>
@endpush
@endsection