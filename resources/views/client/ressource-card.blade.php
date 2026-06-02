@php
    $config = match($ressource->type) {
        'pdf'      => ['bg-red-50 border-red-200',    '📄', 'text-red-700'],
        'ebook'    => ['bg-purple-50 border-purple-200', '📖', 'text-purple-700'],
        'lien'     => ['bg-green-50 border-green-200',  '🔗', 'text-green-700'],
        'video'    => ['bg-yellow-50 border-yellow-200','🎬', 'text-yellow-700'],
        'document' => ['bg-blue-50 border-blue-200',   '📝', 'text-blue-700'],
        default    => ['bg-gray-50 border-gray-200',   '📎', 'text-gray-700'],
    };
@endphp

<div class="border rounded-xl p-4 {{ $config[0] }} hover:shadow-md transition">
    <div class="flex items-start space-x-3">
        <span class="text-2xl">{{ $config[1] }}</span>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate">{{ $ressource->titre }}</p>
            @if($ressource->description)
            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($ressource->description, 60) }}</p>
            @endif
            <div class="mt-3">
                @if($ressource->type === 'lien' || $ressource->type === 'video')
                    <a href="{{ $ressource->lien_url }}" target="_blank"
                        class="inline-flex items-center text-xs font-medium {{ $config[2] }} hover:underline">
                        Ouvrir le lien →
                    </a>
                @else
                    {{-- Visionneuse PDF inline --}}
                    <button onclick="ouvrirPdf('{{ route('client.pdf', $ressource) }}')"
                        class="inline-flex items-center text-xs font-medium {{ $config[2] }} hover:underline">
                        📖 Lire le document
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>