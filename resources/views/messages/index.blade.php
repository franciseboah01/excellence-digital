@extends(
    auth()->user()->hasRole('admin') ? 'layouts.admin' :
    (auth()->user()->hasRole('enseignant') ? 'layouts.enseignant' : 'layouts.client')
)
@section('title', 'Messagerie')
@section('page_title', '💬 Messagerie')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="min-height: 60vh;">

    {{-- LISTE CONVERSATIONS --}}
    <div class="edc-card overflow-hidden">
        <div class="px-5 py-4 flex justify-between items-center" style="border-bottom: 1px solid var(--edc-border);">
            <h2 class="font-bold" style="color: var(--edc-text-primary);">💬 Conversations</h2>
            <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                class="btn-primary btn-xs">
                ✏️ Nouveau
            </button>
        </div>

        <div class="divide-y" style="border-color: var(--edc-border);">
            @forelse($conversationsData as $conv)
            <a href="{{ route('messages.conversation', $conv['interlocuteur']) }}"
                class="flex items-center space-x-3 px-5 py-4 transition"
                style="{{ $conv['non_lus'] > 0 ? 'background-color: rgba(59,130,246,0.06);' : '' }}"
                onmouseover="this.style.backgroundColor='{{ $conv['non_lus'] > 0 ? 'rgba(59,130,246,0.10)' : 'rgba(255,255,255,0.03)' }}'"
                onmouseout="this.style.backgroundColor='{{ $conv['non_lus'] > 0 ? 'rgba(59,130,246,0.06)' : 'transparent' }}'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr($conv['interlocuteur']->prenom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-sm truncate" style="color: var(--edc-text-primary);">
                            {{ $conv['interlocuteur']->prenom }} {{ $conv['interlocuteur']->nom }}
                        </p>
                        <span class="text-xs flex-shrink-0 ml-2" style="color: var(--edc-text-muted);">
                            {{ $conv['dernier_message']?->created_at->format('H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-0.5">
                        <p class="text-xs truncate" style="color: var(--edc-text-muted);">
                            {{ \Str::limit($conv['dernier_message']?->contenu, 35) }}
                        </p>
                        @if($conv['non_lus'] > 0)
                        <span class="ml-2 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold flex-shrink-0"
                            style="background-color: var(--edc-primary);">
                            {{ $conv['non_lus'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-12" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">💬</p>
                <p class="text-sm">Aucune conversation.</p>
                <button onclick="document.getElementById('newConvModal').classList.remove('hidden')"
                    class="mt-3 text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">
                    Démarrer une conversation
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ZONE VIDE --}}
    <div class="lg:col-span-2 edc-card flex items-center justify-center">
        <div class="text-center py-16" style="color: var(--edc-text-muted);">
            <p class="text-6xl mb-4">💬</p>
            <p class="font-medium text-lg" style="color: var(--edc-text-primary);">Sélectionnez une conversation</p>
            <p class="text-sm mt-2">ou démarrez-en une nouvelle</p>
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
@endsection