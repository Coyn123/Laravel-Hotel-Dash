<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class EnsureAppIsSetup
{
    public function handle(Request $request, Closure $next)
    {
        $isSetupComplete = DB::table('configurations')->count() >= 1;
        // Allow Livewire assets, message endpoints, and Vite HMR to pass through
        if (
            $request->is('livewire/*') ||
            $request->is('vendor/livewire/*') ||
            $request->is('@vite/*')
        ) {
            return $next($request);
        }
    
        if (! $isSetupComplete && ! $request->is('setup')) {
            return redirect()->route('setup');
        }
    
        if ($isSetupComplete && ($request->is('dashbord') || $request->is('/'))) {
            return redirect()->route('dashboard');
        }
    
        return $next($request);
    }    
}
