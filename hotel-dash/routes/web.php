<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Dashboard;
use App\Livewire\Setup;
use App\Livewire\RoomBoard;

// Dashboard page
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
// Handle setup form submission on first run
Route::get('/setup', Setup::class)->name('setup');
