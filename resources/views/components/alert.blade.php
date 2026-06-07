@props(['type' => 'info', 'message' => ''])

@php
    $classes = match($type) {
        'success' => 'alert-success',
        'error'   => 'alert-error',
        'warning' => 'alert-warning',
        default   => 'alert-info',
    };
    $icon = match($type) {
        'success' => '✅',
        'error'   => '❌',
        'warning' => '⚠️',
        default   => 'ℹ️',
    };
@endphp

<div class="alert {{ $classes }}" role="alert">
    <span>{{ $icon }}</span>
    <span>{{ $message ?: $slot }}</span>
</div>