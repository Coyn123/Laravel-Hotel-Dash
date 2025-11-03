@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-wrapper">
    <header class="mb-6">
        @if (auth()->check())
        <div class="header-inner">
            <div class="title font-bold text-lg md:text-xl">
                Coyner Hospitality Dashboard
            </div>

            <div class="name-display text-center font-medium">
                {{ auth()->user()->name }}
            </div>

            <div class="controls_area">
                <div class="notification-btn">
                    @livewire('header.notification-box')
                </div>
                <div class="lights_toggle">
                    @livewire('header.toggle-lights')
                </div>
                <div class="logout_button">
                    @livewire('header.login-logout')
                </div>
            </div>
        </div>
        @else
        <div class="header-inner">
            <div class="title font-bold text-lg">
                Coyner Hospitality Dashboard
            </div>
        </div>
        @endif
    </header>

    @if (auth()->check())
    <div class="dashboard">
        {{-- 🏢 Left Panel: Floors --}}
        <div class="floor-panel">
            @livewire('floors-view')
        </div>

        {{-- 🏨 Middle Panel: Rooms --}}
        <div class="message-panel room-panel">
            @livewire('room-board-view')
        </div>

        {{-- 🏘️ Right Panel: Properties --}}
        <div class="message-panel property-panel" id="notif-target">
            @livewire('property-board-view')
        </div>
    </div>
    @else
        @livewire('auth.login')
    @endif
</div>
@endsection
