<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Hospitality Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite compiled CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body>
    {{-- If a Livewire slot exists, render it. Otherwise, fall back to Blade section --}}
    @isset($slot)
        {{ $slot }}
    @else
        @yield('content')
    @endisset

    {{-- Livewire scripts --}}
    @livewireScripts
</body>
</html>

