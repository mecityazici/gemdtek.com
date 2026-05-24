<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public const SUPPORTED = ['tr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        // 1) ?lang= query param wins
        $locale = $request->query('lang');

        // 2) Otherwise, honour Accept-Language only when it's actually set
        //    (avoid Symfony's fallback to the first array element, which
        //    silently flipped the default during tests)
        // Only ?lang= controls the API locale. We intentionally ignore
        // Accept-Language because consumers vary wildly (browsers, mobile
        // SDKs, server-to-server) and an opt-in query param is predictable.
        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::SUPPORTED[0]; // default 'tr'
        }

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
