<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>@yield('title', 'Hospitality Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Global styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Page-specific styles --}}
    @stack('styles')

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body class="">

    {{-- Global header/nav could go here if you want it on every page --}}
    @includeWhen(View::exists('partials.nav'), 'partials.nav')

    <main class="w-full">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    {{-- Global footer --}}
    @includeWhen(View::exists('partials.footer'), 'partials.footer')

    {{-- Livewire scripts --}}
    @livewireScripts

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
