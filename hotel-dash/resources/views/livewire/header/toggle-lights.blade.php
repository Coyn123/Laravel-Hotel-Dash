<div class="lights-wrapper relative">
    <button 
        wire:click="toggle"
        class="lights_toggle w-full h-full flex items-center justify-center p-2"
    >
        @if($theme === 'light')
            <span class="icon">☀️</span>
        @else
            <span class="icon">🌙</span>
        @endif
    </button>
</div>
