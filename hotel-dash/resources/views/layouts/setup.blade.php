<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Initial Setup' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite compiled CSS & JS --}}
    @vite(['resources/css/app.css'])

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">
    <div class="setup-wrapper max-w-3xl mx-auto my-8 p-6 bg-white rounded-lg shadow">
        {{ $slot }}
    </div>

    {{-- Livewire scripts --}}
    @livewireScripts
</body>
</html>
