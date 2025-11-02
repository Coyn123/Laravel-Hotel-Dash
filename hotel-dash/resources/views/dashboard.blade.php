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
        @else
        <div class="header-inner grid grid-cols-3 items-center">
            <div class="title justify-self-start font-bold text-lg">
                Coyner Hospitality Dashboard
            </div>
        </div>
        @endif
    </header>

    @if (auth()->check())   
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            @livewire('floors-view')
        </div>

        <div class="flex flex-col gap-4">
            <div>
                @livewire('room-board-view')
            </div>
        </div>

    <div class="flex flex-col gap-4">
        <div>
            <livewire:property-board-view id="notif-target" />
        </div>
    </div>


    @else
        @livewire('auth.login')
    @endif
</div>
@endsection
