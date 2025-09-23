import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import livewire from '@defstudio/vite-livewire-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: false, // disable Laravel's default Blade refresh
        }),
        livewire({
            refresh: ['resources/css/app.css'], // also refresh Tailwind/CSS
        }),
    ],
    server: {
        host: 'localhost', // force IPv4 instead of [::1]
        port: 5173,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
});
