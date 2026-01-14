<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        // Deteksi HTTPS dari header proxy (ngrok, nginx, dll)
        $isSecure = $request->isSecure() || 
                    $request->header('X-Forwarded-Proto') === 'https';

        if (!$isSecure) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
