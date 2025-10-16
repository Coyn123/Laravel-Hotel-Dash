<?php

namespace App\Livewire\Header;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginLogout extends Component
{
    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.header.login-logout');
    }
}
