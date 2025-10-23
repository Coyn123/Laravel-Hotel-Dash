<?php

namespace App\Livewire\Header;

use Livewire\Component;

class ToggleLights extends Component
{
    public string $theme = 'light';

    public function mount()
    {
        // Restore from session or default
        $this->theme = session('theme', 'light');
    }

    public function toggle()
    {
        $this->theme = $this->theme === 'dark' ? 'light' : 'dark';
        session(['theme' => $this->theme]);
        redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.header.toggle-lights')->layout('layouts.app');
    }
}
