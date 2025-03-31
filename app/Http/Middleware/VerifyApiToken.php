<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret_key = config('env.tems_login_secret_key');
         // Get token from Authorization header
         $token = $request->header('Authorization');

         // Verify the token (in this example, checking against a static token from the .env file)
         if ($token !== 'Bearer ' . $secret_key) {
             // Return unauthorized response if token is invalid
             return response()->json(['message' => 'Unauthorized'], 401);
         }

        return $next($request);
    }

}
