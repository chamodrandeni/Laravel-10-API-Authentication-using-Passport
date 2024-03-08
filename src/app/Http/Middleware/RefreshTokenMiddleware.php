<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $Refreshtoken = $request->header('Refreshtoken');
        
        if (Auth::user()->token()->expires_at->lt(now())) {
            // Access token has expired, regenerate it
            $request->user()->token()->revoke(); // Revoke the current access token
            $token = $request->user()->createToken('MyApp')->accessToken; // Generate a new access token
            $request->headers->set('Authorization', 'Bearer ' . $token); // Set the new access token in the request headers
        }

        return $next($request);
    }
}
