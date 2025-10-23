<button 
    wire:click="toggle"
    type="button"
    class="p-2 rounded-md transition-colors"
    aria-label="Toggle light/dark mode"
>
    @if($theme === 'light')
        ☀️
    @else
        🌙
    @endif
</button>
