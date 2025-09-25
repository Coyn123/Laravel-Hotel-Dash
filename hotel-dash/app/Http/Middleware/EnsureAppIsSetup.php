<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
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
    
        // to setup if not complete
        if (! $isSetupComplete && ! $request->is('setup')) {
            return redirect()->route('setup');
        }
    
        // to dashboard if setup is complete and not already there
        if ($isSetupComplete && ! $request->is('dashboard')) {
            return redirect()->route('dashboard');
        }
    
        // allow dashboard route to pass through
        return $next($request);
    }
           
}
