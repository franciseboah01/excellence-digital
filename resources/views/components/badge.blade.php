@props(['color' => 'blue', 'text'])

<span class="badge badge-{{ $color }}">{{ $text ?? $slot }}</span>