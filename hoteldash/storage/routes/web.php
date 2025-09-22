<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Dashboard;
use App\Livewire\Setup;

// Dashboard page
Route::get('/dashboard', Dashboard::class)->name('dashboard');
// Handle setup form submission on first run
Route::get('/setup', Setup::class)->name('setup');
