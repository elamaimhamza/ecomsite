<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function login(Request $request)
    {

        // ✅ Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required|string',
        ]);

        // ✅ Find the user by email
        $utilisateur = Utilisateur::where('email', $validated['email'])->first();

        if (!$utilisateur || !Hash::check($validated['mot_de_passe'], $utilisateur->mot_de_passe)) {
            return response()->json(['message' => 'Email ou Mot de pass incorrect'], 401);
        }

        // ✅ Generate new API token
        $token = Str::random(60);
        $utilisateur->update(['api_token' => hash('sha256', $token)]);

        // ✅ Return token and basic info
        return response()->json([
            'message' => 'Connexion réussie',
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_utilisateur' => $utilisateur->type_utilisateur,
                'api_token' => $token // client stores this
            ]
        ]);
    }

    public function verify(Request $request)
    {
        // Get the Authorization header
        $authorizationHeader = $request->header('Authorization');

        // Check if the header exists and starts with "Bearer "
        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(["message" => "Authorization header missing or invalid"], 401);
        }

        // Extract the token part (after "Bearer ")
        $token = substr($authorizationHeader, 7);

        // Hash the token
        $hashed = hash('sha256', $token);

        // Find the user by hashed token
        $utilisateur = Utilisateur::where('api_token', $hashed)->first();

        // If no user is found, return an error
        if (!$utilisateur) {
            return response()->json(["message" => "Token invalide"], 401);
        }

        // If valid, return user data
        return response()->json([
            'valid' => true,
            'message' => 'Token valide',
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_utilisateur' => $utilisateur->type_utilisateur,
            ]
        ]);
    }
}
