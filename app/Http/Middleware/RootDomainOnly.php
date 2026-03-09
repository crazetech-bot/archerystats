<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RootDomainOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $host       = $request->getHost();
        $rootDomain = env('APP_ROOT_DOMAIN', 'sportdns.com');

        // Allow root domain and www only
        if ($host === $rootDomain || str_starts_with($host, 'www.')) {
            return $next($request);
        }

        // During local dev (no dots in host), allow through
        if (!str_contains($host, '.')) {
            return $next($request);
        }

        abort(404);
    }
}
