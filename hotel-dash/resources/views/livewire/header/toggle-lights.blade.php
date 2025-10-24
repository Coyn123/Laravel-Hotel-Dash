<div class="lights-wrapper relative">
    <button 
        wire:click="toggle"
        class="lights_toggle"
    >
        @if($theme === 'light')
            <span class="icon">☀️</span>
        @else
            <span class="icon">🌙</span>
        @endif
    </button>
</div>
