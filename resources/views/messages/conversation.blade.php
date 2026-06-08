@extends(
    auth()->user()->hasRole('admin') ? 'layouts.admin' :
    (auth()->user()->hasRole('enseignant') ? 'layouts.enseignant' : 'layouts.client')
)
@section('title', 'Conversation avec ' . $user->prenom)
@section('page_title', '💬 Messagerie')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="height: calc(100vh - 180px);">

    {{-- LISTE CONVERSATIONS --}}
    <div class="edc-card overflow-hidden flex flex-col">
        <div class="px-5 py-4 flex justify-between items-center flex-shrink-0" style="border-bottom: 1px solid var(--edc-border);">
            <h2 class="font-bold" style="color: var(--edc-text-primary);">💬 Conversations</h2>
            <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                class="btn-primary btn-xs">
                ✏️ Nouveau
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            @forelse($conversationsData as $conv)
            <a href="{{ route('messages.conversation', $conv['interlocuteur']) }}"
                class="flex items-center space-x-3 px-5 py-4 transition"
                style="border-bottom: 1px solid var(--edc-border);
                    {{ $conv['interlocuteur']->id === $user->id ? 'background-color: rgba(59,130,246,0.10); border-left: 3px solid var(--edc-primary);' : '' }}
                    {{ $conv['non_lus'] > 0 && $conv['interlocuteur']->id !== $user->id ? 'background-color: rgba(59,130,246,0.04);' : '' }}"
                onmouseover="if(this.style.borderLeft.indexOf('3px')===-1){this.style.backgroundColor='rgba(255,255,255,0.03)'}"
                onmouseout="if(this.style.borderLeft.indexOf('3px')===-1){this.style.backgroundColor='transparent'}">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr($conv['interlocuteur']->prenom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-sm truncate" style="color: var(--edc-text-primary);">
                            {{ $conv['interlocuteur']->prenom }} {{ $conv['interlocuteur']->nom }}
                        </p>
                        <span class="text-xs ml-2 flex-shrink-0" style="color: var(--edc-text-muted);">
                            {{ $conv['dernier_message']?->created_at->format('H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-0.5">
                        <p class="text-xs truncate" style="color: var(--edc-text-muted);">
                            {{ \Str::limit($conv['dernier_message']?->contenu, 30) }}
                        </p>
                        @if($conv['non_lus'] > 0 && $conv['interlocuteur']->id !== $user->id)
                        <span class="ml-2 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold flex-shrink-0"
                            style="background-color: var(--edc-primary);">
                            {{ $conv['non_lus'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-8" style="color: var(--edc-text-muted);">
                <p class="text-sm">Aucune conversation.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ZONE CONVERSATION --}}
    <div class="lg:col-span-2 edc-card flex flex-col overflow-hidden">

        {{-- Header conversation --}}
        <div class="px-6 py-4 flex items-center space-x-3 flex-shrink-0"
            style="border-bottom: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                {{ strtoupper(substr($user->prenom, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold" style="color: var(--edc-text-primary);">{{ $user->prenom }} {{ $user->nom }}</p>
                <p class="text-xs" style="color: var(--edc-text-muted);">
                    {{ ucfirst($user->getRoleNames()->first()) }} —
                    @if($user->statut === 'actif')
                    <span style="color: var(--edc-secondary);">● En ligne</span>
                    @else
                    <span style="color: var(--edc-text-muted);">● Hors ligne</span>
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
                    {{-- Bulle --}}
                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                        {{ $estMoi ? 'message-bubble-sent' : 'message-bubble-received' }}">
                        {{ $message->contenu }}
                    </div>
                    {{-- Heure + statut --}}
                    <div class="flex items-center space-x-1 mt-1 {{ $estMoi ? 'justify-end' : 'justify-start' }}">
                        <span class="text-xs" style="color: var(--edc-text-muted);">
                            {{ $message->created_at->format('d/m H:i') }}
                        </span>
                        @if($estMoi)
                        <span class="text-xs" style="color: {{ $message->lu ? 'var(--edc-primary-light)' : 'var(--edc-text-muted)' }};">
                            {{ $message->lu ? '✓✓' : '✓' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">👋</p>
                <p class="text-sm">Démarrez la conversation !</p>
            </div>
            @endforelse
        </div>

        {{-- Formulaire envoi --}}
        <div class="px-6 py-4 flex-shrink-0" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
            <form method="POST" action="{{ route('messages.envoyer') }}"
                id="messageForm" class="flex space-x-3">
                @csrf
                <input type="hidden" name="destinataire_id" value="{{ $user->id }}">
                <textarea name="contenu" id="messageInput" rows="1" required
                    class="edc-input flex-1 resize-none"
                    placeholder="Écrivez votre message..."
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
                <button type="submit" class="btn-primary flex-shrink-0" style="padding: 12px 18px;">
                    📤
                </button>
            </form>
            <p class="text-xs mt-1 ml-1" style="color: var(--edc-text-muted);">Appuyez sur Entrée pour envoyer</p>
        </div>
    </div>
</div>

{{-- MODAL NOUVEAU MESSAGE --}}
<div id="newConvModal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.7);">
    <div class="edc-card w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-lg" style="color: var(--edc-text-primary);">✏️ Nouveau message</h3>
            <button onclick="document.getElementById('newConvModal').classList.add('hidden')"
                class="text-xl transition"
                style="color: var(--edc-text-muted);"
                onmouseover="this.style.color='#EF4444'"
                onmouseout="this.style.color='#64748B'">✕</button>
        </div>
        <form method="POST" action="{{ route('messages.envoyer') }}" class="space-y-4">
            @csrf
            <div>
                <label class="edc-label">Destinataire *</label>
                <select name="destinataire_id" required class="edc-select">
                    <option value="">-- Choisir --</option>
                    @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">
                        {{ $contact->prenom }} {{ $contact->nom }}
                        ({{ $contact->getRoleNames()->first() }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="edc-label">Message *</label>
                <textarea name="contenu" rows="4" required class="edc-input"
                    placeholder="Écrivez votre message..."></textarea>
            </div>
            <button type="submit" class="btn-primary w-full">
                📤 Envoyer
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;
</script>
@endpush
@endsection