<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Excellence Digital Center') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%233B82F6'/><text x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' fill='white' font-size='18' font-weight='bold'>E</text></svg>">
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