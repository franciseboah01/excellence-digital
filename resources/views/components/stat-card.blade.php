@props(['value', 'label', 'color' => 'blue', 'icon' => '📊'])

<div class="stat-card border-{{ $color }}-500">
    <p class="text-3xl font-extrabold text-{{ $color }}-700">{{ $value }}</p>
    <p class="text-gray-500 text-sm mt-1">{{ $icon }} {{ $label }}</p>
</div>