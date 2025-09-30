<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureAppIsSetup
{
    public function handle($request, Closure $next)
    {
        $isSetupComplete = DB::table('properties_config')->count() >= 1; 
    
        // allow internal requests to pass through
        if (
            $request->is('livewire/*') ||
            $request->is('_debugbar/*') ||
            $request->is('vendor/*')
        ) {
            return $next($request);
        }
    
        // if not setup, force to setup page
        if (! $isSetupComplete && ! $request->is('setup')) {
            return redirect()->route('setup');
        }

        // if setup is complete, just allow everything
        return $next($request);
    }
}
