@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-wrapper">
    {{-- Header --}}
    <header class="mb-6">
        <div class="header-inner flex items-center">
            <div class="brand flex items-center space-x-2">
                <div class="logo font-bold text-lg" aria-hidden="true">Coyner</div>
                <div class="title text-xl">Hospitality Dashboard</div>
            </div>
            {{-- Lame auth check, is the name in the session cookies? low security--}}
            @if (session('name'))
            <div class="controls ml-auto flex items-center">
                @livewire('header.notification-box')
            </div>
            <div class="name-display">{{ session('name') }}'s Dashboard </div>
            @endif
        </div> 
    </header>
 @auth
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @livewire('floors-view')
            </div>
            <div>
                @livewire('room-board')
            </div>
        </div>
    @else
        @livewire('auth.login')
    @endauth
</div>
@endsection