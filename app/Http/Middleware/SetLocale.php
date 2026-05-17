<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check Accept-Language header (Standard for Mobile APIs)
        $locale = $request->header('Accept-Language');

        // 2. Fallback to cookie (Existing web logic)
        if (!$locale || !in_array($locale, ['en', 'ar'])) {
            $locale = $request->cookie('igate_lang');
        }

        // 3. Fallback to app default
        if (!$locale || !in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale');
        }
        
        App::setLocale($locale);

        return $next($request);
    }
}
