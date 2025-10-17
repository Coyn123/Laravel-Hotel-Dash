@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-wrapper">
    <header class="mb-6">
        @if (auth()->check())
        <div class="header-inner grid grid-cols-3 items-center">

            <div class="title justify-self-start font-bold text-lg">
                Coyner Hospitality Dashboard
            </div>

            <div class="name-display text-center font-medium">
                {{ auth()->user()->name }}'s Dashboard
            </div>

            <div class="logout_area justify-self-end">
                @livewire('header.login-logout')
            </div>
        </div>
        @else
        <div class="header-inner grid grid-cols-3 items-center">
            <div class="title justify-self-start font-bold text-lg">
                Coyner Hospitality Dashboard
            </div>
        </div>
        @endif
    </header>

    @if (auth()->check())   
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @livewire('floors-view')
            </div>
            <div>
                @livewire('room-board-view')
            </div>
            <div>
                @livewire('property-board-view')
            </div>
        </div>
    @else
        @livewire('auth.login')
    @endif
</div>
@endsection
