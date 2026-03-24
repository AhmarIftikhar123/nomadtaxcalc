<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceWwwHttps extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {

            $host = $request->getHost();

            // If non-www, redirect to www
            if (!str_starts_with($host, 'www.')) {
                $url = 'https://www.' . $host . $request->getRequestUri();
                return redirect()->to($url, 301);
            }

            // If HTTP, redirect to HTTPS
            if (!$request->secure()) {
                return redirect()->secure(
                    $request->getRequestUri(),
                    301
                );
            }
        }

        return $next($request);
    }
}
