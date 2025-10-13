<?php

namespace App\Http\Middleware;

use App\Models\Utilisateur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the "Authorization" header
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token manquant.'], 401);
        }
        // Extract the token part (after "Bearer ")
        $token = substr($token, 7);

        // Hash the token
        $hashed = hash('sha256', $token);

        // Find the user by hashed token
        $utilisateur = Utilisateur::where('api_token', $hashed)->first();

        // If no user is found, return an error
        if (!$utilisateur) {
            return response()->json(["message" => "Token invalide"], 401);
        }

        // Attach the user to the request
        $request->merge(['auth_user' => $utilisateur]);

        return $next($request);
    }
}
