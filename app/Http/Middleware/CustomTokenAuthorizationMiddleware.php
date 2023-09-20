<?php

namespace App\Http\Middleware;

use Closure;

class CustomTokenAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the 'Authorization' header exists
        if (!$request->hasHeader('Authorization')) {


            return response()->json(['error' => 'aaUnauthorized'], 401);
        }

        // Get the 'Authorization' header value
        $authorizationHeader = $request->header('Authorization');

        // Check if the header starts with 'Bearer '
        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['error' => 'bbUnauthorized'], 401);
        }

        // Extract the token without the 'Bearer ' prefix
        $token = substr($authorizationHeader, 7);

        // Perform token validation logic (e.g., validate against your authentication service)
        if (!$this->isValidToken($token)) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Token is valid, continue with the request
        return $next($request);
    }

    // Add your token validation logic here
    private function isValidToken($token)
    {
        // Implement your token validation logic here
        // Return true if the token is valid, false otherwise
        return true; // Change this to your validation logic
    }


}