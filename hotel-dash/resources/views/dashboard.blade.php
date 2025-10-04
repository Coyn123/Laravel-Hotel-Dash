@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/css/main_style.css'])
@endpush

@section('content')
<div class="dashboard-wrapper">
    {{-- Header --}}
    <header class="mb-6">
        <div class="header-inner flex items-center justify-between">
            <div class="brand flex items-center space-x-2">
                <div class="logo font-bold text-lg" aria-hidden="true">Coyner</div>
                <div class="title text-xl">Hospitality Dashboard</div>
            </div>
            <div class="controls ml-auto">
                <div class="notification-wrapper relative">
                    <button class="notification-btn"
                            id="notificationBtn"
                            aria-label="View notifications"
                            aria-expanded="false"
                            aria-controls="notificationBox">
                        🔔
                    </button>
                    <div class="notification-box absolute right-0 mt-2 w-64 bg-white shadow-lg rounded hidden"
                         id="notificationBox">
                        <h4 class="notification-title font-semibold p-2 border-b">Notifications</h4>
                        <div class="notification-content p-2" id="notificationContent">
                            <p>Loading…</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        @livewire('floors-view')
    </div>
    <div>
        @livewire('room-board')
    </div>
    </div>
</div>
@endsection