<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Dashboard;
use App\Livewire\Setup;
use App\Livewire\RoomBoard;

// Dashboard page
Route::get('/dashboard', Dashboard::class)->name('dashboard');
// Handle setup form submission on first run
Route::get('/setup', Setup::class)->name('setup');

Route::get('/panel/{property}/{floor}/{room}', RoomBoard::class)->name('room.board');