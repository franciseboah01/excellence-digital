@php
    $siteNom      = \App\Models\Configuration::get('site_nom', 'Excellence Digital Center');
    $siteWhatsapp = \App\Models\Configuration::get('site_whatsapp', '2250748746140');
    
    $initiales = collect(explode(' ', $siteNom))
        ->map(fn($mot) => strtoupper(substr($mot, 0, 1)))
        ->take(3)
        ->implode('');
    $initiales = $initiales ?: 'EDC';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteNom }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%233B82F6'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-size='40' font-weight='bold' fill='white'>{{ $initiales }}</text></svg>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col justify-center items-center px-4 py-8"
    style="background-color: #0B0F1A;">

    <div class="w-full max-w-md rounded-2xl shadow-2xl p-6 sm:p-8"
        style="background-color: #111827; border: 1px solid #2A3552;">
        {{ $slot }}
    </div>

</body>
</html>