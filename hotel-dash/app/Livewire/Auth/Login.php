<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $mode = 'login'; // 'login' or 'register'

    // Login fields
    public $login_email = '';
    public $login_password = '';
    public $remember = false;

    // Registration fields
    public $register_name = '';
    public $register_email = '';
    public $register_password = '';
    public $register_password_confirmation = '';

    public function rules()
    {
        return $this->mode === 'login'
            ? [
                'login_email' => 'required|email',
                'login_password' => 'required|min:6',
            ]
            : [
                'register_name' => 'required|string|max:255',
                'register_email' => 'required|email|unique:users,email',
                'register_password' => 'required|min:6|same:register_password_confirmation',
            ];
    }
    
    public function login()
    {
        $credentials = $this->validate([
            'login_email'    => ['required', 'email'],
            'login_password' => ['required', 'string'],
        ]);
    
        if (Auth::attempt(
            ['email' => $credentials['login_email'], 'password' => $credentials['login_password']],
            $this->remember
        )) {
            request()->session()->regenerate();
            $this->dispatch('user-logged-in');
            return redirect()->route('dashboard');
        }
        throw ValidationException::withMessages([
            'login_email' => __('auth.failed'),
        ]);
    }
    

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name'     => $this->register_name,
            'email'    => $this->register_email,
            'password' => Hash::make($this->register_password),
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->dispatch('user-registered');
        return redirect()->route('dashboard');
    }

    public function switchMode($mode)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->mode = $mode;
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
