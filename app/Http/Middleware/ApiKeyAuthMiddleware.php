<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthMiddleware
{
    private array $allowedApiKeys;

    public function __construct()
    {
        $this->allowedApiKeys = explode(',', config('auth.x_api_keys'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-API-KEY');
        if (!$providedKey || !in_array($providedKey, $this->allowedApiKeys)) {
            throw new AuthenticationException('Invalid or missing API key');
        }

        $request->attributes->set('authSource', self::class);

        return $next($request);
    }
}
