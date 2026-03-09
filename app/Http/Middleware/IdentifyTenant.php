<?php

namespace App\Http\Middleware;

use App\Models\Club;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host      = $request->getHost();
        $rootDomain = env('APP_ROOT_DOMAIN', 'sportdns.com');

        // Root domain or www — no tenant context (super admin area)
        if ($host === $rootDomain || str_starts_with($host, 'www.')) {
            return $next($request);
        }

        // Local dev — single domain, no subdomain context
        if (!str_contains($host, '.')) {
            return $next($request);
        }

        $subdomain = explode('.', $host)[0];

        // Also skip localhost-style subdomains during local dev
        if ($subdomain === 'localhost' || $subdomain === '127') {
            return $next($request);
        }

        $club = Club::where('slug', $subdomain)->where('active', true)->first();

        if (!$club) {
            abort(503, 'This club subdomain is not active or does not exist.');
        }

        app()->instance('currentClub', $club);
        View::share('currentClub', $club);

        return $next($request);
    }
}
