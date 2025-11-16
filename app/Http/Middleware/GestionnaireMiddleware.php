<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GestionnaireMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->get('auth_user'); // from middleware
        $type = $user->type_utilisateur;
        if ($type !=  "Gestionnaire") {
            return response()->json(["message" => "unauthorized"], 401);
        }

        return $next($request);
    }
}
