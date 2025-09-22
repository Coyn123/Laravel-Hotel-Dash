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
    $isSetupComplete = DB::table('configurations')->count() >= '1';

        if (! $isSetupComplete && !($request->is('setup') || $request->is('/'))) {
            return redirect()->route('setup');
        }

        if ($isSetupComplete && ($request->is('setup') || $request->is('/'))) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
