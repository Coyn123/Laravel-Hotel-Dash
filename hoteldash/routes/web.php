<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Dashboard page
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Handle setup form submission on first run
Route::post('/setup', [DashboardController::class, 'storeSetup'])->name('setup.store');
