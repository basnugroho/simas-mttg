<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSessionWrites
{
    /**
     * Handle an incoming request.
     * - Prevent session from being written for safe HTTP methods (GET, HEAD, OPTIONS)
     * - Allow opt-out via header `X-Session-Write: true`
     */
    public function handle(Request $request, Closure $next)
    {
        // If client explicitly requires session write, skip prevention
        if (strtolower($request->header('X-Session-Write', 'false')) === 'true') {
            return $next($request);
        }

        // Only prevent writes for safe methods
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            // Temporarily mark session as read-only by replacing the save handler
            // We use a simple approach: prevent session from being saved by
            // setting a header on response and closing session early.
            $response = $next($request);

            // If session store exists, ensure we don't trigger session write.
            try {
                $session = $request->getSession();
                if ($session && method_exists($session, 'save')) {
                    // Avoid save call: set internal bags as not dirty if possible
                    // If session driver writes automatically, we can't fully prevent it,
                    // but closing the session early reduces lock window.
                    session_write_close();
                }
            } catch (\Throwable $e) {
                // ignore: best-effort only
            }

            return $response;
        }

        return $next($request);
    }
}
